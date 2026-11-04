<?php

namespace App\Santa;

use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SantaVoice
{
    /**
     * @return array{text: string, audio: string}
     */
    public function reply(array $turns, ?string $system = null): array
    {
        $text = $this->complete($turns, $system ?? SantaPrompt::conversation());

        return [
            'text' => $text,
            'audio' => $this->speak($text),
        ];
    }

    /**
     * @return array{text: string, audio: string}
     */
    public function deliver(DonationWish $wish): array
    {
        return $this->reply([
            ['role' => 'user', 'content' => SantaPrompt::heardWish($wish)],
        ], SantaPrompt::wish($wish));
    }

    public function transcribe(UploadedFile $audio): string
    {
        $response = Http::withToken($this->secret('santa.openai_key', 'an OpenAI API key'))
            ->timeout(25)
            ->attach(
                'file',
                file_get_contents($audio->getRealPath()) ?: '',
                $audio->getClientOriginalName() ?: 'audio.webm',
                ['Content-Type' => $audio->getMimeType() ?: 'audio/webm'],
            )
            ->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => (string) config('santa.openai_transcribe_model'),
            ]);

        $this->guard($response, 'OpenAI');

        return trim((string) $response->json('text'));
    }

    /**
     * @param  list<array{role: string, content: string}>  $turns
     */
    private function complete(array $turns, string $system): string
    {
        $messages = [
            ['role' => 'system', 'content' => $system],
        ];

        foreach ($turns as $turn) {
            $messages[] = [
                'role' => $turn['role'],
                'content' => $turn['content'],
            ];
        }

        $response = Http::withToken($this->secret('santa.openai_key', 'an OpenAI API key'))
            ->timeout(25)
            ->acceptJson()
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => (string) config('santa.openai_chat_model'),
                // Terra's default effort is medium, which is too slow for a live call.
                'reasoning_effort' => 'none',
                'max_completion_tokens' => 300,
                'messages' => $messages,
            ]);

        $this->guard($response, 'OpenAI');

        return $this->text($response->json('choices.0.message') ?? []);
    }

    private function speak(string $text): string
    {
        $voiceId = (string) config('santa.elevenlabs_voice_id');

        $response = Http::withHeaders([
            'xi-api-key' => $this->secret('santa.elevenlabs_key', 'an ElevenLabs API key'),
            'Accept' => 'audio/mpeg',
        ])
            ->timeout(30)
            ->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}", [
                'text' => $text,
                'model_id' => (string) config('santa.elevenlabs_model'),
                'voice_settings' => [
                    'stability' => 0.7,
                    'similarity_boost' => 0.8,
                    'style' => 0.5,
                    'use_speaker_boost' => true,
                ],
            ]);

        $this->guard($response, 'ElevenLabs');

        $audio = $response->body();

        if ($audio === '') {
            throw new SantaException('Santa could not speak just then. Please try again.');
        }

        return base64_encode($audio);
    }

    /**
     * @param  array<string, mixed>  $message
     */
    private function text(array $message): string
    {
        if (! empty($message['refusal'])) {
            throw new SantaException('Santa could not answer that.');
        }

        $content = $message['content'] ?? '';

        if (is_array($content)) {
            $content = collect($content)
                ->map(fn ($part) => is_array($part) ? (string) ($part['text'] ?? '') : (string) $part)
                ->implode('');
        }

        $content = trim((string) $content);

        if ($content === '') {
            throw new SantaException('Santa could not think of a reply.');
        }

        return $content;
    }

    private function secret(string $key, string $label): string
    {
        $value = (string) config($key);

        if ($value === '') {
            throw new SantaException("Santa is missing {$label}.", 503);
        }

        return $value;
    }

    private function guard(Response $response, string $provider): void
    {
        if ($response->successful()) {
            return;
        }

        Log::warning('Santa provider request failed', [
            'provider' => $provider,
            'status' => $response->status(),
        ]);

        $status = $response->status();

        if (in_array($status, [401, 403], true)) {
            $setting = $provider === 'ElevenLabs' ? 'ELEVENLABS_API_KEY' : 'OPENAI_API_KEY';

            throw new SantaException("The {$provider} key was rejected. Check {$setting} on Laravel Cloud.");
        }

        if ($status === 404 || $status === 400) {
            $setting = $provider === 'ElevenLabs'
                ? 'ELEVENLABS_VOICE_ID and ELEVENLABS_MODEL'
                : 'OPENAI_CHAT_MODEL and OPENAI_TRANSCRIBE_MODEL';

            throw new SantaException("{$provider} rejected the request. Check {$setting}.");
        }

        throw new SantaException('Santa could not answer just then. Please try again.');
    }
}
