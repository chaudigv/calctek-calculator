<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Calculation */
class CalculationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expression' => $this->expression,
            'result' => $this->result,
            'error' => $this->whenLoaded('error', fn () => new ErrorResource($this->error)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
