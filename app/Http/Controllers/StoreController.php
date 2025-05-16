<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $stores = $business->stores()
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('code', 'like', "%{$request->search}%");
                });
            })
            ->when($request->active, fn($q) => $q->active())
            ->when($request->main_store, fn($q) => $q->mainStore())
            ->with('manager:id,name,email')
            ->latest()
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($stores);
    }

    public function store(Request $request, Business $business)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:20', Rule::unique('stores')],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'manager_id' => 'nullable|exists:users,id',
            'is_main_store' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        // If this is set as main store, unset other main stores
        if ($request->is_main_store) {
            $business->stores()->where('is_main_store', true)->update(['is_main_store' => false]);
        }

        $store = $business->stores()->create($validator->validated());

        return $this->successResponse($store, 'Store created successfully', 201);
    }

    public function show(Business $business, Store $store)
    {
        if ($store->business_id !== $business->id) {
            return $this->errorResponse('Store does not belong to this business', 403);
        }

        $store->load(['manager:id,name,email']);

        return $this->successResponse($store);
    }

    public function update(Request $request, Business $business, Store $store)
    {
        if ($store->business_id !== $business->id) {
            return $this->errorResponse('Store does not belong to this business', 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:20', Rule::unique('stores')->ignore($store->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'manager_id' => 'nullable|exists:users,id',
            'is_main_store' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        // If this is set as main store, unset other main stores
        if ($request->is_main_store && !$store->is_main_store) {
            $business->stores()->where('is_main_store', true)->update(['is_main_store' => false]);
        }

        $store->update($validator->validated());

        return $this->successResponse($store, 'Store updated successfully');
    }

    public function destroy(Business $business, Store $store)
    {
        if ($store->business_id !== $business->id) {
            return $this->errorResponse('Store does not belong to this business', 403);
        }

        if ($store->is_main_store) {
            return $this->errorResponse('Cannot delete main store', 400);
        }

        $store->delete();

        return $this->successResponse(null, 'Store deleted successfully');
    }

    public function toggleStatus(Business $business, Store $store)
    {
        if ($store->business_id !== $business->id) {
            return $this->errorResponse('Store does not belong to this business', 403);
        }

        if ($store->is_main_store) {
            return $this->errorResponse('Cannot deactivate main store', 400);
        }

        $store->update(['is_active' => !$store->is_active]);

        return $this->successResponse([
            'is_active' => $store->is_active
        ], 'Store status updated successfully');
    }

    public function inventory(Request $request, Business $business, Store $store)
    {
        if ($store->business_id !== $business->id) {
            return $this->errorResponse('Store does not belong to this business', 403);
        }

        $inventory = $store->inventory()
            ->with('product:id,name,sku,type')
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('sku', 'like', "%{$request->search}%");
                });
            })
            ->when($request->status, function($query) use ($request) {
                match($request->status) {
                    'in_stock' => $query->inStock(),
                    'low_stock' => $query->lowStock(),
                    'out_of_stock' => $query->where('available_quantity', '<=', 0),
                    'expiring' => $query->expiring(),
                    'expired' => $query->expired(),
                    default => null
                };
            })
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($inventory);
    }
} 