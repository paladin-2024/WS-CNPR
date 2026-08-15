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
        return $this->db->fetch("SELECT * FROM utilisateurs WHERE email = ?", [$email]);
    }

    public function findById($id)
    {
        return $this->db->fetch("SELECT * FROM utilisateurs WHERE id = ?", [$id]);
    }

    public function create($data)
    {
        if (isset($data['password'])) {
            $data['mot_de_passe'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }
        return $this->db->insert('utilisateurs', $data);
    }

    public function getAll()
    {
        return $this->db->fetchAll(
            "SELECT id, nom, prenom, email, telephone, role, statut, date_creation FROM utilisateurs ORDER BY date_creation DESC"
        );
    }

    public function count()
    {
        return $this->db->fetch("SELECT COUNT(*) as total FROM utilisateurs")['total'];
    }

    public function update($id, $data)
    {
        return $this->db->update('utilisateurs', $data, 'id = ?', [$id]);
    }

    public function delete($id)
    {
        return $this->db->delete('utilisateurs', 'id = ?', [$id]);
    }
}
