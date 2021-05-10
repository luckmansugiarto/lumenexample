<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        static $sequence = 1;

    	return [
    	    'name' => 'Book ' . ($sequence++),
            'isbn' => $this->faker->isbn10,
            'published_at' => $this->faker->dateTime,
            'author' => $this->faker->name
    	];
    }
}
