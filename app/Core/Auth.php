<?php
namespace App\Core;

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id()
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function login($user)
    {
        // Never keep the bcrypt hash in the session - $_SESSION['user'] is read
        // all over the app (views, Auth::user(), role checks) and there's no
        // reason any of that needs the password hash sitting in memory/session
        // storage. Callers that need the hash (login's own password_verify(),
        // ProfileController's password-change flow) already fetch it fresh
        // from the DB rather than through Auth::user().
        unset($user['mot_de_passe']);
        $_SESSION['user'] = $user;
    }

    public static function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function hasRole($roles)
    {
        $user = self::user();
        if (!$user) return false;
        if (is_string($roles)) $roles = [$roles];
        return in_array($user['role'], $roles);
    }

    public static function hasPermission($permission)
    {
        $permissions = [
            'admin'                => ['all'],
            'minister_admin'       => ['manage_agents', 'manage_conducteurs', 'manage_vehicules', 'view_stats', 'manage_taxes'],
            'agent'                => ['manage_conducteurs', 'manage_vehicules', 'view_stats', 'manage_taxes'],
            'inspecteur'           => ['inspect', 'verify', 'report'],
            'gestionnaire_parking' => ['manage_parking', 'collect_taxes'],
            'transporteur'         => ['manage_vehicules', 'view_own'],
            'conducteur'           => ['view_own', 'update_profile'],
            'citoyen'              => ['signal', 'view_public'],
            'imprimeur'            => ['manage_brevets', 'download_brevets'],
            'receptionnaire'       => ['manage_brevets', 'confirmer_brevets'],
            'operateur_saisie'     => ['manage_conducteurs'],
            'validateur'           => ['manage_taxes', 'view_stats'],
            'receveur'             => ['manage_brevets', 'confirmer_brevets'],
            'instructeur'          => ['inspect', 'verify'],
        ];

        $user = self::user();
        if (!$user) return false;

        $userPerms = $permissions[$user['role']] ?? [];
        return in_array('all', $userPerms) || in_array($permission, $userPerms);
    }
}
