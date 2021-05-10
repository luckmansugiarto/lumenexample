<?php

namespace App\Contracts\Repositories;

use Illuminate\Support\Collection;

interface TeachingSessionRepositoryInterface extends EloquentRepositoryInterface
{
    public function addBook(int $bookId);

    public function applyFilterByFutureStartDate();

    public function applyFilterByPastStartDate();

    public function applyFilterByUser(int $userId);

    public function hasBook(int $bookId): bool;

    public function loadBooks();

    public function removeBooks(array $bookIds): bool;
}