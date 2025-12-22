<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santa Claus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #000;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
            overflow: hidden;
        }
        .phone-container {
            background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%);
            border-radius: 48px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8), inset 0 0 0 12px #1a1a1a;
            border: 8px solid #000;
            position: relative;
            overflow: hidden;
        }
        .status-bar {
            height: 44px;
            backdrop-filter: blur(20px);
            background: rgba(0, 0, 0, 0.3);
        }
        .notch {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 165px;
            height: 30px;
            background: #000;
            border-radius: 0 0 20px 20px;
            z-index: 50;
        }
        .signal-dot {
            width: 3px;
            height: 10px;
            background: white;
            border-radius: 1px;
            margin-right: 1.5px;
        }
        .call-btn {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .call-btn:active {
            transform: scale(0.9);
            background: rgba(255, 255, 255, 0.15);
        }
        .call-btn-icon {
            width: 28px;
            height: 28px;
            margin-bottom: 2px;
        }
        .end-call-btn {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #FF3B30;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .end-call-btn:active {
            transform: scale(0.9);
            background: #E02B20;
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
        .battery {
            width: 26px;
            height: 12px;
            border: 2px solid white;
            border-radius: 3px;
            position: relative;
            opacity: 0.9;
        }
        .battery::after {
            content: '';
            position: absolute;
            right: -4px; top: 3px;
            width: 2px; height: 4px;
            background: white;
            border-radius: 0 2px 2px 0;
        }
        .battery-fill {
            position: absolute;
            left: 1px; top: 1px; bottom: 1px;
            width: 18px;
            background: white;
            border-radius: 1px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
<div class="phone-container w-full max-w-sm mx-auto" style="height: 812px; max-height: 90vh;">
    <div class="notch"></div>

    <div class="status-bar flex items-center justify-between px-8 text-white text-xs font-semibold relative z-40">
        <div class="flex items-center space-x-1">
            <div class="signal-dot" style="height: 5px;"></div>
            <div class="signal-dot" style="height: 7px;"></div>
            <div class="signal-dot" style="height: 9px;"></div>
            <div class="signal-dot" style="height: 10px;"></div>
            <span class="ml-1 text-xs">5G</span>
            <svg class="w-4 h-4 ml-1" fill="white" viewBox="0 0 24 24">
                <path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z"/>
            </svg>
        </div>
        <div id="status-time" class="absolute left-1/2 transform -translate-x-1/2 font-semibold">9:41</div>
        <div class="flex items-center space-x-2">
            <div class="battery"><div class="battery-fill"></div></div>
        </div>
    </div>

    <div class="flex flex-col items-center h-full pt-12 pb-24 px-6">
        <div class="text-center mb-2">
            <h1 class="text-white text-3xl font-normal mb-1 tracking-tight">Santa Claus</h1>
            <p class="text-gray-400 text-base">mobile</p>
            <div class="text-gray-400 text-base mt-1" id="call-timer">00:00</div>
        </div>

        <div class="relative my-6">
            <div id="listening-ring" class="absolute inset-0 rounded-full bg-green-500 opacity-20 avatar-ring" style="display: none;"></div>
            <div id="avatar-pulse" class="w-36 h-36 rounded-full flex items-center justify-center text-7xl" style="background: linear-gradient(135deg, #E53E3E 0%, #C53030 100%);">
                🎅
            </div>
        </div>

        <div class="text-center mb-4">
            <p id="status-text" class="text-white text-base font-normal mb-1">Tap to Start</p>
            <p id="sub-status" class="text-gray-400 text-sm">Press the microphone to talk</p>
        </div>

        <div class="flex flex-col items-center w-full mt-auto">
            <div class="grid grid-cols-3 gap-x-10 gap-y-6 mb-12 w-full max-w-xs">
                <div class="flex flex-col items-center">
                    <button class="call-btn"><svg class="call-btn-icon" fill="white" viewBox="0 0 24 24"><path d="M19 11h-1.7c0 .74-.16 1.43-.43 2.05l1.23 1.23c.56-.98.9-2.09.9-3.28zm-4.02.17c0-.06.02-.11.02-.17V5c0-1.66-1.34-3-3-3S9 3.34 9 5v.18l5.98 5.99zM4.27 3L3 4.27l6.01 6.01V11c0 1.66 1.33 3 2.99 3 .22 0 .44-.03.65-.08l1.66 1.66c-.71.33-1.5.52-2.31.52-2.76 0-5.3-2.1-5.3-5.1H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c.91-.13 1.77-.45 2.54-.9L19.73 21 21 19.73 4.27 3z"/></svg></button>
                    <span class="text-white text-xs mt-2">mute</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn"><svg class="call-btn-icon" fill="white" viewBox="0 0 24 24"><path d="M3 9h4l3-4H6C4.9 5 4 5.9 4 7v2h2V7h2l-2.5 3.5H3V9zm18 6h-2v2h-2l2.5-3.5H22v-1.5h-4l-3 4h4c1.1 0 2-.9 2-2v-2h-2v2zm-8-9h-2v7h2V6zm0 9h-2v2h2v-2z"/></svg></button>
                    <span class="text-white text-xs mt-2">keypad</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn"><svg class="call-btn-icon" fill="white" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg></button>
                    <span class="text-white text-xs mt-2">speaker</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn"><svg class="call-btn-icon" fill="white" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg></button>
                    <span class="text-white text-xs mt-2">add call</span>
                </div>
                <div class="flex flex-col items-center">
                    <button id="talk-btn" class="call-btn bg-green-600"><svg class="call-btn-icon" fill="white" viewBox="0 0 24 24"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.91-3c-.49 0-.9.36-.98.85C16.52 14.2 14.47 16 12 16s-4.52-1.8-4.93-4.15c-.08-.49-.49-.85-.98-.85-.61 0-1.09.54-1 1.14.49 3 2.89 5.35 5.91 5.78V20c0 .55.45 1 1 1s1-.45 1-1v-2.08c3.02-.43 5.42-2.78 5.91-5.78.1-.6-.39-1.14-1-1.14z"/></svg></button>
                    <span class="text-white text-xs mt-2">talk</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn"><svg class="call-btn-icon" fill="white" viewBox="0 0 24 24"><path d="M20 0H4v2h16V0zM4 24h16v-2H4v2zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 2.75c1.24 0 2.25 1.01 2.25 2.25s-1.01 2.25-2.25 2.25S9.75 10.24 9.75 9 10.76 6.75 12 6.75zM17 17H7v-1.5c0-1.67 3.33-2.5 5-2.5s5 .83 5 2.5V17z"/></svg></button>
                    <span class="text-white text-xs mt-2">contacts</span>
                </div>
            </div>

            <button id="end-call" class="end-call-btn">
                <svg class="w-7 h-7" fill="white" viewBox="0 0 24 24" transform="rotate(135)">
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
    let currentAudio = null;
    let callStartTime = null;
    let timerInterval = null;

    function updateStatusTime() {
        const now = new Date();
        statusTime.textContent = `${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;
    }
    updateStatusTime();
    setInterval(updateStatusTime, 30000);

    function updateCallTimer() {
        if (callStartTime) {
            const elapsed = Math.floor((Date.now() - callStartTime) / 1000);
            callTimer.textContent = `${Math.floor(elapsed / 60).toString().padStart(2, '0')}:${(elapsed % 60).toString().padStart(2, '0')}`;
        }
    }

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.lang = 'en-US';

        recognition.onstart = () => {
            isListening = true;
            listeningRing.style.display = 'block';
            avatarPulse.classList.add('listening-pulse');
            statusText.textContent = 'Listening...';
            talkBtn.classList.replace('bg-green-600', 'bg-red-600');
        };

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            conversationHistory.push({ role: 'user', content: transcript });
            getSantaResponse();
        };

        recognition.onend = () => { if (isListening && !isSantaSpeaking) recognition.start(); };
    }

    talkBtn.addEventListener('click', () => {
        if (!callStartTime) {
            callStartTime = Date.now();
            timerInterval = setInterval(updateCallTimer, 1000);
        }
        isListening ? stopListening() : startListening();
    });

    endCallBtn.addEventListener('click', () => {
        stopEverything();
        clearInterval(timerInterval);
        statusText.textContent = 'Call Ended';
        setTimeout(() => {
            conversationHistory = [];
            callStartTime = null;
            callTimer.textContent = '00:00';
            statusText.textContent = 'Tap to Start';
        }, 3000);
    });

    function startListening() { if (recognition && !isSantaSpeaking) recognition.start(); }

    function stopListening() {
        isListening = false;
        listeningRing.style.display = 'none';
        avatarPulse.classList.remove('listening-pulse');
        if (recognition) recognition.stop();
        statusText.textContent = 'Ready';
        talkBtn.classList.replace('bg-red-600', 'bg-green-600');
    }

    function stopEverything() {
        stopListening();
        isSantaSpeaking = false;
        if (currentAudio) { currentAudio.pause(); currentAudio = null; }
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
                    messages: [{ role: 'system', content: `You are Santa. Warm, jolly, 2 sentences max. End with a question.` }, ...conversationHistory]
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
            currentAudio = new Audio(URL.createObjectURL(audioBlob));
            currentAudio.onended = () => {
                isSantaSpeaking = false;
                statusText.textContent = 'Your turn';
                startListening();
            };
            currentAudio.play();
            statusText.textContent = 'Santa speaking';
        } catch (e) {
            isSantaSpeaking = false;
            statusText.textContent = 'Error occurred';
        }
    }
</script>
</body>
</html>