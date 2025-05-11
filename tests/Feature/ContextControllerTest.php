<?php

namespace Tests\Feature;

use App\Models\Context;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContextControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        return $token;
    }

    public function test_can_get_all_contexts()
    {
        $token = $this->authenticatedUser();

        Context::factory()->count(3)->create();

        $response = $this->getJson('/api/contexts', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_can_create_context()
    {
        $token = $this->authenticatedUser();

        $data = [
            'name' => 'default',
        ];

        $response = $this->postJson('/api/contexts', $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'name' => 'default',
        ]);
    }

    public function test_cannot_create_context_without_name()
    {
        $token = $this->authenticatedUser();

        $data = [];

        $response = $this->postJson('/api/contexts', $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_can_get_single_context()
    {
        $token = $this->authenticatedUser();

        $context = Context::factory()->create();

        $response = $this->getJson("/api/contexts/{$context->id}", [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $context->id,
            'name' => $context->name,
        ]);
    }

    public function test_cannot_get_non_existing_context()
    {
        $token = $this->authenticatedUser();

        $response = $this->getJson("/api/contexts/9999", [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(404);
    }

    public function test_can_update_context()
    {
        $token = $this->authenticatedUser();

        $context = Context::factory()->create();

        $data = [
            'name' => 'updated-context',
        ];

        $response = $this->putJson("/api/contexts/{$context->id}", $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'updated-context',
        ]);
    }

    public function test_cannot_update_context_without_name()
    {
        $token = $this->authenticatedUser();

        $context = Context::factory()->create();

        $data = [];

        $response = $this->putJson("/api/contexts/{$context->id}", $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_cannot_update_non_existing_context()
    {
        $token = $this->authenticatedUser();

        $data = ['name' => 'updated-context'];

        $response = $this->putJson("/api/contexts/9999", $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(404);
    }

    public function test_can_delete_context()
    {
        $token = $this->authenticatedUser();

        $context = Context::factory()->create();

        $response = $this->deleteJson("/api/contexts/{$context->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(204);
    }

    public function test_cannot_delete_non_existing_context()
    {
        $token = $this->authenticatedUser();

        $response = $this->deleteJson("/api/contexts/9999", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(404);
    }
}
