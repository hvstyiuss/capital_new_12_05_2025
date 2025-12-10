<?php

namespace App\Http\Controllers;

use App\Models\Critere;
use App\Http\Requests\StoreCritereRequest;
use App\Http\Requests\UpdateCritereRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CritereController extends Controller
{
    public function index()
    {
        return Critere::with('category')->orderBy('ordre')->paginate(20);
    }

    public function show(Critere $critere)
    {
        return $critere->load(['category', 'optionEvaluations']);
    }

    public function store(StoreCritereRequest $request)
    {
        $data = $request->validated();
        $critere = Critere::create($data);
        return response($critere, Response::HTTP_CREATED);
    }

    public function update(UpdateCritereRequest $request, Critere $critere)
    {
        $data = $request->validated();
        $critere->update($data);
        return $critere;
    }

    public function destroy(Critere $critere)
    {
        $critere->delete();
        return response()->noContent();
    }
}



