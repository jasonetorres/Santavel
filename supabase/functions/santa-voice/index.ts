import "jsr:@supabase/functions-js/edge-runtime.d.ts";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
  "Access-Control-Allow-Headers": "Content-Type, Authorization, X-Client-Info, Apikey",
};

const ELEVENLABS_API_KEY = Deno.env.get('ELEVENLABS_API_KEY');
const OPENAI_API_KEY = Deno.env.get('OPENAI_API_KEY');

interface Message {
  role: string;
  content: string;
}

Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, {
      status: 200,
      headers: corsHeaders,
    });
  }

  try {
    const { messages }: { messages: Message[] } = await req.json();

    // Get AI response from OpenAI
    const aiResponse = await fetch('https://api.openai.com/v1/chat/completions', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${OPENAI_API_KEY}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        model: 'gpt-4',
        messages: messages,
        max_tokens: 150,
        temperature: 0.9,
      }),
    });

    if (!aiResponse.ok) {
      throw new Error('Failed to get AI response');
    }

    const aiData = await aiResponse.json();
    const santaText = aiData.choices[0].message.content;

    // Convert to speech using ElevenLabs
    const voiceResponse = await fetch(
      `https://api.elevenlabs.io/v1/text-to-speech/1wg2wOjdEWKA7yQD8Kca`,
      {
        method: 'POST',
        headers: {
          'Accept': 'audio/mpeg',
          'Content-Type': 'application/json',
          'xi-api-key': ELEVENLABS_API_KEY!,
        },
        body: JSON.stringify({
          text: santaText,
          model_id: 'eleven_turbo_v2_5',
          voice_settings: {
            stability: 0.7,
            similarity_boost: 0.8,
            style: 0.5,
            use_speaker_boost: true,
          },
        }),
      }
    );

    if (!voiceResponse.ok) {
      throw new Error('Failed to generate voice');
    }

    const audioBuffer = await voiceResponse.arrayBuffer();
    const base64Audio = btoa(String.fromCharCode(...new Uint8Array(audioBuffer)));

    return new Response(
      JSON.stringify({
        text: santaText,
        audio: base64Audio,
      }),
      {
        headers: {
          ...corsHeaders,
          'Content-Type': 'application/json',
        },
      }
    );
  } catch (error) {
    console.error('Error:', error);
    return new Response(
      JSON.stringify({ error: error.message }),
      {
        status: 500,
        headers: {
          ...corsHeaders,
          'Content-Type': 'application/json',
        },
      }
    );
  }
});