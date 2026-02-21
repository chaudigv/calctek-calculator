<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpressionParseException;
use App\Http\Requests\StoreCalculationRequest;
use App\Http\Resources\CalculationResource;
use App\Models\Calculation;
use App\Services\ExpressionParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CalculationsController extends Controller
{
    public function __construct(private ExpressionParser $parser) {}

    public function index(): AnonymousResourceCollection
    {
        $calculations = Calculation::query()
            ->with('error')
            ->orderBy('created_at', 'desc')
            ->get();

        return CalculationResource::collection($calculations);
    }

    public function store(StoreCalculationRequest $request): CalculationResource
    {
        $expression = $request->validated('expression');

        try {
            $result = $this->parser->parse($expression);

            $calculation = Calculation::create([
                'expression' => $expression,
                'result' => $result,
            ]);

            return new CalculationResource($calculation);
        } catch (ExpressionParseException $e) {
            $calculation = Calculation::create([
                'expression' => $expression,
            ]);

            $calculation->error()->create([
                'message' => $e->getMessage(),
            ]);

            $calculation->load('error');

            return new CalculationResource($calculation);
        }
    }

    public function destroy(Calculation $calculation): JsonResponse
    {
        $calculation->delete();

        return response()->json(null, 204);
    }

    public function destroyAll(): JsonResponse
    {
        Calculation::query()->delete();

        return response()->json(null, 204);
    }
}
