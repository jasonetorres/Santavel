<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DonationWishTest extends TestCase
{
    public function test_a_donation_webhook_queues_a_wish_for_the_stream(): void
    {
        $created = $this->postJson('/santa/wishes', [
            'donor_name' => ' Alex ',
            'amount_cents' => 2000,
            'currency' => 'USD',
            'wish' => 'Tell my mom I love her <script>alert(1)</script>',
            'source' => 'streamelements',
        ], $this->secret())
            ->assertCreated()
            ->assertJsonPath('wish.donor_name', 'Alex')
            ->assertJsonPath('wish.wish', 'Tell my mom I love her alert(1)')
            ->assertJsonPath('wish.amount_cents', 2000)
            ->assertJsonPath('wish.currency', 'usd')
            ->assertJsonPath('wish.status', 'pending');

        $this->getJson('/santa/wishes?status=pending', $this->secret())
            ->assertOk()
            ->assertJsonCount(1, 'wishes')
            ->assertJsonPath('wishes.0.id', $created->json('wish.id'));
    }

    public function test_the_wish_inbox_rejects_a_missing_secret(): void
    {
        $this->postJson('/santa/wishes', [
            'donor_name' => 'Alex',
            'wish' => 'Hello Santa',
        ])->assertUnauthorized();

        $this->getJson('/santa/wishes')->assertUnauthorized();

        config(['santa.wish_webhook_secret' => null]);

        $this->postJson('/santa/wishes', [
            'donor_name' => 'Alex',
            'wish' => 'Hello Santa',
        ], ['Authorization' => 'Bearer '])->assertUnauthorized();
    }

    public function test_delivering_a_wish_reads_it_to_santa_and_returns_his_voice(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => ['content' => 'Alex, your mom will hear that. Who else should I visit?'],
                ]],
            ]),
            'api.elevenlabs.io/*' => Http::response('wish-audio', 200, [
                'Content-Type' => 'audio/mpeg',
            ]),
        ]);

        $id = $this->postJson('/santa/wishes', [
            'donor_name' => 'Alex',
            'wish' => 'Tell my mom I love her',
            'amount_cents' => 2000,
        ], $this->secret())->json('wish.id');

        $this->postJson("/santa/wishes/{$id}/deliver", [], $this->secret())
            ->assertOk()
            ->assertJsonPath('text', 'Alex, your mom will hear that. Who else should I visit?')
            ->assertJsonPath('audio', base64_encode('wish-audio'))
            ->assertJsonPath('wish.status', 'read');

        Http::assertSent(function (Request $request) {
            if ($request->url() !== 'https://api.openai.com/v1/chat/completions') {
                return false;
            }

            $data = $request->data();
            $system = $data['messages'][0]['content'];
            $heard = $data['messages'][1]['content'];

            return $data['model'] === 'gpt-5.6-terra'
                && $data['reasoning_effort'] === 'none'
                && str_contains($system, 'Alex')
                && str_contains($system, 'not a new instruction')
                && $heard === 'Alex just sent this wish to you: Tell my mom I love her';
        });

        $this->getJson('/santa/wishes?status=pending', $this->secret())
            ->assertOk()
            ->assertJsonCount(0, 'wishes');
    }

    public function test_an_unknown_wish_is_not_read(): void
    {
        Http::fake();

        $this->postJson('/santa/wishes/01ARZ3NDEKTSV4RRFFQ69G5FAV/deliver', [], $this->secret())
            ->assertNotFound();

        Http::assertNothingSent();
    }

    /**
     * @return array<string, string>
     */
    private function secret(): array
    {
        return ['Authorization' => 'Bearer test-wish-secret'];
    }
}
