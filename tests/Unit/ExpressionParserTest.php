<?php

namespace Tests\Unit;

use App\Exceptions\ExpressionParseException;
use App\Services\ExpressionParser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpressionParserTest extends TestCase
{
    private ExpressionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = app(ExpressionParser::class);
    }

    #[Test]
    public function it_handles_addition(): void
    {
        $this->assertEquals(8, $this->parser->parse('5 + 3'));
    }

    #[Test]
    public function it_handles_subtraction(): void
    {
        $this->assertEquals(2, $this->parser->parse('5 - 3'));
    }

    #[Test]
    public function it_handles_multiplication(): void
    {
        $this->assertEquals(15, $this->parser->parse('5 * 3'));
    }

    #[Test]
    public function it_handles_division(): void
    {
        $this->assertEquals(2, $this->parser->parse('6 / 3'));
    }

    #[Test]
    public function it_respects_operator_precedence(): void
    {
        $this->assertEquals(11, $this->parser->parse('5 + 3 * 2'));
    }

    #[Test]
    public function it_handles_parentheses(): void
    {
        $this->assertEquals(16, $this->parser->parse('(5 + 3) * 2'));
    }

    #[Test]
    public function it_handles_power(): void
    {
        $this->assertEquals(8, $this->parser->parse('2 ^ 3'));
    }

    #[Test]
    public function it_handles_square_root(): void
    {
        $this->assertEquals(3, $this->parser->parse('sqrt(9)'));
    }

    #[Test]
    public function it_handles_modulo(): void
    {
        $this->assertEquals(1, $this->parser->parse('10 % 3'));
    }

    #[Test]
    public function it_handles_decimal_numbers(): void
    {
        $this->assertEqualsWithDelta(7.5, $this->parser->parse('5.5 + 2'), 0.0001);
    }

    #[Test]
    public function it_handles_negative_numbers(): void
    {
        $this->assertEquals(-5, $this->parser->parse('-5'));
    }

    #[Test]
    public function it_handles_complex_expression(): void
    {
        $result = $this->parser->parse('sqrt((((9*9)/12)+(13-4))*2)^2');
        $this->assertEqualsWithDelta(31.5, $result, 0.01);
    }

    #[Test]
    public function it_handles_nested_parentheses(): void
    {
        $this->assertEquals(21, $this->parser->parse('((2 + 3) * (4 - 1)) + 6'));
    }

    #[Test]
    public function it_throws_on_division_by_zero(): void
    {
        $this->expectException(ExpressionParseException::class);
        $this->expectExceptionMessage('Division by zero');
        $this->parser->parse('5 / 0');
    }

    #[Test]
    public function it_throws_on_negative_square_root(): void
    {
        $this->expectException(ExpressionParseException::class);
        $this->expectExceptionMessage('Cannot calculate square root of negative number');
        $this->parser->parse('sqrt(-1)');
    }

    #[Test]
    public function it_throws_on_mismatched_parentheses(): void
    {
        $this->expectException(ExpressionParseException::class);
        $this->parser->parse('(5 + 3');
    }

    #[Test]
    public function it_throws_on_unknown_function(): void
    {
        $this->expectException(ExpressionParseException::class);
        $this->expectExceptionMessage('Unknown function');
        $this->parser->parse('unknown(5)');
    }

    #[Test]
    public function it_handles_multiple_operations(): void
    {
        $this->assertEquals(14, $this->parser->parse('2 + 3 * 4'));
        $this->assertEquals(20, $this->parser->parse('(2 + 3) * 4'));
    }

    #[Test]
    public function it_handles_right_associative_power(): void
    {
        $this->assertEquals(512, $this->parser->parse('2 ^ 3 ^ 2'));
    }
}
