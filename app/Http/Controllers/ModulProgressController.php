<?php

namespace App\Http\Controllers;

use App\Models\ModulProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ModulProgressController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->modulProgresses()->with('modul')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'modul_pembelajaran_id' => ['required', 'exists:modul_pembelajarans,id'],
        ]);

        $progress = ModulProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'modul_pembelajaran_id' => $validated['modul_pembelajaran_id'],
            ],
            [
                'selesai' => true,
                'completed_at' => Carbon::now(),
            ]
        );

        return response()->json([
            'message' => 'Progress modul berhasil disimpan.',
            'data' => $progress->load('modul'),
        ], 201);
    }
}
