<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Business",
 *     required={"id", "name", "type", "email", "is_active", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="name", type="string", maxLength=255),
 *     @OA\Property(property="type", type="string", enum={"bakery", "cake_tools", "academy"}),
 *     @OA\Property(property="registration_number", type="string", maxLength=50, nullable=true),
 *     @OA\Property(property="tax_number", type="string", maxLength=50, nullable=true),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="phone", type="string", maxLength=20, nullable=true),
 *     @OA\Property(property="address", type="string", nullable=true),
 *     @OA\Property(property="city", type="string", maxLength=100, nullable=true),
 *     @OA\Property(property="state", type="string", maxLength=100, nullable=true),
 *     @OA\Property(property="country", type="string", maxLength=100, nullable=true),
 *     @OA\Property(property="postal_code", type="string", maxLength=20, nullable=true),
 *     @OA\Property(property="currency", type="string", maxLength=3, nullable=true),
 *     @OA\Property(property="timezone", type="string", nullable=true),
 *     @OA\Property(property="logo_path", type="string", nullable=true),
 *     @OA\Property(property="is_active", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Business {}

/**
 * @OA\Schema(
 *     schema="BusinessUpdateRequest",
 *     required={"name", "type", "email"},
 *     @OA\Property(property="name", type="string", maxLength=255),
 *     @OA\Property(property="type", type="string", enum={"bakery", "cake_tools", "academy"}),
 *     @OA\Property(property="registration_number", type="string", maxLength=50),
 *     @OA\Property(property="tax_number", type="string", maxLength=50),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="phone", type="string", maxLength=20),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="city", type="string", maxLength=100),
 *     @OA\Property(property="state", type="string", maxLength=100),
 *     @OA\Property(property="country", type="string", maxLength=100),
 *     @OA\Property(property="postal_code", type="string", maxLength=20),
 *     @OA\Property(property="currency", type="string", maxLength=3),
 *     @OA\Property(property="timezone", type="string"),
 *     @OA\Property(property="logo", type="string", format="binary"),
 *     @OA\Property(property="is_active", type="boolean")
 * )
 */
class BusinessUpdateRequest {}

/**
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     @OA\Property(property="current_page", type="integer"),
 *     @OA\Property(property="from", type="integer"),
 *     @OA\Property(property="last_page", type="integer"),
 *     @OA\Property(property="path", type="string"),
 *     @OA\Property(property="per_page", type="integer"),
 *     @OA\Property(property="to", type="integer"),
 *     @OA\Property(property="total", type="integer")
 * )
 */
class PaginationMeta {}

/**
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     @OA\Property(property="first", type="string", format="uri"),
 *     @OA\Property(property="last", type="string", format="uri"),
 *     @OA\Property(property="prev", type="string", format="uri", nullable=true),
 *     @OA\Property(property="next", type="string", format="uri", nullable=true)
 * )
 */
class PaginationLinks {} 