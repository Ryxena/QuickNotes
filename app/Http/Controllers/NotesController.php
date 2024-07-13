<?php

namespace App\Http\Controllers;

use App\Helper\ApiResponse;
use App\Models\Notes;
use Illuminate\http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NotesController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user()->id;
        $notes = Notes::where('user_id', $user)->get();
        if ($notes->isNotEmpty()) {
            ApiResponse::success($notes, "Success get all notes");
        } else {
            ApiResponse::error(null, "No notes found");
        }
    }

    public function search(Request $request)
    {
        $user = auth()->user()->id;
        $notes = Notes::where('user_id', $user)->get();
        if ($notes->isNotEmpty()) {
            ApiResponse::success($notes, "Success get all notes");
        } else {
            ApiResponse::error(null, "No notes found");
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'status' => ['sometimes', Rule::in(['favorite', 'regular'])],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed', $validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        $note = new Notes($validatedData);
        $note->users_id = auth()->user()->id;

        if ($request->hasFile('image')) {
            $note->image = $request->file('image')->store('notes', 'public');
        }

        $note->save();
        $note->categories()->attach($validatedData['category_ids']);

        return ApiResponse::success($note, 'Note created successfully', 201);
    }
}
