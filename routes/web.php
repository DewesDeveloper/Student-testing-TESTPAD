<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PublicTestController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestSessionController;
use App\Models\Discipline;
use App\Models\Test;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index']);

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $user = Auth::user();

        if ($user->role === 'teacher') {
            $disciplines = Discipline::all();
            $query = Test::where('user_id', $user->id)->with(['results', 'discipline']);
            if ($request->filled('discipline_id')) {
                $query->where('discipline_id', $request->discipline_id);
            }
            $tests = $query->latest()->get();

            return view('dashboard', compact('tests', 'disciplines'));
        } else {
            // ЛОГИКА ДЛЯ СТУДЕНТА: Получаем его результаты
            $results = TestResult::where('user_id', $user->id)
                ->with(['test.discipline'])
                ->latest()
                ->get();

            return view('dashboard_student', compact('results'));
        }
    })->middleware(['auth:sanctum'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/tests/create', [TestController::class, 'create'])->name('tests.create');
    Route::post('/tests', [TestController::class, 'store'])->name('tests.store');
    Route::get('/tests/{test}', [TestController::class, 'show'])->name('tests.show');
    Route::get('/tests/{test}/edit', [TestController::class, 'edit'])->name('tests.edit');
    Route::put('/tests/{test}', [TestController::class, 'update'])->name('tests.update');
    Route::delete('/tests/{test}', [TestController::class, 'destroy'])->name('tests.destroy');

    Route::get('/tests/{test}/result-settings', [TestController::class, 'resultSettings'])->name('tests.result-settings');
    Route::patch('/tests/{test}/result-settings', [TestController::class, 'updateResultSettings'])->name('tests.result-settings.update');

    Route::get('/take-test/{test}', [TestSessionController::class, 'show'])->name('test.take');
    Route::post('/take-test/{test}/submit', [TestSessionController::class, 'submit'])->name('test.submit');
    Route::get('/result/{result}', [TestSessionController::class, 'showResult'])->name('test.result');
    Route::get('/results/{result}/details', [TestSessionController::class, 'showDetailedResult'])->name('results.details');

    Route::patch('/tests/{test}/update-settings', [TestController::class, 'updateSettings'])->name('tests.updateSettings');
    Route::post('/tests/{test}/upload-image', [TestController::class, 'uploadImage'])->name('tests.uploadImage');
    Route::get('/catalog', [PublicTestController::class, 'index'])->name('tests.catalog');

    Route::get('/tests/{test}/manual-review', [TestController::class, 'manualReviewIndex'])->name('tests.manual-index');
    Route::get('/results/{result}/review', [TestController::class, 'manualReviewShow'])->name('results.review');
    Route::post('/results/{result}/update-score', [TestController::class, 'updateManualScore'])->name('results.update-score');

    Route::get('/tests/{test}/settings', [TestController::class, 'settings'])->name('tests.settings');
    Route::patch('/tests/{test}/settings', [TestController::class, 'updateGeneralSettings'])->name('tests.settings.update');

    Route::get('/tests/{test}/start-page', [TestController::class, 'startPage'])->name('tests.start-page');
    Route::patch('/tests/{test}/start-page', [TestController::class, 'updateStartPage'])->name('tests.start-page.update');

    Route::get('/tests/{test}/statistics', [TestController::class, 'statistics'])->name('tests.statistics');

    Route::post('/disciplines', [TestController::class, 'storeDiscipline'])->name('disciplines.store');

    // Экспорт результатов в Excel
    Route::get('/tests/{test}/export-excel', [TestController::class, 'exportExcel'])->name('tests.export-excel');

    // Экспорт ответов в pdf
    Route::get('/results/{result}/question/{question}/pdf', [TestController::class, 'exportQuestionPdf'])->name('results.question-pdf');
    Route::get('/results/{result}/pdf', [TestController::class, 'exportPdf'])->name('results.pdf');

});
Route::middleware('guest')->group(function () {
    // Страница ввода почты
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    // Обработка отправки ссылки
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    // Страница установки нового пароля (из ссылки в письме)
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    // Обработка обновления пароля
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});
