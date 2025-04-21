<?php

namespace Tests\Feature\Controllers\client\v1;

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

    public function test_show_payment_without_amount(): void
    {
        $data = [
            'username' => $this->faker->name,
            'phone' => $this->faker->numerify('09#########'),
            'description' => $this->faker->sentence,
        ];
        $response = $this->postJson(route('payment.create.v1'), $data);
        $response->assertStatus(200);

        $paymentData = $response->json('data');

        $this->assertNotEmpty($paymentData['payment_link']);
        $this->assertStringContainsString(route('payments.show.v1', ['payment' => $paymentData['id']]), $paymentData['payment_link']);

        $response = $this->getJson($paymentData['payment_link']);

        $response->assertStatus(200);
        $response->assertJson([
            "data" => $paymentData
        ]);
    }


    public function test_show_payment_with_amount(): void
    {
        $data = [
            'username' => $this->faker->name,
            'phone' => $this->faker->numerify('09#########'),
            'description' => $this->faker->sentence,
        ];

        $response = $this->postJson(route('payment.create.v1'), $data);
        $response->assertStatus(200);

        $paymentData = $response->json('data');
        $this->assertNotEmpty($paymentData['payment_link']);
        $this->assertStringContainsString(route('payments.show.v1', ['payment' => $paymentData['id']]), $paymentData['payment_link']);

        $response = $this->getJson($paymentData['payment_link'] . "?amount=" . $this->faker->numberBetween(1, 1000) * 1000);

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
                'total_paid',
                'payment_link',
                'transactions' => [
                    '*' => [
                        'id',
                        'payment_id',
                        'amount',
                        'gateway',
                        'token',
                        'link',
                        'status',
                        'tracking_code',
                        'detail',
                        'created_at',
                        'updated_at'
                    ]
                ],
            ],
        ]);

        $transaction = $response->json('data')['transactions'][0];
        $this->assertEquals('pending', $transaction['status']);
        $this->assertNotEmpty($transaction['link']);
        $this->assertNotEmpty($transaction['token']);
    }
}
