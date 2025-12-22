<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=0">
    <title>Santa Claus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #000;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
            overflow: hidden;
            height: 100dvh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .phone-container {
            background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%);
            border-radius: 48px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8), inset 0 0 0 12px #1a1a1a;
            border: 8px solid #000;
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            height: 96%;
            margin-top: -15px;
        }
        .status-bar {
            height: 40px;
            backdrop-filter: blur(20px);
            background: rgba(0, 0, 0, 0.3);
        }
        .notch {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 160px;
            height: 28px;
            background: #000;
            border-radius: 0 0 20px 20px;
            z-index: 50;
        }
        .call-btn {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .end-call-btn {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #FF3B30;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(255, 59, 48, 0.3);
        }
        .avatar-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.1; transform: scale(1.1); }
        }
        .listening-pulse {
            animation: listening-pulse 1.5s ease-in-out infinite;
        }
        @keyframes listening-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(52, 199, 89, 0.7); }
            50% { box-shadow: 0 0 0 15px rgba(52, 199, 89, 0); }
        }
    </style>
</head>
<body>
<div class="phone-container">
    <div class="notch"></div>

    <div class="status-bar flex items-center justify-between px-8 text-white text-xs font-semibold relative z-40">
        <div id="status-time" class="font-semibold">9:41</div>
        <div class="flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="white" viewBox="0 0 24 24"><path d="M12.01 21.49L23.64 7c-.45-.34-4.93-4-11.64-4C5.28 3 .81 6.66.36 7l11.63 14.49.01.01.01-.01z"/></svg>
            <div class="w-5 h-2.5 border border-white/50 rounded-sm relative">
                <div class="absolute inset-y-0 left-0 bg-white w-3 m-0.5"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center h-full pt-4 pb-40 px-6">
        <div class="text-center">
            <h1 class="text-white text-3xl font-normal tracking-tight">Santa Claus</h1>
            <p id="call-timer" class="text-gray-400 text-base">00:00</p>
        </div>

        <div class="relative my-4">
            <div id="listening-ring" class="absolute inset-0 rounded-full bg-green-500 opacity-20 avatar-ring" style="display: none;"></div>
            <div id="avatar-pulse" class="w-32 h-32 rounded-full flex items-center justify-center text-6xl shadow-2xl" style="background: linear-gradient(135deg, #E53E3E 0%, #C53030 100%);">
                🎅
            </div>
        </div>

        <div class="text-center mb-2">
            <p id="status-text" class="text-white text-base font-normal">Tap Talk to Start</p>
            <p id="sub-status" class="text-gray-500 text-sm">mobile</p>
        </div>

        <div class="flex flex-col items-center w-full mt-auto">
            <div class="grid grid-cols-3 gap-x-8 gap-y-6 mb-8 w-full max-w-xs px-2">
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v1a7 7 0 0 1-14 0v-1"></path></svg>
                    </button>
                    <span class="text-white text-[10px] mt-1 font-medium">mute</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><circle cx="4" cy="4" r="2"/><circle cx="12" cy="4" r="2"/><circle cx="20" cy="4" r="2"/><circle cx="4" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="20" cy="12" r="2"/><circle cx="4" cy="20" r="2"/><circle cx="12" cy="20" r="2"/><circle cx="20" cy="20" r="2"/></svg>
                    </button>
                    <span class="text-white text-[10px] mt-1 font-medium">keypad</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                    </button>
                    <span class="text-white text-[10px] mt-1 font-medium">speaker</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                    <span class="text-white text-[10px] mt-1 font-medium">add call</span>
                </div>
                <div class="flex flex-col items-center">
                    <button id="talk-btn" class="call-btn bg-green-600 border-none">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/><path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/></svg>
                    </button>
                    <span id="talk-label" class="text-white text-[10px] mt-1 font-medium">talk</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </button>
                    <span class="text-white text-[10px] mt-1 font-medium">contacts</span>
                </div>
            </div>

            <button id="end-call" class="end-call-btn">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="white" transform="rotate(135)">
                    <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    const OPENAI_API_KEY = '{{ env('OPENAI_API_KEY') }}';
    const ELEVENLABS_API_KEY = '{{ env('ELEVENLABS_API_KEY') }}';

    const talkBtn = document.getElementById('talk-btn');
    const talkLabel = document.getElementById('talk-label');
    const statusText = document.getElementById('status-text');
    const subStatus = document.getElementById('sub-status');
    const listeningRing = document.getElementById('listening-ring');
    const avatarPulse = document.getElementById('avatar-pulse');
    const endCallBtn = document.getElementById('end-call');
    const callTimer = document.getElementById('call-timer');
    const statusTime = document.getElementById('status-time');

    let recognition;
    let isListening = false;
    let isSantaSpeaking = false;
    let conversationHistory = [];
    let callStartTime = null;
    let timerInterval = null;
    let lastTranscript = "";

    const santaVoice = new Audio();
    santaVoice.preload = "auto";

    const primeAudio = () => {
        santaVoice.play().then(() => {
            santaVoice.pause();
            santaVoice.currentTime = 0;
        }).catch(() => {});
    };

    function updateStatusTime() {
        const now = new Date();
        statusTime.textContent = `${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;
    }
    updateStatusTime();

    function updateCallTimer() {
        if (callStartTime) {
            const elapsed = Math.floor((Date.now() - callStartTime) / 1000);
            callTimer.textContent = `${Math.floor(elapsed / 60).toString().padStart(2, '0')}:${(elapsed % 60).toString().padStart(2, '0')}`;
        }
    }

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = true; // Changed to true to keep it alive in noise
        recognition.interimResults = true;
        recognition.lang = 'en-US';

        recognition.onstart = () => {
            isListening = true;
            listeningRing.style.display = 'block';
            avatarPulse.classList.add('listening-pulse');
            statusText.textContent = 'Listening...';
            talkBtn.classList.replace('bg-green-600', 'bg-red-600');
            talkLabel.textContent = 'done'; // Visual cue for noise fallback
        };

        recognition.onresult = (event) => {
            // Keep track of what we hear
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    lastTranscript = event.results[i][0].transcript;
                    // If room is quiet, it will trigger naturally here
                    processVoiceResult(lastTranscript);
                } else {
                    // Interim results can go here if needed
                    lastTranscript = event.results[i][0].transcript;
                }
            }
        };

        recognition.onerror = (e) => {
            console.error("Speech Error", e);
            if (isListening) stopListening();
        };

        recognition.onend = () => {
            // Only restart if we didn't explicitly stop to process
            if (isListening && !isSantaSpeaking) recognition.start();
        };
    }

    function processVoiceResult(text) {
        if (!text || !isListening) return;
        conversationHistory.push({ role: 'user', content: text });
        getSantaResponse();
    }

    talkBtn.addEventListener('click', () => {
        primeAudio();
        if (!callStartTime) {
            callStartTime = Date.now();
            timerInterval = setInterval(updateCallTimer, 1000);
        }

        if (isListening) {
            // NOISE FALLBACK: Manual stop triggers processing of whatever was heard last
            if (lastTranscript) {
                processVoiceResult(lastTranscript);
            } else {
                stopListening();
            }
        } else {
            startListening();
        }
    });

    endCallBtn.addEventListener('click', () => {
        stopEverything();
        clearInterval(timerInterval);
        statusText.textContent = 'Call Ended';
        setTimeout(() => {
            conversationHistory = [];
            callStartTime = null;
            callTimer.textContent = '00:00';
            statusText.textContent = 'Tap Talk to Start';
        }, 3000);
    });

    function startListening() {
        lastTranscript = "";
        if (recognition && !isSantaSpeaking) recognition.start();
    }

    function stopListening() {
        isListening = false;
        listeningRing.style.display = 'none';
        avatarPulse.classList.remove('listening-pulse');
        if (recognition) { try { recognition.abort(); } catch(e) {} }
        statusText.textContent = 'Ready';
        talkBtn.classList.replace('bg-red-600', 'bg-green-600');
        talkLabel.textContent = 'talk';
    }

    function stopEverything() {
        stopListening();
        isSantaSpeaking = false;
        santaVoice.pause();
        santaVoice.currentTime = 0;
    }

    async function getSantaResponse() {
        stopListening();
        isSantaSpeaking = true;
        statusText.textContent = 'Santa thinking...';

        try {
            const aiResponse = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${OPENAI_API_KEY}`, 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    model: 'gpt-3.5-turbo',
                    messages: [{ role: 'system', content: `You are Santa. Warm, jolly, 2 sentences max. End with a question.` }, ...conversationHistory.length > 0 ? conversationHistory : [{ role: 'user', content: 'Hello!' }]]
                })
            });

            const aiData = await aiResponse.json();
            const santaText = aiData.choices[0].message.content;
            conversationHistory.push({ role: 'assistant', content: santaText });

            const voiceResponse = await fetch(`https://api.elevenlabs.io/v1/text-to-speech/1wg2wOjdEWKA7yQD8Kca`, {
                method: 'POST',
                headers: { 'xi-api-key': ELEVENLABS_API_KEY, 'Content-Type': 'application/json' },
                body: JSON.stringify({ text: santaText, model_id: 'eleven_turbo_v2_5' })
            });

            const audioBlob = await voiceResponse.blob();
            const audioUrl = URL.createObjectURL(audioBlob);

            santaVoice.src = audioUrl;

            santaVoice.onended = () => {
                isSantaSpeaking = false;
                statusText.textContent = 'Your turn';
                lastTranscript = ""; // Reset for next turn
                startListening();
                URL.revokeObjectURL(audioUrl);
            };

            santaVoice.play().then(() => {
                statusText.textContent = 'Santa speaking';
            }).catch(error => {
                statusText.textContent = 'Tap to Listen';
                window.addEventListener('touchstart', () => santaVoice.play(), {once: true});
            });

        } catch (e) {
            isSantaSpeaking = false;
            statusText.textContent = 'Error';
        }
    }
</script>
</body>
</html>