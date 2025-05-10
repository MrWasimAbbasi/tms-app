<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Translation;
use App\Models\Locale;
use App\Models\Context;

class TranslationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_translation_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum'); // Change to 'api' if needed

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        $payload = [
            'key' => 'greeting',
            'content' => 'Hello World',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ];

        $response = $this->postJson('/api/translations', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['key' => 'greeting']);

        $this->assertDatabaseHas('translations', ['key' => 'greeting']);
    }

    public function test_store_translation_validation_error()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum'); // Use 'api' if you're using Passport

        $response = $this->postJson('/api/translations', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['key', 'content', 'locale_id', 'context_id']);
    }

    public function test_store_translation_key_must_be_unique()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum'); // Or 'api' for Passport

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        Translation::create([
            'key' => 'duplicate-key',
            'content' => 'Hello',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response = $this->postJson('/api/translations', [
            'key' => 'duplicate-key',
            'content' => 'Another Hello',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['key']);
    }

    public function test_search_returns_paginated_translations_without_filters()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/translations-search');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links']);
    }

    public function test_search_filters_by_key()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();
        Translation::factory()->create(['key' => 'welcome-message']);
        Translation::factory()->create(['key' => 'farewell-message']);

        $response = $this->getJson('/api/translations-search?key=welcome');

        $response->assertStatus(200)
            ->assertJsonFragment(['key' => 'welcome-message'])
            ->assertJsonMissing(['key' => 'farewell-message']);
    }

    public function test_search_filters_by_content()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $locale = Locale::factory()->create();
        $context = Context::factory()->create();
        Translation::factory()->create(['content' => 'Good morning']);
        Translation::factory()->create(['content' => 'Good night']);

        $response = $this->getJson('/api/translations-search?content=morning');

        $response->assertStatus(200)
            ->assertJsonFragment(['content' => 'Good morning'])
            ->assertJsonMissing(['content' => 'Good night']);
    }

    public function test_search_filters_by_context_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $welcomeContext = Context::factory()->create(['name' => 'Welcome']);
        $otherContext = Context::factory()->create(['name' => 'Goodbye']);
        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        Translation::factory()->create(['context_id' => $welcomeContext->id]);
        Translation::factory()->create(['context_id' => $otherContext->id]);

        $response = $this->getJson('/api/translations-search?context=Welc');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_search_respects_per_page_parameter()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        Translation::factory()->count(20)->create();

        $response = $this->getJson('/api/translations-search?per_page=5');

        $response->assertStatus(200);
        $this->assertEquals(5, count($response->json('data')));
    }


}
