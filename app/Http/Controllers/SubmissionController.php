<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    
public function index(Request $request)
{
    $query = Submission::with(['form'])
        ->where('user_id', $request->user()->id)
        ->latest();
    return response()->json($query->paginate(20));
}

public function show(Request $request, $id)
{
    $submission = Submission::with(['form', 'payments'])
        ->where('user_id', $request->user()->id)
        ->find($id);

    if (!$submission) {
        return response()->json([
            'success' => 'false',
            'message' => 'Submission not found'
        ], 404);
    }

    return response()->json([
        'success' => 'true',
        'submission' => $submission
    ], 200);
}


public function store(Request $request)
{
    $user = Auth::guard('api')->user();
    $validator = Validator::make($request->all(), [
        'form_id' => 'required|integer|exists:forms,id',
        'data' => 'required|array',
        'status' => 'sometimes|in:draft,submitted,approved,rejected',
    ]);
    if ($validator->fails()) {
        return response()->json([$validator->errors()], 422);
    }

    $submission = new Submission();
    $submission->user_id = $user->id;
    $submission->form_id = $request->form_id;
    $submission->data = $request->data;
    $submission->status = $request->status ?? 'submitted';
    $submission->save();

    return response()->json([
            'success' => 'true',
            'message' => 'Submission created successfully',
            'submission' => $submission,
    ], 201);
}

public function update(Request $request, $id)
{
    $user = Auth::guard('api')->user();
    $submission = Submission::where('user_id', $user->id)->findOrFail($id);
    $validated = Validator::make($request->all(), [
        'data' => 'sometimes|array',
        'status' => 'sometimes|in:draft,submitted,approved,rejected',
    ]);
    if ($validated->fails()) {
        return response()->json([$validated->errors()], 422);
    }
    $submission->data = $request->data ?? $submission->data;
    $submission->status = $request->status ?? $submission->status;
    $submission->save();

    return response()->json([
        'success' => 'true',
        'message' => 'Submission updated successfully',
        'submission' => $submission
    ]);
}

}

