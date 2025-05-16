<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Businesses",
 *     description="API Endpoints for business management"
 * )
 */
class BusinessController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/businesses",
     *     summary="List all businesses",
     *     tags={"Businesses"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by business type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"bakery", "cake_tools", "academy"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search businesses by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of businesses",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Business")),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $businesses = Business::query()
            ->when($request->type, fn($q) => $q->ofType($request->type))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->active, fn($q) => $q->active())
            ->latest()
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($businesses);
    }

    /**
     * @OA\Post(
     *     path="/api/businesses",
     *     summary="Create a new business",
     *     tags={"Businesses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "type", "email"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="type", type="string", enum={"bakery", "cake_tools", "academy"}),
     *             @OA\Property(property="registration_number", type="string", maxLength=50),
     *             @OA\Property(property="tax_number", type="string", maxLength=50),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string", maxLength=20),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="city", type="string", maxLength=100),
     *             @OA\Property(property="state", type="string", maxLength=100),
     *             @OA\Property(property="country", type="string", maxLength=100),
     *             @OA\Property(property="postal_code", type="string", maxLength=20),
     *             @OA\Property(property="currency", type="string", maxLength=3),
     *             @OA\Property(property="timezone", type="string"),
     *             @OA\Property(property="logo", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Business created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Business created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Business")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="error", type="string", example="The given data was invalid")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['bakery', 'cake_tools', 'academy'])],
            'registration_number' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'email' => 'required|email|unique:businesses,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'currency' => 'nullable|string|size:3',
            'timezone' => 'nullable|string|timezone',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $data = $validator->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('businesses/logos', 'public');
        }

        $business = Business::create($data);

        // Create main store for the business
        $business->stores()->create([
            'name' => $business->name . ' Main Store',
            'code' => strtoupper(substr($business->name, 0, 3)) . '-001',
            'is_main_store' => true,
        ]);

        return $this->successResponse($business, 'Business created successfully', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/businesses/{business}",
     *     summary="Get business details",
     *     tags={"Businesses"},
     *     @OA\Parameter(
     *         name="business",
     *         in="path",
     *         description="Business ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Business")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Business not found"
     *     )
     * )
     */
    public function show(Business $business)
    {
        $business->load(['mainStore', 'stores']);
        return $this->successResponse($business);
    }

    /**
     * @OA\Put(
     *     path="/api/businesses/{business}",
     *     summary="Update business details",
     *     tags={"Businesses"},
     *     @OA\Parameter(
     *         name="business",
     *         in="path",
     *         description="Business ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BusinessUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Business updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Business")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Business $business)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['bakery', 'cake_tools', 'academy'])],
            'registration_number' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'email' => ['required', 'email', Rule::unique('businesses')->ignore($business->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'currency' => 'nullable|string|size:3',
            'timezone' => 'nullable|string|timezone',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $data = $validator->validated();

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($business->logo_path) {
                Storage::disk('public')->delete($business->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('businesses/logos', 'public');
        }

        $business->update($data);

        return $this->successResponse($business, 'Business updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/businesses/{business}",
     *     summary="Delete a business",
     *     tags={"Businesses"},
     *     @OA\Parameter(
     *         name="business",
     *         in="path",
     *         description="Business ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Business deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy(Business $business)
    {
        // Delete logo if exists
        if ($business->logo_path) {
            Storage::disk('public')->delete($business->logo_path);
        }

        $business->delete();

        return $this->successResponse(null, 'Business deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/businesses/{business}/toggle-status",
     *     summary="Toggle business active status",
     *     tags={"Businesses"},
     *     @OA\Parameter(
     *         name="business",
     *         in="path",
     *         description="Business ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Business status updated successfully"),
     *             @OA\Property(property="data", @OA\Property(property="is_active", type="boolean"))
     *         )
     *     )
     * )
     */
    public function toggleStatus(Business $business)
    {
        $business->update(['is_active' => !$business->is_active]);

        return $this->successResponse([
            'is_active' => $business->is_active
        ], 'Business status updated successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/businesses/{business}/statistics",
     *     summary="Get business statistics",
     *     tags={"Businesses"},
     *     @OA\Parameter(
     *         name="business",
     *         in="path",
     *         description="Business ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_stores", type="integer"),
     *                 @OA\Property(property="total_products", type="integer"),
     *                 @OA\Property(property="low_stock_products", type="integer"),
     *                 @OA\Property(property="out_of_stock_products", type="integer"),
     *                 @OA\Property(property="total_bakery_products", type="integer"),
     *                 @OA\Property(property="total_tools", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function statistics(Business $business)
    {
        $stats = [
            'total_stores' => $business->stores()->count(),
            'total_products' => $business->products()->count(),
            'low_stock_products' => $business->products()->lowStock()->count(),
            'out_of_stock_products' => $business->products()->whereDoesntHave('inventory', function($q) {
                $q->where('available_quantity', '>', 0);
            })->count(),
        ];

        if ($business->isBakery()) {
            $stats['total_bakery_products'] = $business->products()->where('type', 'bakery')->count();
        } elseif ($business->isCakeTools()) {
            $stats['total_tools'] = $business->products()->where('type', 'cake_tool')->count();
        }

        return $this->successResponse($stats);
    }

    public function switch($business)
    {
        $user = auth()->user();
        
        // Validate if user has access to this business
        if (!in_array($business, $user->enabled_businesses ?? [])) {
            return back()->with('error', 'You do not have access to this business.');
        }

        // Update user's current business
        $user->current_business = $business;
        $user->save();

        return back()->with('success', 'Successfully switched to ' . ucfirst($business));
    }
} 