<?php

namespace App\Enums;

enum TokenType: string
{
    case Number = 'number';
    case Operator = 'operator';
    case Function = 'function';
    case LeftParen = 'left_paren';
    case RightParen = 'right_paren';
}
