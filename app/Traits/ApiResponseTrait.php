<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a standardized API response.
     *
     * @param bool $error
     * @param string $message
     * @param mixed $data
     * @param mixed $pagination
     * @param int $status
     * @return JsonResponse
     */
    protected function apiResponse(bool $error, string $message, $data = null, $pagination = null, int $status = 200): JsonResponse
    {
        $response = [
            'error' => $error,
            'message' => $message,
            'data' => $data,
        ];

        if ($pagination !== null) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $status);
    }

    /**
     * Map Laravel paginator to standardized pagination format.
     *
     * @param mixed $paginator
     * @return array
     */
    protected function formatPagination($paginator): array
    {
        return [
            'pageNumber' => $paginator->currentPage(),
            'pageSize' => $paginator->perPage(),
            'count' => $paginator->total(),
            'totalPages' => $paginator->lastPage(),
            'hasNextPage' => $paginator->hasMorePages(),
            'hasPreviousPage' => $paginator->currentPage() > 1,
            'nextPage' => $paginator->nextPageUrl(),
            'previousPage' => $paginator->previousPageUrl(),
        ];
    }
}
