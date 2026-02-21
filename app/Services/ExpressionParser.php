<?php

namespace App\Services;

use App\DTOs\Token;
use App\Enums\TokenType;
use App\Exceptions\ExpressionParseException;
use App\Services\Functions\FunctionRegistry;
use App\Services\Operators\OperatorRegistry;
use Illuminate\Support\Str;

/**
 * Recursive descent parser for mathematical expressions.
 *
 * Operator Precedence (lowest to highest):
 * ┌───────────┬────────────┬───────────────┐
 * │ Level     │ Operators  │ Associativity │
 * ├───────────┼────────────┼───────────────┤
 * │ 1 (low)   │ +  -       │ left          │
 * │ 2         │ *  /  %    │ left          │
 * │ 3         │ ^          │ right         │
 * │ 4         │ unary -/+  │ right         │
 * │ 5 (high)  │ () funcs   │ n/a           │
 * └───────────┴────────────┴───────────────┘
 *
 * Call hierarchy (each level handles one precedence tier):
 *   parseExpression → parseAddition → parseMultiplication → parsePower → parseUnary → parseFactor
 */
class ExpressionParser
{
    /** @var array<int, Token> */
    private array $tokens = [];

    private int $position = 0;

    public function __construct(
        private OperatorRegistry $operators,
        private FunctionRegistry $functions,
    ) {}


    public function parse(string $expression): float
    {
        $expression = Str::of($expression)->trim()->lower()->replace(' ', '')->toString();

        $this->tokens = $this->tokenize($expression);
        $this->position = 0;

        $result = $this->parseExpression();

        if ($this->position < count($this->tokens)) {
            throw ExpressionParseException::invalidSyntax('Unexpected token');
        }

        return $result;
    }

    /** @return array<int, Token> */
    private function tokenize(string $expression): array
    {
        $tokens = [];
        $length = strlen($expression);
        $i = 0;
        $operatorSymbols = $this->operators->symbols();

        while ($i < $length) {
            $char = $expression[$i];

            if (ctype_digit($char) || $char === '.') {
                $number = '';

                while ($i < $length && (ctype_digit($expression[$i]) || $expression[$i] === '.')) {
                    $number .= $expression[$i];
                    $i++;
                }

                if (substr_count($number, '.') > 1) {
                    throw ExpressionParseException::invalidSyntax("Invalid number: {$number}");
                }

                $tokens[] = new Token(TokenType::Number, (float) $number);
                continue;
            }

            if (ctype_alpha($char)) {
                $func = '';

                while ($i < $length && ctype_alpha($expression[$i])) {
                    $func .= $expression[$i];
                    $i++;
                }

                if (! $this->functions->has($func)) {
                    throw ExpressionParseException::unknownFunction($func);
                }

                $tokens[] = new Token(TokenType::Function, $func);
                continue;
            }

            if ($char === '(') {
                $tokens[] = new Token(TokenType::LeftParen, '(');
                $i++;
                continue;
            }

            if ($char === ')') {
                $tokens[] = new Token(TokenType::RightParen, ')');
                $i++;
                continue;
            }

            if (in_array($char, $operatorSymbols)) {
                $tokens[] = new Token(TokenType::Operator, $char);
                $i++;
                continue;
            }

            throw ExpressionParseException::invalidSyntax("Unexpected character: {$char}");
        }

        return $tokens;
    }

    private function current(): ?Token
    {
        return $this->tokens[$this->position] ?? null;
    }

    private function advance(): void
    {
        $this->position++;
    }

    private function parseExpression(): float
    {
        return $this->parseAddition();
    }

    private function parseAddition(): float
    {
        $left = $this->parseMultiplication();
        $precedence1Operators = $this->operators->getByPrecedence(1);

        while ($this->current()?->is(TokenType::Operator) && in_array($this->current()->value, $precedence1Operators)) {
            $symbol = $this->current()->value;
            $this->advance();
            $right = $this->parseMultiplication();

            $left = $this->operators->get($symbol)->apply($left, $right);
        }

        return $left;
    }

    private function parseMultiplication(): float
    {
        $left = $this->parsePower();
        $precedence2Operators = $this->operators->getByPrecedence(2);

        while ($this->current()?->is(TokenType::Operator) && in_array($this->current()->value, $precedence2Operators)) {
            $symbol = $this->current()->value;
            $this->advance();
            $right = $this->parsePower();

            $left = $this->operators->get($symbol)->apply($left, $right);
        }

        return $left;
    }

    private function parsePower(): float
    {
        $left = $this->parseUnary();
        $precedence3Operators = $this->operators->getByPrecedence(3);

        if ($this->current()?->is(TokenType::Operator) && in_array($this->current()->value, $precedence3Operators)) {
            $symbol = $this->current()->value;
            $this->advance();
            $right = $this->parsePower();

            return $this->operators->get($symbol)->apply($left, $right);
        }

        return $left;
    }

    private function parseUnary(): float
    {
        if ($this->current()?->is(TokenType::Operator) && $this->current()->value === '-') {
            $this->advance();

            return -$this->parseUnary();
        }

        if ($this->current()?->is(TokenType::Operator) && $this->current()->value === '+') {
            $this->advance();

            return $this->parseUnary();
        }

        return $this->parseFactor();
    }

    private function parseFactor(): float
    {
        $token = $this->current();

        if (! $token) {
            throw ExpressionParseException::invalidSyntax('Unexpected end of expression');
        }

        if ($token->is(TokenType::Number)) {
            $this->advance();

            return $token->value;
        }

        if ($token->is(TokenType::Function)) {
            $func = $token->value;
            $this->advance();

            if ($this->current()?->isNot(TokenType::LeftParen) ?? true) {
                throw ExpressionParseException::invalidSyntax("Expected '(' after function {$func}");
            }

            $this->advance();
            $arg = $this->parseExpression();

            if ($this->current()?->isNot(TokenType::RightParen) ?? true) {
                throw ExpressionParseException::mismatchedParentheses();
            }

            $this->advance();

            return $this->applyFunction($func, $arg);
        }

        if ($token->is(TokenType::LeftParen)) {
            $this->advance();
            $result = $this->parseExpression();

            if ($this->current()?->isNot(TokenType::RightParen) ?? true) {
                throw ExpressionParseException::mismatchedParentheses();
            }

            $this->advance();

            return $result;
        }

        throw ExpressionParseException::invalidSyntax("Unexpected token: {$token->value}");
    }

    private function applyFunction(string $name, float $arg): float
    {
        return $this->functions->get($name)->apply($arg);
    }
}
