<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountAndPricingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_api_token(): void
    {
        $response = $this->postJson('/api/account/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'User registered.')
            ->assertJsonPath('data.user.email', 'budi@example.com');

        $this->assertIsString($response->json('data.token'));
        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
        ]);
        $this->assertDatabaseCount('api_tokens', 1);
    }

    public function test_user_can_login_and_receive_api_token(): void
    {
        User::query()->create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/account/login', [
            'email' => 'budi@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonPath('data.user.email', 'budi@example.com');

        $this->assertIsString($response->json('data.token'));
        $this->assertDatabaseCount('api_tokens', 1);
    }

    public function test_manage_routes_require_bearer_token(): void
    {
        $this->getJson('/api/manage/prices/categories')
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthorized.',
            ]);
    }

    public function test_authenticated_user_can_manage_price_categories_and_prices(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $token = $user->createApiToken()['plain_text_token'];
        $headers = [
            'Authorization' => 'Bearer '.$token,
        ];

        $categoryResponse = $this->withHeaders($headers)->postJson('/api/manage/prices/categories', [
            'name' => 'Private Class',
            'description' => 'Kategori untuk kelas privat',
        ]);

        $categoryResponse
            ->assertCreated()
            ->assertJsonPath('data.name', 'Private Class')
            ->assertJsonPath('data.prices_count', 0);

        $categoryId = $categoryResponse->json('data.id');

        $this->withHeaders($headers)->getJson('/api/manage/prices/categories')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $priceResponse = $this->withHeaders($headers)->postJson('/api/manage/prices', [
            'category_id' => $categoryId,
            'name' => 'Paket 1 Sesi',
            'description' => 'Satu kali pertemuan',
            'price' => 150000,
        ]);

        $priceResponse
            ->assertCreated()
            ->assertJsonPath('data.name', 'Paket 1 Sesi')
            ->assertJsonPath('data.category.id', $categoryId)
            ->assertJsonPath('data.price', '150000.00');

        $priceId = $priceResponse->json('data.id');

        $this->withHeaders($headers)->patchJson('/api/manage/prices/'.$priceId, [
            'price' => 200000,
        ])
            ->assertOk()
            ->assertJsonPath('data.price', '200000.00');

        $this->withHeaders($headers)->deleteJson('/api/manage/prices/'.$priceId)
            ->assertOk()
            ->assertJson([
                'message' => 'Price deleted.',
            ]);

        $this->withHeaders($headers)->deleteJson('/api/manage/prices/categories/'.$categoryId)
            ->assertOk()
            ->assertJson([
                'message' => 'Price category deleted.',
            ]);
    }
}
