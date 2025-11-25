<?php
/**
 * User Model
 */

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function findByEmail($email) {
        $this->db->query("SELECT * FROM {$this->table} WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->result();
    }

    public function getCustomers() {
        $this->db->query("SELECT * FROM {$this->table} WHERE role_id = :role_id ORDER BY created_at DESC");
        $this->db->bind(':role_id', ROLE_CUSTOMER);
        return $this->db->results();
    }

    public function getStaff() {
        $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE role_id IN (:staff, :manager, :admin) 
            ORDER BY created_at DESC
        ");
        $this->db->bind(':staff', ROLE_STAFF);
        $this->db->bind(':manager', ROLE_MANAGER);
        $this->db->bind(':admin', ROLE_ADMIN);
        return $this->db->results();
    }

    public function updateLastLogin($userId) {
        $this->db->query("UPDATE {$this->table} SET updated_at = NOW() WHERE id = :id");
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }
}
?>