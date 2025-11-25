<?php
/**
 * Payment Controller - Xử lý thanh toán
 */

class PaymentController extends Controller {
    private $paymentModel;
    private $orderModel;

    public function __construct() {
        parent::__construct();
        $this->paymentModel = new Payment();
        $this->orderModel = new Order();
    }

    public function process($orderId) {
        $this->requireLogin();

        $order = $this->orderModel->find($orderId);
        if (!$order || $order['user_id'] != $_SESSION[SESSION_USER_ID]) {
            $this->setFlash('Đơn hàng không tồn tại', FLASH_ERROR);
            $this->redirect('orders/history');
        }

        if ($order['status'] != ORDER_PENDING) {
            $this->setFlash('Đơn hàng không thể thanh toán', FLASH_ERROR);
            $this->redirect('orders/detail/' . $orderId);
        }

        $payment = $this->paymentModel->where(['order_id' => $orderId])[0] ?? null;
        if (!$payment) {
            $this->setFlash('Thông tin thanh toán không tồn tại', FLASH_ERROR);
            $this->redirect('orders/detail/' . $orderId);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePayment($order, $payment);
        }

        $data = [
            'title' => 'Thanh toán đơn hàng - ' . APP_NAME,
            'order' => $order,
            'payment' => $payment,
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('payments/process', $data);
    }

    private function handlePayment($order, $payment) {
        $this->validateCSRF();

        $paymentMethod = $_POST['payment_method'] ?? $payment['method'];

        try {
            $this->db->beginTransaction();

            // Update payment method if changed
            if ($paymentMethod != $payment['method']) {
                $this->paymentModel->update($payment['id'], ['method' => $paymentMethod]);
            }

            // Handle different payment methods
            switch ($paymentMethod) {
                case PAYMENT_CASH_PICKUP:
                    // For cash on pickup, just update status to confirmed
                    $this->orderModel->update($order['id'], ['status' => ORDER_CONFIRMED]);
                    $this->paymentModel->update($payment['id'], ['status' => PAYMENT_PENDING]);
                    break;

                case PAYMENT_BANK_TRANSFER:
                    // Simulate bank transfer - in real app, integrate with bank API
                    $this->processBankTransfer($order, $payment);
                    break;

                case PAYMENT_MOMO:
                case PAYMENT_VNPAY:
                    // Simulate e-wallet payment - in real app, integrate with payment gateway
                    $this->processEWallet($order, $payment, $paymentMethod);
                    break;
            }

            // Log status change
            $this->logOrderStatusChange($order['id'], $order['status'], ORDER_CONFIRMED, 'Khách hàng đã thanh toán');

            $this->db->commit();

            $this->setFlash('Thanh toán thành công! Đơn hàng đã được xác nhận.', FLASH_SUCCESS);
            $this->redirect('orders/detail/' . $order['id']);

        } catch (Exception $e) {
            $this->db->rollback();
            $this->setFlash('Có lỗi xảy ra khi thanh toán: ' . $e->getMessage(), FLASH_ERROR);
        }
    }

    private function processBankTransfer($order, $payment) {
        // Simulate bank transfer processing
        // In real application, integrate with bank API
        
        $providerReference = 'BANK' . time() . mt_rand(1000, 9999);
        
        $this->paymentModel->update($payment['id'], [
            'status' => PAYMENT_PAID,
            'paid_at' => date('Y-m-d H:i:s'),
            'provider_reference' => $providerReference
        ]);

        $this->orderModel->update($order['id'], ['status' => ORDER_CONFIRMED]);
    }

    private function processEWallet($order, $payment, $method) {
        // Simulate e-wallet payment processing
        // In real application, integrate with MoMo/VNPay API
        
        $providerReference = strtoupper($method) . time() . mt_rand(1000, 9999);
        
        $this->paymentModel->update($payment['id'], [
            'status' => PAYMENT_PAID,
            'paid_at' => date('Y-m-d H:i:s'),
            'provider_reference' => $providerReference
        ]);

        $this->orderModel->update($order['id'], ['status' => ORDER_CONFIRMED]);
    }

    public function success($orderId) {
        $this->requireLogin();

        $order = $this->orderModel->find($orderId);
        if (!$order || $order['user_id'] != $_SESSION[SESSION_USER_ID]) {
            $this->setFlash('Đơn hàng không tồn tại', FLASH_ERROR);
            $this->redirect('orders/history');
        }

        $payment = $this->paymentModel->where(['order_id' => $orderId])[0] ?? null;

        $data = [
            'title' => 'Thanh toán thành công - ' . APP_NAME,
            'order' => $order,
            'payment' => $payment
        ];

        $this->view('payments/success', $data);
    }

    public function failed($orderId) {
        $this->requireLogin();

        $order = $this->orderModel->find($orderId);
        if (!$order || $order['user_id'] != $_SESSION[SESSION_USER_ID]) {
            $this->setFlash('Đơn hàng không tồn tại', FLASH_ERROR);
            $this->redirect('orders/history');
        }

        $data = [
            'title' => 'Thanh toán thất bại - ' . APP_NAME,
            'order' => $order
        ];

        $this->view('payments/failed', $data);
    }

    public function callback($gateway) {
        // Handle payment gateway callbacks
        // This would be called by payment providers like MoMo, VNPay
        
        $orderId = $_GET['order_id'] ?? null;
        $status = $_GET['status'] ?? 'failed';
        $transactionId = $_GET['transaction_id'] ?? null;

        if (!$orderId) {
            http_response_code(400);
            echo 'Missing order ID';
            exit;
        }

        $order = $this->orderModel->find($orderId);
        if (!$order) {
            http_response_code(404);
            echo 'Order not found';
            exit;
        }

        $payment = $this->paymentModel->where(['order_id' => $orderId])[0] ?? null;
        if (!$payment) {
            http_response_code(404);
            echo 'Payment not found';
            exit;
        }

        try {
            $this->db->beginTransaction();

            if ($status === 'success') {
                $this->paymentModel->update($payment['id'], [
                    'status' => PAYMENT_PAID,
                    'paid_at' => date('Y-m-d H:i:s'),
                    'provider_reference' => $transactionId
                ]);

                $this->orderModel->update($order['id'], ['status' => ORDER_CONFIRMED]);
                $this->logOrderStatusChange($order['id'], $order['status'], ORDER_CONFIRMED, 'Thanh toán thành công qua ' . $gateway);
            } else {
                $this->paymentModel->update($payment['id'], [
                    'status' => PAYMENT_FAILED
                ]);
            }

            $this->db->commit();

            // Redirect to appropriate page
            if ($status === 'success') {
                header('Location: ' . BASE_URL . '/payment/success/' . $orderId);
            } else {
                header('Location: ' . BASE_URL . '/payment/failed/' . $orderId);
            }
            exit;

        } catch (Exception $e) {
            $this->db->rollback();
            http_response_code(500);
            echo 'Payment processing failed';
        }
    }

    private function logOrderStatusChange($orderId, $oldStatus, $newStatus, $note = '') {
        $historyData = [
            'order_id' => $orderId,
            'changed_by_user_id' => $_SESSION[SESSION_USER_ID] ?? null,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note
        ];
        (new OrderStatusHistory())->create($historyData);
    }
}
?>