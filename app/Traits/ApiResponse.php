<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    public function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function errorResponse(
        string $message = 'Something went wrong',
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        mixed $errors = null
    ): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    public function createdResponse(mixed $data, string $message = 'Created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    public function deletedResponse(): HttpResponse
    {
        return response()->noContent();
    }

    public function notFoundResponse(string $resource = 'Resource'): JsonResponse
    {
        return $this->errorResponse("$resource not found", Response::HTTP_NOT_FOUND);
    }

    public function validationErrorResponse(mixed $errors): JsonResponse
    {
        return $this->errorResponse('Validation error', Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }
}
