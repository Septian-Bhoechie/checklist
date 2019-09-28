<?php

namespace Bhoechie\Checklist\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($response->getStatusCode() == 200 && $response instanceof JsonResponse) {
            $originalData = $response->getData();
            if (isset($originalData->current_page)) {

                $response->setData([
                    'meta' => [
                        "count" => $originalData->per_page,
                        "total" => $originalData->total,
                    ],
                    "links" => [
                        "first" => $originalData->first_page_url,
                        "last" => $originalData->last_page_url,
                        "next" => $originalData->next_page_url,
                        "prev" => $originalData->prev_page_url,
                    ],
                    "data" => $originalData->data,
                ]);
            } else {
                $response->setData([
                    "data" => [
                        'attributes' => $originalData,
                    ],
                ]);
            }

        }
        return $response;
    }
}
