<?php

namespace App\Http\Controllers;

use App\Models\OptionEvaluation;
use App\Http\Requests\StoreOptionEvaluationRequest;
use App\Http\Requests\UpdateOptionEvaluationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OptionEvaluationController extends Controller
{
    public function index()
    {
        return OptionEvaluation::with('critere')->orderBy('ordre')->paginate(20);
    }

    public function show(OptionEvaluation $optionEvaluation)
    {
        return $optionEvaluation->load('critere');
    }

    public function store(StoreOptionEvaluationRequest $request)
    {
        $data = $request->validated();
        $option = OptionEvaluation::create($data);
        return response($option, Response::HTTP_CREATED);
    }

    public function update(UpdateOptionEvaluationRequest $request, OptionEvaluation $optionEvaluation)
    {
        $data = $request->validated();
        $optionEvaluation->update($data);
        return $optionEvaluation;
    }

    public function destroy(OptionEvaluation $optionEvaluation)
    {
        $optionEvaluation->delete();
        return response()->noContent();
    }
}



