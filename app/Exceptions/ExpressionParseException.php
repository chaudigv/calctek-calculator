<?php

namespace App\Exceptions;

use Exception;

class ExpressionParseException extends Exception
{
    public static function divisionByZero(): self
    {
        return new self('Division by zero');
    }

    public static function invalidSyntax(string $details = ''): self
    {
        $message = 'Invalid syntax';

        if ($details) {
            $message .= ": {$details}";
        }

        return new self($message);
    }

    public static function negativeSquareRoot(): self
    {
        return new self('Cannot calculate square root of negative number');
    }

    public static function unknownFunction(string $name): self
    {
        return new self("Unknown function: {$name}");
    }

    public static function mismatchedParentheses(): self
    {
        return new self('Mismatched parentheses');
    }
}
