<?php
namespace App\Services\Repositories;

use App\Models\Book;

class BookRepository extends EloquentRepository
{
    public function __construct(Book $model)
    {
        parent::__construct($model);
    }
}