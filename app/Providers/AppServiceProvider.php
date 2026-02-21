<?php

namespace App\Providers;

use App\Services\Functions\AbsFunction;
use App\Services\Functions\CosFunction;
use App\Services\Functions\FunctionRegistry;
use App\Services\Functions\LnFunction;
use App\Services\Functions\LogFunction;
use App\Services\Functions\SinFunction;
use App\Services\Functions\SqrtFunction;
use App\Services\Functions\TanFunction;
use App\Services\Operators\AdditionOperator;
use App\Services\Operators\DivisionOperator;
use App\Services\Operators\ModuloOperator;
use App\Services\Operators\MultiplicationOperator;
use App\Services\Operators\OperatorRegistry;
use App\Services\Operators\PowerOperator;
use App\Services\Operators\SubtractionOperator;
use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OperatorRegistry::class, fn () => (new OperatorRegistry)
            ->register(new AdditionOperator)
            ->register(new SubtractionOperator)
            ->register(new MultiplicationOperator)
            ->register(new DivisionOperator)
            ->register(new ModuloOperator)
            ->register(new PowerOperator)
        );

        $this->app->singleton(FunctionRegistry::class, fn () => (new FunctionRegistry)
            ->register(new SqrtFunction)
            ->register(new AbsFunction)
            ->register(new SinFunction)
            ->register(new CosFunction)
            ->register(new TanFunction)
            ->register(new LogFunction)
            ->register(new LnFunction)
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);
        JsonResource::withoutWrapping();

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
