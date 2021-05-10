<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\TeachingSessionRepositoryInterface as Repository;
use App\Http\Requests\TeachingSessionRequest;
use App\Http\Resources\TeachingSessionResource as Resource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class TeachingSessionController extends Controller
{
    private Repository $repository;

    public function __construct(Repository $rep)
    {
        $this->repository = $rep;
    }

    public function assignBook($sessionId, $bookId, Request $request)
    {
        $this->verifySession($sessionId);

        if ($this->repository->hasBook($bookId))
        {
            return $this->handlePutResponse(false);
        }

        return $this->handlePostResponse($this->repository->addBook($bookId));
    }

    public function createNew(TeachingSessionRequest $request)
    {
        $result = $this->repository->save(array_merge($request->post(), ['user_id' => Auth::id()]));

        if (!is_null($result))
        {
            $result = [$result];
        }
        else
        {
            $result = [];
        }

        return $this->handlePostResponse(Resource::collection(collect($result)));
    }

    public function delete($id, Request $request)
    {
        $this->verifySession($id);

        return $this->handleDeleteResponse($this->repository->delete());
    }

    public function getDetails($id, Request $request)
    {
        $this->verifySession($id);
        return $this->handleGetResponse(Resource::collection($this->repository->loadBooks()->getRecords()));
    }

    public function getList(Request $request)
    {
        if (!is_null(Auth::id()))
        {
            $this->repository->applyFilterByUser(Auth::id());

            if ((bool)$request->get('show_past', 0) === false)
            {
                $this->repository->applyFilterByFutureStartDate();
            }
        }

        return $this->handleGetResponse(Resource::collection($this->repository->getRecords()));
    }

    public function removeBook($sessionId, $bookId, Request $request)
    {
        $this->verifySession($sessionId);

        $result = $this->repository
            ->applyFilters(['id' => $sessionId])
            ->removeBooks([$bookId]);

        return $this->handleDeleteResponse($result);
    }

    private function verifySession($sessionId)
    {
        $session = $this->repository
            ->applyFilterById($sessionId)
            ->applyFilterByUser(Auth::id())
            ->getRecords();

        if ($session->count() === 0)
        {
            abort(Response::HTTP_NOT_FOUND, 'Invalid session');
        }
    }
}
