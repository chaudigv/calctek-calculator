# CalcTek Calculator

A full-stack calculator application built with Laravel 12 and Vue.js, featuring a recursive descent parser for safe expression evaluation and a persistent calculation history.

## Features

- **Expression-based calculations** - Supports full mathematical expressions like `5 + (3 - 1) * 2`
- **Safe parsing** - Uses a recursive descent parser instead of `eval()` for security
- **Calculation history** - All calculations are persisted with a "ticker tape" display
- **Error handling** - Invalid expressions are stored with polymorphic error records
- **Keyboard support** - Full keyboard input for expressions

### Supported Operations

| Operator | Description | Example |
|----------|-------------|---------|
| `+` | Addition | `5 + 3` |
| `-` | Subtraction | `10 - 4` |
| `*` | Multiplication | `6 * 7` |
| `/` | Division | `20 / 4` |
| `%` | Modulo | `10 % 3` |
| `^` | Power | `2 ^ 8` |
| `()` | Parentheses | `(5 + 3) * 2` |

### Supported Functions

| Function | Description | Example |
|----------|-------------|---------|
| `sqrt()` | Square root | `sqrt(16)` |
| `abs()` | Absolute value | `abs(-5)` |
| `sin()` | Sine (radians) | `sin(3.14159)` |
| `cos()` | Cosine (radians) | `cos(0)` |
| `tan()` | Tangent (radians) | `tan(0.785)` |
| `log()` | Base-10 logarithm | `log(100)` |
| `ln()` | Natural logarithm | `ln(2.718)` |

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue 3 (Composition API), TypeScript, Tailwind CSS
- **Database**: SQLite (default) or MySQL/PostgreSQL
- **Build**: Vite

## Architecture

### Expression Parser

The parser uses a **recursive descent** approach with operator precedence:

```
Precedence (lowest to highest):
┌───────────┬────────────┬───────────────┐
│ Level     │ Operators  │ Associativity │
├───────────┼────────────┼───────────────┤
│ 1 (low)   │ +  -       │ left          │
│ 2         │ *  /  %    │ left          │
│ 3         │ ^          │ right         │
│ 4         │ unary -/+  │ right         │
│ 5 (high)  │ () funcs   │ n/a           │
└───────────┴────────────┴───────────────┘
```

The call hierarchy mirrors precedence levels:

```
parseExpression()
  └─ parseAddition()        ← handles + and -
       └─ parseMultiplication()  ← handles * / %
            └─ parsePower()      ← handles ^
                 └─ parseUnary() ← handles unary -/+
                      └─ parseFactor() ← handles numbers, (), functions
```

### Extensible Design

Operators and functions are registered via Laravel's service container:

```php
// app/Providers/AppServiceProvider.php
$this->app->singleton(OperatorRegistry::class, fn () => (new OperatorRegistry)
    ->register(new AdditionOperator)
    ->register(new SubtractionOperator)
    // ...
);
```

To add a new operator or function, create a class implementing `OperatorInterface` or `FunctionInterface` and register it.

### Polymorphic Errors

Calculation errors are stored in a separate `errors` table using Laravel's polymorphic relationships, allowing the error system to be reused across different models:

```php
// app/Models/Concerns/HasErrors.php
trait HasErrors
{
    public function error(): MorphOne
    {
        return $this->morphOne(Error::class, 'errorable');
    }
}
```

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- npm or pnpm

### Setup

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd calctek-calculator
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node dependencies:
   ```bash
   npm install
   ```

4. Copy environment file and generate key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. Start the development server:
   ```bash
   composer dev
   ```

   This runs both the Laravel server and Vite dev server concurrently.

7. Visit `http://localhost:8000`

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/calculations` | List all calculations |
| POST | `/api/calculations` | Create a calculation |
| DELETE | `/api/calculations/{id}` | Delete a calculation |
| DELETE | `/api/calculations` | Clear all calculations |

### Example Request

```bash
curl -X POST http://localhost:8000/api/calculations \
  -H "Content-Type: application/json" \
  -d '{"expression": "5 + (3 - 1) * 2"}'
```

### Example Response

```json
{
  "id": 1,
  "expression": "5 + (3 - 1) * 2",
  "result": "9",
  "error": null,
  "created_at": "2026-02-21T10:30:00.000000Z",
  "updated_at": "2026-02-21T10:30:00.000000Z"
}
```

## Testing

Run the test suite:

```bash
php artisan test
```

Run specific test files:

```bash
php artisan test --filter=ExpressionParserTest
php artisan test --filter=CalculationsTest
```

## Project Structure

```
app/
├── DTOs/
│   └── Token.php                 # Token data transfer object
├── Enums/
│   └── TokenType.php             # Token type enumeration
├── Exceptions/
│   └── ExpressionParseException.php
├── Http/
│   ├── Controllers/
│   │   └── CalculationsController.php
│   ├── Requests/
│   │   └── StoreCalculationRequest.php
│   └── Resources/
│       ├── CalculationResource.php
│       └── ErrorResource.php
├── Models/
│   ├── Calculation.php
│   ├── Concerns/
│   │   └── HasErrors.php         # Reusable trait for error relationships
│   └── Error.php
├── Providers/
│   └── AppServiceProvider.php    # Operator/function registration
└── Services/
    ├── ExpressionParser.php      # Recursive descent parser
    ├── Registry.php              # Generic registry base class
    ├── Functions/
    │   ├── FunctionInterface.php
    │   ├── FunctionRegistry.php
    │   ├── SqrtFunction.php
    │   └── ...
    └── Operators/
        ├── OperatorInterface.php
        ├── OperatorRegistry.php
        ├── AdditionOperator.php
        └── ...

resources/js/
├── components/
│   └── Calculator/
│       ├── CalculatorDisplay.vue
│       ├── CalculatorKeypad.vue
│       └── TickerTape.vue
├── composables/
│   └── useCalculator.ts          # Calculator state management
├── pages/
│   └── Calculator.vue            # Main calculator page
└── types/
    └── calculator.ts             # TypeScript type definitions
```
