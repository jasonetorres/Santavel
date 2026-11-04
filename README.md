# Santavel

Call Santa is the phone line for Jason Torres' Christmas charity stream. Viewers open `/santa`, hold the green button, and talk to Santa. The app sends the audio to OpenAI, asks Santa for a short reply, and speaks it with ElevenLabs.

The December 18, 2026 stream is a freeCodeCamp fundraiser. A donation wish can be queued and read to Santa on air. That queue is a small API, not a finished overlay.

## What you need on Laravel Cloud

Set the environment runtime to **PHP 8.4 or 8.5**. The lockfile uses Symfony 8, which does not install on PHP 8.3. New Laravel Cloud environments default to PHP 8.5.

Laravel Cloud writes these variables into `.env` on the server. `APP_KEY` is generated for you on the first deploy. If this environment was created earlier and `APP_KEY` is empty, generate one locally with `php artisan key:generate --show` and paste it in.

| Variable | Required | Purpose |
| --- | --- | --- |
| `APP_KEY` | Yes | Encrypts the session cookie. Cloud can generate it. |
| `APP_ENV` | Yes | `production` |
| `APP_DEBUG` | Yes | `false` |
| `APP_URL` | Yes | The public Cloud URL, including `https://` |
| `OPENAI_API_KEY` | Yes | Transcription and Santa's replies. Server-side only. |
| `ELEVENLABS_API_KEY` | Yes | Santa's voice. Server-side only. |
| `SANTA_WISH_WEBHOOK_SECRET` | For wishes | Long random string. Donation tools send it as `Authorization: Bearer`. |
| `OPENAI_CHAT_MODEL` | No | Default `gpt-5.6-terra`. `gpt-3.5-turbo` was shut down on October 23, 2026. |
| `OPENAI_TRANSCRIBE_MODEL` | No | Default `gpt-transcribe`. `whisper-1` still answers until February 26, 2027, then shuts down. |
| `ELEVENLABS_VOICE_ID` | No | Default `1wg2wOjdEWKA7yQD8Kca`, the voice from the 2025 stream. Change it if that voice is gone from the ElevenLabs account. |
| `ELEVENLABS_MODEL` | No | Default `eleven_flash_v2_5`, the current low-latency replacement for `eleven_turbo_v2_5`. |
| `SESSION_DRIVER` | No | Default `cookie`. Do not set this to `database` unless a `sessions` table exists. |
| `CACHE_STORE` | No | Default `file`. See wishes below if you attach a Cloud cache. |
| `QUEUE_CONNECTION` | No | Default `sync`. Nothing is queued. |
| `DB_CONNECTION` | No | Default `sqlite`, so the default migrate command can exit without a database server. |

Do not put `OPENAI_API_KEY` or `ELEVENLABS_API_KEY` in the page. The browser only talks to this app.

No database, cache, or queue resource is required for the phone line. Attach one only for the cases in the deploy steps.

## Deploy

In the environment settings:

1. Runtime: PHP **8.4** or **8.5**.
2. Build command:

```sh
composer install --no-dev --no-interaction --prefer-dist
npm ci
npm run build
mkdir -p database storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
touch database/database.sqlite
php artisan optimize
```

3. Deploy command:

```sh
php artisan migrate --force
```

There are no migrations yet. The command still has to succeed, which it does against the SQLite file created during the build. `php artisan optimize` caches config. That is safe because `env()` is only read from config files. It is also why the old page broke: the Blade view called `env()` itself, and a cached config turns those calls into empty strings.

4. Add the variables in the table above, then deploy.

Cloud does not keep files written by the deploy command. Wishes live in the cache, under `storage/`, for the life of the running instance. That is enough for a single instance on stream night. Attach a Laravel Cloud cache if a restart or a second instance must see the same queue, and leave `CACHE_STORE` as the value Cloud injects.

If you later attach Postgres or MySQL, set `DB_CONNECTION` to `pgsql` or `mysql` as well. Cloud injects `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`, and it does not by itself change `DB_CONNECTION` away from `sqlite`.

## Local

PHP 8.4 or newer, Composer, and Node 22.

```sh
composer install
cp .env.example .env
php artisan key:generate
mkdir -p database && touch database/database.sqlite
npm install
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000/santa`. The phone UI loads without provider keys. Holding the button returns a configuration error until `OPENAI_API_KEY` and `ELEVENLABS_API_KEY` are set.

```sh
composer test
```

## Donation wishes

Viewers who donate get their wish read to Santa during the stream. The inbox stores the wish. It does not take payment, and it does not play audio in OBS by itself.

Your donation tool (StreamElements, Streamlabs, Ko-fi, a freeCodeCamp form, or a relay in front of one of those) POSTs the wish:

```sh
curl -X POST "$APP_URL/santa/wishes" \
  -H "Authorization: Bearer $SANTA_WISH_WEBHOOK_SECRET" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"donor_name":"Alex","amount_cents":2000,"currency":"usd","wish":"Tell my mom I love her","source":"streamelements"}'
```

The queue:

```sh
curl "$APP_URL/santa/wishes?status=pending" \
  -H "Authorization: Bearer $SANTA_WISH_WEBHOOK_SECRET" \
  -H "Accept: application/json"
```

When you are ready for Santa to hear one on air, deliver it. The response is Santa's reply and a base64 MP3 in the same voice as the phone line. Play that audio on stream. Delivering a wish marks it read. Calling deliver again speaks it again, which is the replay if the first playback fails.

```sh
curl -X POST "$APP_URL/santa/wishes/WISH_ID/deliver" \
  -H "Authorization: Bearer $SANTA_WISH_WEBHOOK_SECRET" \
  -H "Accept: application/json"
```

Field names on StreamElements and the other tools do not match this body. Map them in that tool, or in a few lines of glue, before they hit `/santa/wishes`. The wish text is stored with tags stripped and is sent to Santa as something a viewer said, not as a new instruction.

A browser source or Streamer.bot button can call `deliver` and play `audio`. That player is the part left for stream week.

Call audio and transcripts are not stored here. They are sent to OpenAI, and Santa's reply text is sent to ElevenLabs, only to answer that turn.

## Manual checklist before December 18

- Confirm the ElevenLabs voice `1wg2wOjdEWKA7yQD8Kca` still exists on the account. If it does not, set `ELEVENLABS_VOICE_ID`.
- Put fresh OpenAI and ElevenLabs keys in the Cloud environment. Rotate any key that was previously rendered into the phone page.
- Set `SANTA_WISH_WEBHOOK_SECRET` and point the donation tool at `POST /santa/wishes`.
- Place one real test call from a phone, and deliver one test wish, on the Cloud URL.
- The Supabase function in `supabase/functions/santa-voice` is not what Laravel Cloud runs. The phone page does not call it. Leave it undeployed unless you still have a client pointed at it. Its models were updated in case you do.
