<?php

namespace Tests\Feature;

use Tests\TestCase;

class SantaPageTest extends TestCase
{
    public function test_the_home_page_opens_the_phone(): void
    {
        $this->get('/')->assertRedirect('/santa');
    }

    public function test_the_phone_page_boots_without_a_database(): void
    {
        $this->get('/santa')
            ->assertOk()
            ->assertSee('Santa Claus')
            ->assertSee('/santa/transcribe', false)
            ->assertSee('/santa/reply', false)
            ->assertSee('csrf-token', false)
            ->assertDontSee('test-openai-key', false)
            ->assertDontSee('test-eleven-key', false)
            ->assertDontSee('api.openai.com', false)
            ->assertDontSee('api.elevenlabs.io', false)
            ->assertDontSee('gpt-3.5-turbo', false)
            ->assertDontSee('whisper-1', false)
            ->assertDontSee("env('", false);
    }

    public function test_the_health_check_responds(): void
    {
        $this->get('/up')->assertOk();
    }
}
