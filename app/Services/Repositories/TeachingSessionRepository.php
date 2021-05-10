<?php
namespace App\Services\Repositories;

use App\Contracts\Repositories\TeachingSessionRepositoryInterface;
use App\Models\TeachingSession;
use App\Services\Repositories\BookRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeachingSessionRepository extends EloquentRepository
    implements TeachingSessionRepositoryInterface
{
    private BookRepository $bookRep;

    public function __construct(TeachingSession $model, BookRepository $bookRep)
    {
        parent::__construct($model);
        $this->bookRep = $bookRep;
    }

    public function addBook(int $bookId)
    {
        $hasUpdates = false;

        foreach (parent::getRecords() as $session)
        {
            if (!$session->books->contains($bookId))
            {
                $book = $this->bookRep->applyFilters(['id' => $bookId])
                    ->getRecords();

                if (count($book) === 1)
                {
                    $session->books()->attach($book[0]->id);
                    $hasUpdates = true;
                }
            }
        }

        return $hasUpdates;
    }

    public function applyFilterByFutureStartDate()
    {
        return $this->applyDateFilter('start_time', '>=');
    }

    public function applyFilterByPastStartDate()
    {
        return $this->applyDateFilter('start_time', '<');
    }

    public function applyFilterByUser(int $userId)
    {
        $this->applyFilters(['user_id' => $userId]);

        return $this;
    }

    public function delete(): bool
    {
        $this->removeBooks($this->model->first()->books->pluck('id')->toArray());
        return parent::delete();
    }

    public function hasBook(int $bookId): bool
    {
        $book = $this->bookRep->applyFilterById($bookId)->getRecords();

        return $book->count() > 1;
    }

    public function getRecords(array $attributes = []): Collection
    {
        if (is_null($this->model->getQuery()->orders))
        {
            $this->applySorting(['start_time' => 'asc']);
        }

        return parent::getRecords($attributes);
    }

    public function loadBooks()
    {
        $this->model = $this->model->with('books');

        return $this;
    }

    public function removeBooks(array $bookIds): bool
    {
        return parent::getRecords()->reduce(function ($carry, $session) use ($bookIds) {
            $carry = $carry || collect($bookIds)->reduce(function ($subCarry, $bookId) use ($session) {
                if ($session->books->contains($bookId) && $session->books()->detach($bookId))
                {
                    $subCarry = $subCarry || true;
                }

                return $subCarry;
            }, false);

            return $carry;
        }, false);
    }

    private function applyDateFilter(string $dateAttribute, string $operator)
    {
        return $this->applyFilters([
            [$dateAttribute, $operator, Carbon::now()->format($this->model->getModel()->getDateFormat())]
        ]);
    }
}