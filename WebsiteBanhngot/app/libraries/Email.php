<?php
class Email {
    private $to;
    private $subject;
    private $message;
    private $headers;
    
    public function __construct() {
        $this->headers = "MIME-Version: 1.0" . "\r\n";
        $this->headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        $this->headers .= "From: " . $this->getFromEmail() . "\r\n";
    }
    
    // Set recipient
    public function setTo($email) {
        $this->to = $email;
        return $this;
    }
    
    // Set subject
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }
    
    // Set message
    public function setMessage($message) {
        $this->message = $this->wrapInTemplate($message);
        return $this;
    }
    
    // Send email
    public function send() {
        if (empty($this->to) || empty($this->subject) || empty($this->message)) {
            return false;
        }
        
        return mail($this->to, $this->subject, $this->message, $this->headers);
    }
    
    // Send order confirmation
    public function sendOrderConfirmation($order, $customerEmail, $customerName) {
        $this->setTo($customerEmail)
             ->setSubject("Xác nhận đơn hàng #{$order['order_code']} - Website Bánh Ngọt");
        
        $message = "
            <h2>Cảm ơn bạn đã đặt hàng!</h2>
            <p>Xin chào <strong>{$customerName}</strong>,</p>
            <p>Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
            
            <h3>Thông tin đơn hàng:</h3>
            <p><strong>Mã đơn hàng:</strong> {$order['order_code']}</p>
            <p><strong>Tổng tiền:</strong> " . FormatHelper::formatCurrency($order['final_price']) . "</p>
            <p><strong>Phương thức thanh toán:</strong> " . FormatHelper::formatPaymentMethod($order['payment_method']) . "</p>
            <p><strong>Thời gian đặt hàng:</strong> " . FormatHelper::formatDateTime($order['created_at']) . "</p>
            
            <p>Chúng tôi sẽ thông báo cho bạn khi đơn hàng sẵn sàng để lấy.</p>
            <p>Trân trọng,<br>Đội ngũ Website Bánh Ngọt</p>
        ";
        
        $this->setMessage($message);
        return $this->send();
    }
    
    // Send order status update
    public function sendOrderStatusUpdate($order, $customerEmail, $customerName) {
        $statusInfo = FormatHelper::formatOrderStatus($order['status']);
        
        $this->setTo($customerEmail)
             ->setSubject("Cập nhật trạng thái đơn hàng #{$order['order_code']}");
        
        $message = "
            <h2>Cập nhật đơn hàng #{$order['order_code']}</h2>
            <p>Xin chào <strong>{$customerName}</strong>,</p>
            <p>Đơn hàng của bạn đã được cập nhật trạng thái:</p>
            
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>
                <strong>Trạng thái mới:</strong> <span style='color: #007bff;'>{$statusInfo['text']}</span>
            </div>
            
            <p><strong>Mã đơn hàng:</strong> {$order['order_code']}</p>
            <p><strong>Tổng tiền:</strong> " . FormatHelper::formatCurrency($order['final_price']) . "</p>
            
            <p>Chúng tôi sẽ thông báo cho bạn khi có cập nhật tiếp theo.</p>
            <p>Trân trọng,<br>Đội ngũ Website Bánh Ngọt</p>
        ";
        
        $this->setMessage($message);
        return $this->send();
    }
    
    // Wrap message in email template
    private function wrapInTemplate($content) {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #f8f9fa; padding: 20px; text-align: center; }
                    .content { padding: 20px; background: white; }
                    .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 14px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Website Bánh Ngọt</h1>
                    </div>
                    <div class='content'>
                        {$content}
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Website Bánh Ngọt. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
    
    // Get from email address
    private function getFromEmail() {
        return "noreply@" . $_SERVER['HTTP_HOST'];
    }
}
?>