<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Leymax POS API",
 *     description="API documentation for Leymax POS system",
 *     @OA\Contact(
 *         email="support@leymax.com",
 *         name="Leymax Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Businesses",
 *     description="Business management endpoints"
 * )
 * @OA\Tag(
 *     name="Stores",
 *     description="Store management endpoints"
 * )
 * @OA\Tag(
 *     name="Categories",
 *     description="Product category management endpoints"
 * )
 * @OA\Tag(
 *     name="Products",
 *     description="Product management endpoints"
 * )
 * @OA\Tag(
 *     name="Inventory",
 *     description="Inventory management endpoints"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return a success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error response.
     *
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => $message,
        ], $code);
    }

    /**
     * Return a paginated response.
     *
     * @param  \Illuminate\Pagination\LengthAwarePaginator  $paginator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function paginatedResponse($paginator)
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }
}
