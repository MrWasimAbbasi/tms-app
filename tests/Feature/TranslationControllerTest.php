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

    public function test_search_translation_model_not_found_exception_handled()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/translations/search?throw_exception=1');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No query results for model [App\\Models\\Translation] search',
            ]);
    }

    public function test_destroy_translation_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        $translation = Translation::create([
            'key' => 'delete-key',
            'content' => 'To be deleted',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response = $this->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id,
        ]);
    }

    public function test_show_translation_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        $translation = Translation::create([
            'key' => 'test-key',
            'content' => 'Test content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response = $this->getJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $translation->id,
                'key' => 'test-key',
                'content' => 'Test content',
                'locale_id' => $locale->id,
                'context_id' => $context->id,
            ]);
    }

    public function test_index_returns_paginated_translations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        Translation::factory()->create([
            'key' => 'first-key',
            'content' => 'First content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);
        Translation::factory()->create([
            'key' => 'second-key',
            'content' => 'Second content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response = $this->getJson('/api/translations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'key',
                        'content',
                        'locale_id',
                        'context_id',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'links' => [],
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_update_translation_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        $translation = Translation::create([
            'key' => 'old-key',
            'content' => 'Old content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $updatedData = [
            'key' => 'updated-key',
            'content' => 'Updated content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ];

        $response = $this->putJson("/api/translations/{$translation->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $translation->id,
                'key' => 'updated-key',
                'content' => 'Updated content',
                'locale_id' => $locale->id,
                'context_id' => $context->id,
            ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'key' => 'updated-key',
            'content' => 'Updated content',
        ]);
    }

    public function test_update_translation_validation_error()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        $translation = Translation::create([
            'key' => 'old-key',
            'content' => 'Old content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response = $this->putJson("/api/translations/{$translation->id}", [
            'content' => 'Updated content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['key']);
    }

    public function test_update_translation_key_must_be_unique()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $locale = Locale::factory()->create();
        $context = Context::factory()->create();

        $translation = Translation::create([
            'key' => 'old-key',
            'content' => 'Old content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        Translation::create([
            'key' => 'duplicate-key',
            'content' => 'Some content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response = $this->putJson("/api/translations/{$translation->id}", [
            'key' => 'duplicate-key',
            'content' => 'Updated content',
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['key']);
    }


}
