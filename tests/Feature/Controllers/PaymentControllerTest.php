<?php

namespace Tests\Feature\Controllers;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    public function test_create_payment_without_amount()
    {
        $data = [
            'username' => $this->faker->name,
            'phone' => $this->faker->numerify('09#########'),
            'description' => $this->faker->sentence,
        ];
        $response = $this->postJson(route('payment.create.v1'), $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'username',
                'phone',
                'description',
                'created_at',
                'updated_at',
            ]
        ]);
        $response->assertJson([
            'message' => "success",
            'data' => [
                'username' => $data['username'],
                'phone' => $data['phone'],
                'description' => $data['description'],
            ]
        ]);
    }

    public function test_create_payment_with_zarinpal_transaction_v1(): void
    {
        $this->createTestGateways('zarinpal');
    }

    public function test_create_payment_with_shepa_transaction_v1(): void
    {
        $this->createTestGateways('shepa');
    }

    public function test_create_payment_with_zibal_transaction_v1(): void
    {
        $this->createTestGateways('zibal');
    }

    private function createTestGateways($gateway): void
    {
        $data = [
            'gateway' => $gateway,
            'username' => $this->faker->name,
            'phone' => $this->faker->numerify('09#########'),
            'description' => $this->faker->sentence,
            'amount' => $this->faker->numberBetween(1, 1000) * 1000
        ];

        $response = $this->postJson(route('payment.create.v1'), $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'username',
                'phone',
                'description',
                'amount',
                'created_at',
                'updated_at',
                'transactions' => [
                    [
                        'id',
                        'payment_id',
                        'amount',
                        'gateway',
                        'token',
                        'link',
                        'status',
                        'detail',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ]);
        $response->assertJson([
            'message' => "success",
            'data' => [
                'username' => $data['username'],
                'phone' => $data['phone'],
                'description' => $data['description'],
                'amount' => $data['amount'],
                'transactions' => [
                    [
                        "amount" => max_amount($data['amount']),
                        "gateway" => $gateway,
                        "status" => "pending",
                    ]
                ]
            ]
        ]);
    }
}
