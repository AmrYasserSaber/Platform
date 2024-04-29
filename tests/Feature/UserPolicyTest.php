<?php

namespace Tests\Feature;



use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;


class UserPolicyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testViewAny()
    {
        // Create a user with the 'admin' role
        $adminUser = User::factory()->create(['role' => 'admin']);

        // Assert that admin user can view any models
        $this->assertTrue((new UserPolicy)->viewAny($adminUser)->allowed());

        // Create a regular user
        $regularUser = User::factory()->create();

        // Assert that regular user cannot view any models
        $this->assertFalse((new UserPolicy)->viewAny($regularUser)->allowed());
    }
}
