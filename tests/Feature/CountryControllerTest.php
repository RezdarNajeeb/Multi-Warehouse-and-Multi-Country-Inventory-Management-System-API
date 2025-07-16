<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CountryControllerTest extends TestCase
{
  use RefreshDatabase;

  protected User $user;

  protected function setUp(): void
  {
    parent::setUp();
    $this->user = User::factory()->create();
  }

  protected function headers(User $user = null): array
  {
    $user = $user ?: $this->user;
    $token = JWTAuth::fromUser($user);
    return ['Authorization' => 'Bearer ' . $token];
  }

  public function test_it_can_list_countries()
  {
    Country::factory()->count(3)->create();

    $response = $this->getJson('/api/countries', $this->headers());

    $response->assertStatus(200)
      ->assertJsonStructure([
        'status',
        'message',
        'data' => [
          '*' => ['id', 'name', 'code', 'createdAt', 'updatedAt']
        ],
      ])
      ->assertJsonCount(3, 'data');
  }

  public function test_it_can_create_a_country()
  {
    $data = [
      'name' => 'Test Country',
      'code' => 'TC',
    ];

    $response = $this->postJson('/api/countries', $data, $this->headers());

    $response->assertStatus(201)
      ->assertJsonFragment($data);

    $this->assertDatabaseHas('countries', $data);
  }

  public function test_it_returns_validation_errors_when_creating_a_country()
  {
    $data = [
      'name' => 'T',
      'code' => 'INVALID-CODE',
    ];

    $response = $this->postJson('/api/countries', $data, $this->headers());

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['name', 'code']);
  }

  public function test_it_can_show_a_country()
  {
    $country = Country::factory()->create();

    $response = $this->getJson('/api/countries/' . $country->id, $this->headers());

    $response->assertStatus(200)
      ->assertJsonFragment([
        'id' => $country->id,
        'name' => $country->name,
      ]);
  }

  public function test_it_can_update_a_country()
  {
    $country = Country::factory()->create();

    $data = [
      'name' => 'Updated Country Name',
    ];

    $response = $this->putJson('/api/countries/' . $country->id, $data, $this->headers());

    $response->assertStatus(200)
      ->assertJsonFragment($data);

    $this->assertDatabaseHas('countries', ['id' => $country->id, 'name' => 'Updated Country Name']);
  }

  public function test_it_can_delete_a_country()
  {
    $country = Country::factory()->create();

    $response = $this->deleteJson('/api/countries/' . $country->id, [], $this->headers());

    $response->assertStatus(204);

    $this->assertDatabaseMissing('countries', ['id' => $country->id]);
  }

  public function test_unauthenticated_user_cannot_access_countries_endpoints()
  {
    $this->getJson('/api/countries')->assertStatus(401);
    $this->postJson('/api/countries')->assertStatus(401);

    $country = Country::factory()->create();
    $this->putJson('/api/countries/' . $country->id)->assertStatus(401);
    $this->deleteJson('/api/countries/' . $country->id)->assertStatus(401);
    $this->getJson('/api/countries/' . $country->id)->assertStatus(401);
  }
}
