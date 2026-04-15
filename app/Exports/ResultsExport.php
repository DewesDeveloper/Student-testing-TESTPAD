<?php

namespace App\Exports;

use App\Models\TestResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ResultsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $testId;

    public function __construct($testId)
    {
        $this->testId = $testId;
    }

    /**
     * Получаем результаты с подгрузкой студента и самого теста
     */
    public function collection()
    {
        return TestResult::where('test_id', $this->testId)
            ->with(['student', 'test'])
            ->latest()
            ->get();
    }

    /**
     * Заголовки таблицы Excel
     */
    public function headings(): array
    {
        return [
            '# ID Результата',
            'Название теста', // Новое поле
            'ФИО Студента',
            'Группа',
            'Баллы',
            'Макс. балл',
            'Процент (%)',
            'Оценка',        // Новое поле
            'Дата прохождения',
        ];
    }

    /**
     * Сопоставление данных для каждой строки
     */
    public function map($res): array
    {
        // 1. Рассчитываем процент
        $percentage = ($res->score / ($res->total_points ?: 1)) * 100;

        // 2. Получаем объект теста для доступа к его шкале оценок
        $test = $res->test;

        // 3. Логика определения оценки (на основе ваших настроек в БД)
        if ($percentage >= $test->grade_5_threshold) {
            $grade = 5;
        } elseif ($percentage >= $test->grade_4_threshold) {
            $grade = 4;
        } elseif ($percentage >= $test->grade_3_threshold) {
            $grade = 3;
        } else {
            $grade = 2;
        }

        return [
            $res->id,
            $test->title, // Название теста
            $res->student->name,
            $res->student->group ?? '—',
            $res->score,
            $res->total_points,
            round($percentage, 1).'%',
            $grade, // Цифровая оценка
            $res->completed_at->format('d.m.Y H:i'),
        ];
    }
}
