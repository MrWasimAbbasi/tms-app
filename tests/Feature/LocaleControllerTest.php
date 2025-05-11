<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;
        return $token;
    }

    public function test_can_get_all_locales()
    {
        $token = $this->authenticatedUser();

        Locale::factory()->count(3)->create();

        $response = $this->getJson('/api/locales', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_can_create_locale()
    {
        $token = $this->authenticatedUser();

        $data = [
            'name' => 'en',
            'description' => 'English',
        ];

        $response = $this->postJson('/api/locales', $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'name' => 'en',
            'description' => 'English',
        ]);
    }

    public function test_cannot_create_locale_without_name()
    {
        $token = $this->authenticatedUser();

        $data = ['description' => 'English'];

        $response = $this->postJson('/api/locales', $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_can_get_single_locale()
    {
        $token = $this->authenticatedUser();

        $locale = Locale::factory()->create();

        $response = $this->getJson("/api/locales/{$locale->id}", [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $locale->id,
            'name' => $locale->name,
            'description' => $locale->description,
        ]);
    }

    public function test_cannot_get_non_existing_locale()
    {
        $token = $this->authenticatedUser();

        $response = $this->getJson("/api/locales/9999", [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(404);
    }

    public function test_can_update_locale()
    {
        $token = $this->authenticatedUser();

        $locale = Locale::factory()->create();

        $data = [
            'name' => 'fr',
            'description' => 'French',
        ];

        $response = $this->putJson("/api/locales/{$locale->id}", $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'fr',
            'description' => 'French',
        ]);
    }

    public function test_cannot_update_locale_without_name()
    {
        $token = $this->authenticatedUser();

        $locale = Locale::factory()->create();

        $data = ['description' => 'French'];

        $response = $this->putJson("/api/locales/{$locale->id}", $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_cannot_update_non_existing_locale()
    {
        $token = $this->authenticatedUser();

        $data = ['name' => 'fr', 'description' => 'French'];

        $response = $this->putJson("/api/locales/9999", $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(404);
    }

    public function test_can_delete_locale()
    {
        $token = $this->authenticatedUser();

        $locale = Locale::factory()->create();

        $response = $this->deleteJson("/api/locales/{$locale->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(204);
    }

    public function test_cannot_delete_non_existing_locale()
    {
        $token = $this->authenticatedUser();

        $response = $this->deleteJson("/api/locales/9999", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(404);
    }
}
