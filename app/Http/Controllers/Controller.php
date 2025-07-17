<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Multi-Warehouse & Multi-Country Inventory Management System API",
 *      description="This documentation describes all REST endpoints exposed by the Inventory Management backend. All requests (except authentication) are protected with JWT (Bearer) tokens.",
 *      @OA\Contact(email="support@example.com", name="API Support")
 * )
 *
 * @OA\Server(url=L5_SWAGGER_CONST_HOST, description="Primary API server")
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
