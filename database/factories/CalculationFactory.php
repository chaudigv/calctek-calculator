<?php

namespace Database\Factories;

use App\Models\Calculation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Calculation>
 */
class CalculationFactory extends Factory
{
    protected $model = Calculation::class;

    public function definition(): array
    {
        $a = $this->faker->numberBetween(1, 100);
        $b = $this->faker->numberBetween(1, 100);
        $operator = $this->faker->randomElement(['+', '-', '*', '/']);

        return [
            'expression' => "{$a} {$operator} {$b}",
            'result' => match ($operator) {
                '+' => $a + $b,
                '-' => $a - $b,
                '*' => $a * $b,
                '/' => $a / $b,
            },
        ];
    }

    public function withError(string $message = 'Division by zero'): self
    {
        return $this->state(fn () => [
            'expression' => '1 / 0',
            'result' => null,
        ])->afterCreating(function (Calculation $calculation) use ($message) {
            $calculation->error()->create([
                'message' => $message,
            ]);
        });
    }
}
