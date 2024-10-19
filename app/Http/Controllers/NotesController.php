<?php

namespace App\Http\Controllers;

use App\Helper\ApiResponse;
use App\Models\Notes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NotesController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $notes = Notes::with('categories')->where('users_id', $user->id)->get()->each(function ($note) {
            if ($note->updated_at->eq($note->created_at)) {
                $note->makeHidden('updated_at');
            }
            $note->categories->each(function ($category) {
                $category->makeHidden(['created_at', 'updated_at', 'pivot']);
            });
        });

        if ($notes->isNotEmpty()) {
            return ApiResponse::success($notes, 'Success get all notes');
        } else {
            return ApiResponse::error(null, 'No notes found');
        }
    }

    public function search(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $user = auth()->user();
        $notes = Notes::with('categories')
            ->where('users_id', $user->id)
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%')
                        ->orWhere('status', 'like', '%'.$search.'%')
                        ->orWhereHas('categories', function ($query) use ($search) {
                            $query->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->get()
            ->each(function ($note) {
                if ($note->updated_at->eq($note->created_at)) {
                    $note->makeHidden('updated_at');
                }
                $note->categories->each(function ($category) {
                    $category->makeHidden(['created_at', 'updated_at', 'pivot']);
                });
            });

        if ($notes->isNotEmpty()) {
            return ApiResponse::success($notes, 'Success get all notes');
        } else {
            return ApiResponse::error(null, 'No notes found');
        }
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_ids' => 'array|exists:categories,id|required',
            'status' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed', $validator->errors(), 422);
        }

        $imageContent = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageContent = $note->image = $request->file('image')->storeAs(
                'public/content/image',
                now()->format('YmdHis').'.'.$request->file('image')->getClientOriginalExtension()
            );

        }

        $note = Notes::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imageContent,
            'status' => $request->has('status') ? $request->input('status') : 'regular',
            'users_id' => $user->id,
        ]);

        if ($request->has('category_ids')) {
            $note->categories()->sync($request->category_ids);
        }

        return ApiResponse::success($note, 'Note created successfully', 201);
    }

    public function detail($id): JsonResponse
    {
        $note = Notes::with('categories')->where('id', $id)->where('users_id', Auth::id())->firstOrFail();

        return ApiResponse::success($note);
    }

    public function destroy($id): JsonResponse
    {
        $user = auth()->user();
        $note = Notes::where('id', $id)->where('users_id', $user->id)->firstOrFail();
        if (! $note) {
            return ApiResponse::error('Note not found');
        }
        $note->delete();

        return ApiResponse::success(null, 'Note deleted successfully');

    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = auth()->user();
        $note = Notes::where('id', $id)->where('users_id', $user->id)->first();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:3',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_ids' => 'array|exists:categories,id|required',
            'status' => 'sometimes|string|in:favorite,regular',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed', $validator->errors(), 422);
        }

        if ($request->hasFile('image')) {
            if ($note->image != null) {
                Storage::delete($note->image);
            }

            $note->image = $request->file('image')->storeAs(
                'public/content/image',
                now()->format('YmdHis').'.'.$request->file('image')->getClientOriginalExtension()
            );
        }
        if ($request->filled('title')) {
            $note->title = $request->input('title');
        }
        if ($request->filled('content')) {
            $note->content = $request->input('content');

        }
        $note->status = $request->has('status') ? $request->input('status') : 'regular';

        $note->save();

        if ($request->has('category_ids')) {
            $note->categories()->sync($request->category_ids);
        }

        return ApiResponse::success($note, 'Note updated successfully');
    }

    public function favorite($id): JsonResponse
    {
        $user = auth()->user();
        $note = Notes::where('id', $id)->where('users_id', $user->id)->first();
        if (! $note) {
            return ApiResponse::error('Note not found');
        }
        $note->status = 'favorite';
        $note->save();

        return ApiResponse::success($note, 'Note favorited successfully');
    }

    public function favorites(): JsonResponse
    {
        $user = auth()->user();
        $note = Notes::where('status', 'favorite')->where('users_id', $user->id)->get();
        if ($note->isNotEmpty()) {
            return ApiResponse::success($note, 'List Note favorite');
        }

        return ApiResponse::error(null, 'No favorites notes found');
    }
}
