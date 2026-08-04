<?php
namespace Tests;

use App\Helpers\Database;
use App\Helpers\Session;
use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    protected \PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = Database::getConnection();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        parent::tearDown();
    }

    protected function setSessionUser(int $userId): void
    {
        Session::start();
        $stmt = $this->pdo->prepare("SELECT u.*, GROUP_CONCAT(DISTINCT r.name) as role_names
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.id = ?
            GROUP BY u.id");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) return;

        Session::setAuthenticated($user['id'], $user['email'], $user['first_name'] . ' ' . $user['last_name']);
        Session::set('user_roles', explode(',', $user['role_names']));
        Session::set('user_id', $user['id']);
        Session::set('organization_id', $user['organization_id']);
        Session::set('daira_id', $user['daira_id']);

        // Load permissions
        $permStmt = $this->pdo->prepare("SELECT DISTINCT p.name
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            JOIN user_roles ur ON rp.role_id = ur.role_id
            WHERE ur.user_id = ?");
        $permStmt->execute([$userId]);
        Session::set('user_permissions', $permStmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    protected function tearDownSession(): void
    {
        $_SESSION = [];
    }
}
