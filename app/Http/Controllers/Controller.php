<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Multi-Warehouse & Multi-Country Inventory Management System API",
 *      description="This documentation describes all REST endpoints in this system. All requests (except authentication) are protected with JWT (Bearer) tokens.",
 *      @OA\Contact(
 *          email="rezdar.00166214@gmail.com",
 *          name="API Support",
 *      ),
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local development server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter JWT Bearer token in format: Bearer {token}"
 * )
 *
 * @OA\Response(
 *     response="Unauthorized",
 *     description="Unauthenticated",
 *     @OA\JsonContent(
 *         type="object",
 *         @OA\Property(property="message", type="string", example="Unauthenticated")
 *     )
 * )
 *
 * @OA\Response(response="Unprocessable Content", description="Unprocessable Content",
 *     @OA\JsonContent(
 *               type="object",
 *               @OA\Property(property="message", type="string", example="The email has already been taken."),
 *               @OA\Property(
 *                   property="errors",
 *                   type="object",
 *                   example={
 *                       "email": {"The email has already been taken."}
 *                   }
 *               )
 *           )
 *       )
 *
 * @OA\Response(response="Not Found", description="Resource not found",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string", example="Resource not found")
 *     )
 * ),
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     description="Pagination metadata",
 *     @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/inventories"),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(
 *         property="next_cursor",
 *         type="string",
 *         example="eyJwYXRoIjoiL2FwaS9pbnZlbnRvcmllcyIsInBlcl9wYWdlIjoxMH0"
 *     ),
 *     @OA\Property(
 *         property="next_page_url",
 *         type="string",
 *         example="http://http://127.0.0.1:8000/api/inventories?cursor=eyJwYXRoIjoiL2FwaS9pbnZlbnRvcmllcyIsInBlcl9wYWdlIjoxMH0",
 *     ),
 *     @OA\Property(
 *         property="prev_cursor",
 *         type="string",
 *         example="eyJwYXRoIjoiL2FwaS9pbnZlbnRvcmlLSNfeDnBlcl9wYWdlIjoxMH0",
 *     ),
 *     @OA\Property(
 *         property="prev_page_url",
 *         type="string",
 *         example="http://http://127.0.0.1:8000/api/inventories?cursor=eyJwYXRoIjoiL2FwaS9pbnZlbnRvcmlLSNfeDnBlcl9wYWdlIjoxMH0"
 *     ),
 * )
 */
abstract class Controller
{
    //
}
