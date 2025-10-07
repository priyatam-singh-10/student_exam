<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    
public function index(Request $request)
{
    $query = Form::query();
    if (!$request->user() || $request->user()->role !== 'admin') {
        $query->where('is_active', true);
    }
    return response()->json($query->latest()->paginate(20));
}

public function show($id)
{
    $form = Form::findOrFail($id);
    return response()->json([
        'success' => 'true',
        'message' => 'Form details',
        'form' => $form
    ]);
}

public function store(Request $request)
{
    $validator = validator::make([$request->all()], [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'fields' => 'required|array',
        'is_active' => 'sometimes|boolean',
        'start_at' => 'nullable|date',
        'end_at' => 'nullable|date|after_or_equal:start_at',
    ]);
    if ($validator->fails()) {
        return response()->json([$validator->errors()], 422);
    }
    $form = Form::create($validator->validated());
    return response()->json($form, 201);
}

public function update(Request $request, $id)
{
    $form = Form::findOrFail($id);
    $validator = validator::make($request->all(), [
        'title' => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        'fields' => 'sometimes|array',
        'is_active' => 'sometimes|boolean',
        'start_at' => 'nullable|date',
        'end_at' => 'nullable|date|after_or_equal:start_at',
    ]);
    $form->update($validator->validated());
    return response()->json($form);
}

public function destroy($id)
{
    $form = Form::findOrFail($id);
    $form->delete();
    return response()->json([
        'success' => 'true',
        'message' => 'Form Deleted successfully',
    ]);
}

}

