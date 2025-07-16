<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Contracts\Pagination\CursorPaginator;

trait ApiResponse
{
    public function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        if ($data instanceof AnonymousResourceCollection && $data->resource instanceof CursorPaginator) {
            $paginator = $data->resource->toArray();

            return response()->json([
                'message' => $message,
                'data' => $paginator['data'],
                'meta' => collect($paginator)->except('data'),
            ], $status);
        }

        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }



    public function errorResponse(
        string $message = 'Something went wrong',
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
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
