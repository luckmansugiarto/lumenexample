<?php

namespace App\Contracts\Repositories;

use Illuminate\Support\Collection;

interface EloquentRepositoryInterface
{
    public function applyFilterById(int $id);

    public function applyPagination(int $max, int $page);

    public function applySorting($sortBy);

    public function delete(): bool;

    public function getRecords(array $columns): Collection;

    public function reset(): void;

    public function save(array $attributes);

    public function update(array $attributes): bool;
}