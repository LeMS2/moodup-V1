<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(Request $request)
{
    $query = Category::where('user_id', $request->user()->id)
        ->orderBy('name');

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where('name', 'like', "%{$search}%");
    }

    return CategoryResource::collection($query->get());
}
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
        ]);

        $category = Category::create([
            'user_id' => $request->user()->id,
            'name' => $data['name'],
        ]);

       return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function show(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) abort(403);
        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) abort(403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
        ]);

        $category->update($data);

        return new CategoryResource($category);
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) abort(403);

        $category->delete();
        return response()->json(['message' => 'Categoria removida.']);
    }
}