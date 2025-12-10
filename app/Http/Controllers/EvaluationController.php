<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Http\Requests\StoreEvaluationRequest;
use App\Http\Requests\UpdateEvaluationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EvaluationController extends Controller
{
    public function index()
    {
        return Evaluation::with(['user', 'evaluator'])->paginate(20);
    }

    public function show(Evaluation $evaluation)
    {
        return $evaluation->load(['user', 'evaluator', 'responseEvaluations.critere', 'responseEvaluations.option', 'evaluationPrimes']);
    }

    public function store(StoreEvaluationRequest $request)
    {
        $data = $request->validated();
        $evaluation = Evaluation::create($data);
        return response($evaluation, Response::HTTP_CREATED);
    }

    public function update(UpdateEvaluationRequest $request, Evaluation $evaluation)
    {
        $data = $request->validated();
        $evaluation->update($data);
        return $evaluation;
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();
        return response()->noContent();
    }
}



