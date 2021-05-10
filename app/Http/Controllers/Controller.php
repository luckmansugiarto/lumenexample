<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\ResourceCollection as Collection;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    private $statusCodes = [
        'default' => Response::HTTP_OK,
        'hasUpdates' => -1,
        'failed' => ''
    ];

    protected function handleDeleteResponse(bool $result)
    {
        $this->statusCodes['hasUpdates'] = Response::HTTP_NO_CONTENT;
        return $this->handleResponse($result, true);
    }

    protected function handleGetResponse(Collection $data)
    {
        return $this->handleResponse($data);
    }

    protected function handlePostResponse($data)
    {
        $this->statusCodes['hasUpdates'] = Response::HTTP_CREATED;
        return $this->handleResponse($data, true);
    }

    protected function handlePutResponse(bool $result)
    {
        $this->statusCodes['hasUpdates'] = Response::HTTP_NO_CONTENT;
        return $this->handleResponse($result, true);
    }

    private function handleResponse($data, $isMutationOperation = false)
    {
        $statusCode = $this->statusCodes['default'];

        if (is_bool($data))
        {
            if ($data === true)
            {
                $statusCode = $this->statusCodes['hasUpdates'];

            }
            $data = [];
        }
        else if ($isMutationOperation === true && $data instanceof Collection && $data->count() > 0)
        {
            $statusCode = $this->statusCodes['hasUpdates'];
        }

        return response()->json($data, $statusCode);
    }
}
