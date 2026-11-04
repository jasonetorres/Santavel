<?php

namespace App\Http\Controllers;

use App\Santa\SantaException;
use App\Santa\SantaVoice;
use App\Santa\WishInbox;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishController extends Controller
{
    public function index(Request $request, WishInbox $inbox): JsonResponse
    {
        $wishes = $request->query('status') === 'pending'
            ? $inbox->pending()
            : $inbox->all();

        return response()->json([
            'wishes' => array_map(fn ($wish) => $wish->toArray(), $wishes),
        ]);
    }

    public function store(Request $request, WishInbox $inbox): JsonResponse
    {
        $validated = $request->validate([
            'donor_name' => ['required', 'string', 'max:80'],
            'wish' => ['required', 'string', 'max:500'],
            'amount_cents' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'currency' => ['nullable', 'string', 'size:3'],
            'source' => ['nullable', 'string', 'max:40'],
        ]);

        $donor = $this->plain($validated['donor_name']);
        $wish = $this->plain($validated['wish']);

        if ($donor === '' || $wish === '') {
            return response()->json([
                'message' => 'The wish needs a name and a message.',
            ], 422);
        }

        $donation = $inbox->add(
            donorName: $donor,
            wish: $wish,
            amountCents: $validated['amount_cents'] ?? null,
            currency: $validated['currency'] ?? 'usd',
            source: $this->plain($validated['source'] ?? 'webhook') ?: 'webhook',
        );

        return response()->json(['wish' => $donation->toArray()], 201);
    }

    public function deliver(string $wish, WishInbox $inbox, SantaVoice $santa): JsonResponse
    {
        $donation = $inbox->find($wish);

        if ($donation === null) {
            return response()->json(['message' => 'That wish is not in the queue.'], 404);
        }

        try {
            $reply = $santa->deliver($donation);
        } catch (SantaException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->status);
        }

        $donation = $inbox->markRead($donation->id) ?? $donation;

        return response()->json([
            ...$reply,
            'wish' => $donation->toArray(),
        ]);
    }

    private function plain(string $value): string
    {
        $stripped = strip_tags($value);
        $collapsed = preg_replace('/\s+/', ' ', $stripped) ?? '';

        return trim($collapsed);
    }
}
