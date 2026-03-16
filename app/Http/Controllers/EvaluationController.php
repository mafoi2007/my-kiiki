<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $selectedClassId = $request->integer('school_class_id');

        $classes = SchoolClass::query()
            ->with('level')
            ->orderBy('level_id')
            ->orderBy('name')
            ->get();

        $evaluations = collect();

        if ($selectedClassId > 0) {
            $evaluations = Evaluation::query()
                ->where('school_class_id', $selectedClassId)
                ->orderBy('sequence_number')
                ->get()
                ->keyBy('sequence_number');
        }

        return view('evaluations.index', [
            'classes' => $classes,
            'evaluations' => $evaluations,
            'selectedClassId' => $selectedClassId,
            'availableSequences' => range(1, 6),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'sequence_number' => ['required', 'integer', 'between:1,6'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_open' => ['nullable', 'boolean'],
        ]);

        Evaluation::updateOrCreate(
            [
                'school_class_id' => $data['school_class_id'],
                'sequence_number' => $data['sequence_number'],
            ],
            [
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'is_open' => (bool) ($data['is_open'] ?? false),
            ]
        );

        return back()->with('success', 'Séquence enregistrée.');
    }

    public function update(Request $request, Evaluation $evaluation): RedirectResponse
    {
        $data = $request->validate([
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_open' => ['nullable', 'boolean'],
        ]);

        $evaluation->update([
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_open' => (bool) ($data['is_open'] ?? false),
        ]);

        return back()->with('success', 'Séquence mise à jour.');
    }
}