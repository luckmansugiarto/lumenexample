<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $books = [
            [
                "id" => 1,
                "name" => "Becke West Arnoldo",
                "isbn" => "3181444340",
                "published_at" => "2000-01-01",
                "author" => "Mrs. John Doe"
            ],
            [
                "id" => 2,
                "name" => "Schuster Champlinborough",
                "isbn" => "1441614311",
                "published_at" => "1991-07-29",
                "author" =>  "Prof. John Doe"
            ],
            [
                "id" => 3,
                "name" => "Okuneva MarianoVille",
                "isbn" => "0779284704",
                "published_at" => "2017-06-30",
                "author" =>  "Prof. Jane Doe"
            ],
            [
                "id" => 4,
                "name" => "Fahey LeonoraVille",
                "isbn" => "454120892X",
                "published_at" => "2001-02-18",
                "author" => "Mr. John Doe"
            ],
            [
                "id" => 5,
                "name" => "Treutel MaiaVille",
                "isbn" => "1969101032",
                "published_at" => "1993-06-14",
                "author" => "Dr. John Doe"
            ],
            [
                "id" => 6,
                "name" => "Hintz LeathaVille",
                "isbn" => "0989022390",
                "published_at" => "2007-11-12",
                "author" => "Dr. Jane Doe"
            ],
            [
                "id" => 7,
                "name" => "Murazik LarueVille",
                "isbn" => "545435998X",
                "published_at" => "1999-02-23",
                "author" => "Mr. Jane Doe"
            ],
            [
                "id" => 8,
                "name" => "Hoeger EarleneVille",
                "isbn" => "4807300024",
                "published_at" => "2016-02-10",
                "author" =>  "Prof. John Doe"
            ],
            [
                "id" => 9,
                "name" => "Jacobs CoreneVille",
                "isbn" => "1673027822",
                "published_at" => "1996-08-20",
                "author" =>  "Prof. Jane Doe"
            ],
            [
                "id" => 10,
                "name" => "Wunsch ThurmanVille",
                "isbn" => "4467121552",
                "published_at" => "2016-03-06",
                "author" => "Mr. John Doe"
            ]
        ];

        foreach ($books as $book) {
            \App\Models\Book::updateOrCreate(['id' => $book['id']], $book);
        }

        \App\Models\User::factory()->count(10)->create();

        \App\Models\TeachingSession::factory()
            ->count(30)
            ->create();
    }
}
