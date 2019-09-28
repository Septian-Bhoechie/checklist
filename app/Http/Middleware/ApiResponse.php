<?php

namespace Bhoechie\Checklist\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

/**
 * Api Response Middleware.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
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
                $data = [
                    "data" => [
                        'attributes' => $originalData,
                    ],
                ];
                if (isset($originalData->id)) {
                    $originalData = (array) $originalData;
                    $data['data']['id'] = $originalData['id'];
                    unset($originalData['id']);
                    $data['data']['attributes'] = $originalData;
                }

                $response->setData($data);
            }

        }
        return $response;
    }
}
