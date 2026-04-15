<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use App\Models\Test;
use Illuminate\Http\Request;

class PublicTestController extends Controller
{
    public function index(Request $request)
    {
        $disciplines = Discipline::withCount('tests')->get();

        $query = Test::with(['results', 'discipline']);

        if ($request->has('discipline_id')) {
            $query->where('discipline_id', $request->discipline_id);
        }

        $tests = $query->latest()->get();

        return view('tests.index', compact('tests', 'disciplines'));
    }
}
