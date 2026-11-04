<?php

namespace App\Http\Controllers;

use App\Santa\SantaException;
use App\Santa\SantaVoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SantaController extends Controller
{
    public function index(): View
    {
        return view('santa');
    }

    public function transcribe(Request $request, SantaVoice $santa): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:8192'],
        ]);

        try {
            $text = $santa->transcribe($request->file('file'));
        } catch (SantaException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->status);
        }

        return response()->json(['text' => $text]);
    }

    public function reply(Request $request, SantaVoice $santa): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:16'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:1000'],
        ]);

        $latest = $validated['messages'][array_key_last($validated['messages'])];

        if ($latest['role'] !== 'user') {
            return response()->json([
                'message' => 'The latest message has to be from the caller.',
            ], 422);
        }

        try {
            $reply = $santa->reply($validated['messages']);
        } catch (SantaException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->status);
        }

        return response()->json($reply);
    }
}
