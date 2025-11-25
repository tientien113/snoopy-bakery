<?php
/**
 * Role Model
 */

class Role extends Model {
    protected $table = 'roles';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function getRoleName($roleId) {
        $this->db->query("SELECT name FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $roleId);
        $result = $this->db->result();
        return $result ? $result['name'] : '';
    }
}
?>