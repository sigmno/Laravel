<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@library.local'],
            [
                'name' => 'Администратор',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        );

        $fiction = Genre::firstOrCreate(
            ['name' => 'Художественная литература'],
            ['description' => 'Романы, повести и классические произведения.'],
        );

        $science = Genre::firstOrCreate(
            ['name' => 'Научно-популярная литература'],
            ['description' => 'Книги для расширения кругозора и обучения.'],
        );

        $bulgakov = Author::firstOrCreate(
            ['name' => 'Михаил Булгаков'],
            ['biography' => 'Русский писатель, драматург и театральный режиссер.'],
        );

        $hawking = Author::firstOrCreate(
            ['name' => 'Стивен Хокинг'],
            ['biography' => 'Физик-теоретик и популяризатор науки.'],
        );

        Book::firstOrCreate(
            ['isbn' => '978-5-17-087884-1'],
            [
                'author_id' => $bulgakov->id,
                'genre_id' => $fiction->id,
                'title' => 'Мастер и Маргарита',
                'description' => 'Знаменитый роман о любви, свободе и выборе.',
                'year' => 1967,
                'total_copies' => 5,
                'available_copies' => 5,
                'is_active' => true,
            ],
        );

        Book::firstOrCreate(
            ['isbn' => '978-5-17-085407-4'],
            [
                'author_id' => $hawking->id,
                'genre_id' => $science->id,
                'title' => 'Краткая история времени',
                'description' => 'Доступное изложение идей современной космологии.',
                'year' => 1988,
                'total_copies' => 3,
                'available_copies' => 3,
                'is_active' => true,
            ],
        );

        $this->call(BookSeeder::class);

        $admin->touch();
    }
}
