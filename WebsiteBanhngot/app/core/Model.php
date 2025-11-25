<?php
/**
 * Base Model Class
 */

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all() {
        $this->db->query("SELECT * FROM {$this->table} WHERE is_active = 1");
        return $this->db->results();
    }

    public function find($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id);
        return $this->db->result();
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $this->db->query("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        return $this->db->execute();
    }

    public function update($id, $data) {
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "{$key} = :{$key}, ";
        }
        $set = rtrim($set, ', ');
        
        $this->db->query("UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id);
        
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("UPDATE {$this->table} SET is_active = 0 WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function hardDelete($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function where($conditions = []) {
        $where = '1=1';
        foreach ($conditions as $key => $value) {
            $where .= " AND {$key} = :{$key}";
        }
        
        $this->db->query("SELECT * FROM {$this->table} WHERE {$where}");
        
        foreach ($conditions as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        return $this->db->results();
    }

    public function count() {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = 1");
        return $this->db->result()['count'];
    }
}
?>