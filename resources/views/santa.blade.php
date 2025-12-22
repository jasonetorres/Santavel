<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Call Santa 🎅</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .phone-container {
            background: #000;
            border-radius: 48px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            border: 12px solid #1a1a1a;
            position: relative;
        }
        .notch {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 28px;
            background: #000;
            border-radius: 0 0 20px 20px;
            z-index: 10;
        }
        .talk-btn {
            transition: all 0.3s ease;
        }
        .talk-btn:active {
            transform: scale(0.95);
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .control-btn {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .control-btn:active {
            transform: scale(0.9);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="phone-container w-full max-w-sm mx-auto" style="height: 650px;">
        <div class="notch"></div>

        <div class="flex flex-col items-center justify-between h-full p-6 pt-10">
            <!-- Header -->
            <div class="text-center">
                <p class="text-white text-xs mb-2">Phone a Friend</p>
                <h1 class="text-white text-2xl font-semibold">Santa Claus 🎅</h1>
                <p class="text-gray-400 text-xs mt-1">North Pole, Arctic</p>
            </div>

            <!-- Santa Avatar -->
            <div class="relative">
                <div class="w-28 h-28 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-6xl shadow-2xl">
                    🎅
                </div>
                <div id="listening-indicator" class="absolute inset-0 rounded-full border-4 border-green-400 pulse" style="display: none;"></div>
            </div>

            <!-- Call Status -->
            <div class="text-center">
                <p id="status-text" class="text-white text-lg mb-1">Ready to call</p>
                <p id="sub-status" class="text-gray-400 text-sm">Press TALK to speak with Santa</p>
            </div>

            <!-- Talk Button -->
            <button id="talk-btn" class="talk-btn w-24 h-24 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-2xl text-white font-bold text-sm">
                TALK
            </button>

            <!-- Control Buttons -->
            <div class="grid grid-cols-3 gap-8 w-full max-w-xs">
                <button class="control-btn bg-gray-800 text-white text-xl">🔇</button>
                <button class="control-btn bg-gray-800 text-white text-xl">⏸️</button>
                <button class="control-btn bg-gray-800 text-white text-xl">🔊</button>
            </div>

            <!-- End Call Button -->
            <button id="end-call" class="control-btn bg-red-600 text-white text-2xl mx-auto">
                📞
            </button>
        </div>
    </div>

    <script>
        const SUPABASE_URL = '{{ env('VITE_SUPABASE_URL') }}';
        const SUPABASE_ANON_KEY = '{{ env('VITE_SUPABASE_SUPABASE_ANON_KEY') }}';

        const talkBtn = document.getElementById('talk-btn');
        const statusText = document.getElementById('status-text');
        const subStatus = document.getElementById('sub-status');
        const listeningIndicator = document.getElementById('listening-indicator');
        const endCallBtn = document.getElementById('end-call');

        let recognition;
        let isListening = false;
        let isSantaSpeaking = false;
        let conversationHistory = [];
        let audioQueue = [];
        let currentAudio = null;

        // Initialize speech recognition
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';

            recognition.onstart = () => {
                isListening = true;
                listeningIndicator.style.display = 'block';
                statusText.textContent = 'Listening...';
                subStatus.textContent = 'Speak now';
                talkBtn.style.background = 'linear-gradient(to bottom right, #ef4444, #dc2626)';
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                console.log('Heard:', transcript);
                conversationHistory.push({ role: 'user', content: transcript });
                getSantaResponse(transcript);
            };

            recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                stopListening();
                if (event.error !== 'no-speech') {
                    setTimeout(startListening, 500);
                }
            };

            recognition.onend = () => {
                if (isListening && !isSantaSpeaking) {
                    recognition.start();
                }
            };
        }

        talkBtn.addEventListener('click', () => {
            if (!isListening) {
                startListening();
            } else {
                stopListening();
            }
        });

        endCallBtn.addEventListener('click', () => {
            stopEverything();
            statusText.textContent = 'Call Ended';
            subStatus.textContent = 'Ho ho ho! Merry Christmas!';
            setTimeout(() => {
                conversationHistory = [];
                statusText.textContent = 'Ready to call';
                subStatus.textContent = 'Press TALK to speak with Santa';
            }, 3000);
        });

        function startListening() {
            if (recognition && !isSantaSpeaking) {
                // First time calling? Santa greets them!
                if (conversationHistory.length === 0) {
                    getSantaResponse('');
                } else {
                    recognition.start();
                }
            }
        }

        function stopListening() {
            isListening = false;
            listeningIndicator.style.display = 'none';
            if (recognition) {
                recognition.stop();
            }
            if (!isSantaSpeaking) {
                statusText.textContent = 'Ready to talk';
                subStatus.textContent = 'Press TALK to speak';
                talkBtn.style.background = 'linear-gradient(to bottom right, #4ade80, #16a34a)';
            }
        }

        function stopEverything() {
            stopListening();
            isSantaSpeaking = false;
            audioQueue = [];
            if (currentAudio) {
                currentAudio.pause();
                currentAudio = null;
            }
        }

        async function getSantaResponse(userMessage) {
            stopListening();
            isSantaSpeaking = true;
            statusText.textContent = 'Santa is speaking...';
            subStatus.textContent = '🎅 Ho ho ho!';

            try {
                const messages = conversationHistory.length === 0 ? [] : [
                    { role: 'system', content: `You are Santa Claus talking to a child on the phone from the North Pole. You are warm, jolly, and genuinely interested in the child.

CRITICAL RULES:
- Keep responses SHORT (2-3 sentences max) so the child can respond
- ALWAYS end with an engaging question to keep the conversation going
- Sound genuinely delighted and excited to talk to them
- Reference the elves, reindeer, Mrs. Claus, and the Nice List naturally
- Act like you've been watching them (you know if they've been good!)
- Ask about their Christmas wishes, being kind, helping at home
- Build excitement about Christmas coming
- Be personal and magical, not generic
- Encourage good behavior naturally

Example responses:
"Ho ho ho! Well hello there! I'm so happy you called! The elves just told me someone special wanted to talk to me. What's your name, dear child?"
"My goodness, what a wonderful name! I've heard such good things about you from my elves! Have you been helping out at home lately?"
"That's wonderful to hear! You know, being kind and helpful is very important to me. What would you like for Christmas this year?"` },
                    ...conversationHistory
                ];

                const response = await fetch(`${SUPABASE_URL}/functions/v1/santa-voice`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${SUPABASE_ANON_KEY}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        messages: messages.length > 0 ? messages : [{ role: 'user', content: 'Hello Santa!' }]
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to get response from Santa');
                }

                const data = await response.json();
                conversationHistory.push({ role: 'assistant', content: data.text });

                // Play the audio
                const audio = new Audio('data:audio/mpeg;base64,' + data.audio);
                currentAudio = audio;

                audio.onended = () => {
                    isSantaSpeaking = false;
                    currentAudio = null;
                    statusText.textContent = 'Your turn!';
                    subStatus.textContent = 'Santa is listening...';
                    // Auto-start listening after Santa finishes
                    setTimeout(() => {
                        if (!isSantaSpeaking) {
                            startListening();
                        }
                    }, 500);
                };

                audio.onerror = () => {
                    console.error('Audio playback error');
                    isSantaSpeaking = false;
                    currentAudio = null;
                    statusText.textContent = 'Ready to talk';
                    subStatus.textContent = 'Press TALK to try again';
                };

                audio.play();

            } catch (error) {
                console.error('Error getting Santa response:', error);
                isSantaSpeaking = false;
                statusText.textContent = 'Oops! Connection lost';
                subStatus.textContent = 'Press TALK to try again';
                talkBtn.style.background = 'linear-gradient(to bottom right, #4ade80, #16a34a)';
            }
        }
    </script>
</body>
</html>
