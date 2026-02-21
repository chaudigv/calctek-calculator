<?php

namespace App\Models;

use App\Models\Concerns\HasErrors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasErrors;
    use HasFactory;

    protected $fillable = [
        'expression',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'result' => 'decimal:10',
        ];
    }
}
