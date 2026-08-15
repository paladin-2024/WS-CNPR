<?php
namespace App\Models;

use App\Core\Database;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail($email)
    {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findById($id)
    {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function create($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->db->insert('users', $data);
    }

    public function getAll()
    {
        return $this->db->fetchAll(
            "SELECT id, nom, prenom, email, telephone, role, created_at FROM users ORDER BY created_at DESC"
        );
    }

    public function count()
    {
        return $this->db->fetch("SELECT COUNT(*) as total FROM users")['total'];
    }

    public function update($id, $data)
    {
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function delete($id)
    {
        return $this->db->delete('users', 'id = ?', [$id]);
    }
}
