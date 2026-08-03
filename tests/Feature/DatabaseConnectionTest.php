<?php

namespace Tests\Feature;

use App\DB;
use App\Models\User;
use Tests\IntegrationTestCase;

class DatabaseConnectionTest extends IntegrationTestCase
{
    public function test_database_connection_is_healthy(): void
    {
        $pdo = DB::connection();
        $this->assertInstanceOf(\PDO::class, $pdo);
        
        $stmt = $pdo->query("SELECT 1");
        $result = $stmt->fetchColumn();
        $this->assertEquals(1, $result);
    }

    public function test_can_query_users_table(): void
    {
        $userModel = new User();
        // Just verify we can fetch users or search for admin email
        $admin = $userModel->findByEmail('admin@shop.test');
        if ($admin) {
            $this->assertEquals('admin', $admin['role']);
            $this->assertEquals('admin@shop.test', $admin['email']);
        } else {
            $this->markTestSkipped('Admin user not found in database (database might be empty).');
        }
    }
}
