<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
 
    $validator = Validator::make($request->all(), [
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

    $data = $validator->validated();

    $form = new Form();
    $form->title = $data['title'];
    $form->description = $data['description'] ?? null;
    $form->fields = $data['fields'];
    $form->is_active = $data['is_active'] ?? true;
    $form->start_at = $data['start_at'] ?? null;
    $form->end_at = $data['end_at'] ?? null;
    $form->save();

    return response()->json([
        'success' => 'true',
        'message' => 'Form created successfully',
        'form' => $form,
    ], 201);
}

public function update(Request $request, $id)
{
    $form = Form::find($id);
    if (!$form) {
        return response()->json([
            'success' => 'false',
            'message' => 'Form not found'
        ], 404);
    }
    $validator = Validator::make($request->all(), [
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
    $form = Form::find($id);
    if (!$form) {
        return response()->json([
            'success' => 'false',
            'message' => 'Form not found'
        ], 404);
    }
    $form->delete();
    return response()->json([
        'success' => 'true',
        'message' => 'Form Deleted successfully',
    ]);
}

}

