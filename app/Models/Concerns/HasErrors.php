<?php

namespace App\Models\Concerns;

use App\Models\Error;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasErrors
{
    public static function bootHasErrors(): void
    {
        static::deleting(function ($model) {
            $model->error?->delete();
        });
    }

    /** @return MorphOne<Error, $this> */
    public function error(): MorphOne
    {
        return $this->morphOne(Error::class, 'errorable');
    }

    public function hasError(): bool
    {
        return $this->error()->exists();
    }

    public function getErrorMessage(): ?string
    {
        return $this->error?->message;
    }
}
