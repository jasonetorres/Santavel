<?php

return [

    'openai_key' => env('OPENAI_API_KEY'),

    'openai_chat_model' => env('OPENAI_CHAT_MODEL', 'gpt-5.6-terra'),

    'openai_transcribe_model' => env('OPENAI_TRANSCRIBE_MODEL', 'gpt-transcribe'),

    'elevenlabs_key' => env('ELEVENLABS_API_KEY'),

    /*
    | The voice from the 2025 charity stream. Override it if ElevenLabs has
    | removed that voice from the account.
    */
    'elevenlabs_voice_id' => env('ELEVENLABS_VOICE_ID', '1wg2wOjdEWKA7yQD8Kca'),

    'elevenlabs_model' => env('ELEVENLABS_MODEL', 'eleven_flash_v2_5'),

    'wish_webhook_secret' => env('SANTA_WISH_WEBHOOK_SECRET'),

];
