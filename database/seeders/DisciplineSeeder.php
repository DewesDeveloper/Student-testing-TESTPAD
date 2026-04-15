<?php

namespace Database\Seeders;

use App\Models\Discipline;
use Illuminate\Database\Seeder;

class DisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = ['Структурное программирование', 'Жизненный цикл ПО', 'Основы программирования C# ч.2', 'Основы программирования C# ч.1', 'Обработка ошибок', 'ООП', 'Базы данных', 'ЖЦ ПО и тестирование', 'Введение в алгоритмы и структуры данных', 'Алгоритмы и структуры данных ч.2'];
        foreach ($data as $name) {
            Discipline::create(['name' => $name]);
        }
    }
}
