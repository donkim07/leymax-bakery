<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $categories = $business->categories()
            ->when($request->search, function($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->parent_id, function($query) use ($request) {
                $query->where('parent_id', $request->parent_id);
            }, function($query) {
                $query->whereNull('parent_id');
            })
            ->when($request->active, fn($q) => $q->active())
            ->ordered()
            ->with(['children' => function($query) {
                $query->ordered()->withCount('products');
            }])
            ->withCount('products')
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($categories);
    }

    public function store(Request $request, Business $business)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function($attribute, $value, $fail) use ($business) {
                    if ($value) {
                        $parent = Category::find($value);
                        if ($parent->business_id !== $business->id) {
                            $fail('The selected parent category does not belong to this business.');
                        }
                    }
                },
            ],
            'position' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category = $business->categories()->create($data);

        return $this->successResponse($category, 'Category created successfully', 201);
    }

    public function show(Business $business, Category $category)
    {
        if ($category->business_id !== $business->id) {
            return $this->errorResponse('Category does not belong to this business', 403);
        }

        $category->load([
            'parent',
            'children' => fn($q) => $q->ordered()->withCount('products'),
            'products' => fn($q) => $q->latest()->take(5)
        ])->loadCount('products');

        return $this->successResponse($category);
    }

    public function update(Request $request, Business $business, Category $category)
    {
        if ($category->business_id !== $business->id) {
            return $this->errorResponse('Category does not belong to this business', 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function($attribute, $value, $fail) use ($business, $category) {
                    if ($value) {
                        if ($value === $category->id) {
                            $fail('A category cannot be its own parent.');
                            return;
                        }
                        $parent = Category::find($value);
                        if ($parent->business_id !== $business->id) {
                            $fail('The selected parent category does not belong to this business.');
                        }
                    }
                },
            ],
            'position' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return $this->successResponse($category, 'Category updated successfully');
    }

    public function destroy(Business $business, Category $category)
    {
        if ($category->business_id !== $business->id) {
            return $this->errorResponse('Category does not belong to this business', 403);
        }

        if ($category->hasChildren()) {
            return $this->errorResponse('Cannot delete category with subcategories', 400);
        }

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return $this->successResponse(null, 'Category deleted successfully');
    }

    public function reorder(Request $request, Business $business)
    {
        $validator = Validator::make($request->all(), [
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.position' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        foreach ($request->categories as $item) {
            $category = Category::find($item['id']);
            if ($category->business_id !== $business->id) {
                return $this->errorResponse('One or more categories do not belong to this business', 403);
            }
            $category->update(['position' => $item['position']]);
        }

        return $this->successResponse(null, 'Categories reordered successfully');
    }

    public function toggleStatus(Business $business, Category $category)
    {
        if ($category->business_id !== $business->id) {
            return $this->errorResponse('Category does not belong to this business', 403);
        }

        $category->update(['is_active' => !$category->is_active]);

        // If deactivating, also deactivate all children
        if (!$category->is_active) {
            $category->children()->update(['is_active' => false]);
        }

        return $this->successResponse([
            'is_active' => $category->is_active
        ], 'Category status updated successfully');
    }
} 