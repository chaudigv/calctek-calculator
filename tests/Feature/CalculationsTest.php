<?php

namespace Tests\Feature;

use App\Models\Calculation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalculationsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_calculations(): void
    {
        Calculation::factory()->count(3)->create();

        $response = $this->getJson('/api/calculations');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    #[Test]
    public function it_lists_calculations_in_descending_order(): void
    {
        $first = Calculation::factory()->create(['created_at' => now()->subMinutes(2)]);
        $second = Calculation::factory()->create(['created_at' => now()->subMinute()]);
        $third = Calculation::factory()->create(['created_at' => now()]);

        $response = $this->getJson('/api/calculations');

        $response->assertOk();
        $ids = collect($response->json())->pluck('id')->toArray();
        $this->assertEquals([$third->id, $second->id, $first->id], $ids);
    }

    #[Test]
    public function it_can_create_simple_calculation(): void
    {
        $response = $this->postJson('/api/calculations', [
            'expression' => '5 + 3',
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['expression' => '5 + 3']);

        $this->assertDatabaseHas('calculations', [
            'expression' => '5 + 3',
        ]);
    }

    #[Test]
    public function it_can_create_complex_calculation(): void
    {
        $response = $this->postJson('/api/calculations', [
            'expression' => '(5 + 3) * 2',
        ]);

        $response->assertCreated();

        $result = (float) $response->json('result');
        $this->assertEquals(16, $result);
    }

    #[Test]
    public function it_handles_division_by_zero(): void
    {
        $response = $this->postJson('/api/calculations', [
            'expression' => '5 / 0',
        ]);

        $response->assertCreated()
            ->assertJsonPath('error.message', 'Division by zero');
    }

    #[Test]
    public function it_validates_expression_is_required(): void
    {
        $response = $this->postJson('/api/calculations', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['expression']);
    }

    #[Test]
    public function it_validates_expression_characters(): void
    {
        $response = $this->postJson('/api/calculations', [
            'expression' => '5 + 3; DROP TABLE calculations;',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['expression']);
    }

    #[Test]
    public function it_can_delete_calculation(): void
    {
        $calculation = Calculation::factory()->create();

        $response = $this->deleteJson("/api/calculations/{$calculation->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('calculations', ['id' => $calculation->id]);
    }

    #[Test]
    public function it_can_clear_all_calculations(): void
    {
        Calculation::factory()->count(5)->create();

        $response = $this->deleteJson('/api/calculations');

        $response->assertNoContent();
        $this->assertDatabaseCount('calculations', 0);
    }

    #[Test]
    public function it_handles_sqrt_function(): void
    {
        $response = $this->postJson('/api/calculations', [
            'expression' => 'sqrt(16)',
        ]);

        $response->assertCreated();
        $result = (float) $response->json('result');
        $this->assertEquals(4, $result);
    }

    #[Test]
    public function it_handles_power_operator(): void
    {
        $response = $this->postJson('/api/calculations', [
            'expression' => '2^3',
        ]);

        $response->assertCreated();
        $result = (float) $response->json('result');
        $this->assertEquals(8, $result);
    }
}
