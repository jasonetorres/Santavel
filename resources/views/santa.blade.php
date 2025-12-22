<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=0">
    <title>Santa Claus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #000; font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif; overflow: hidden; height: 100dvh; display: flex; align-items: flex-start; justify-content: center; }
        .phone-container { background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%); border-radius: 48px; box-shadow: 0 20px 60px rgba(0,0,0,0.8), inset 0 0 0 12px #1a1a1a; border: 8px solid #000; position: relative; overflow: hidden; width: 100%; max-width: 400px; height: 96%; margin-top: -15px; }
        .status-bar { height: 40px; backdrop-filter: blur(20px); background: rgba(0, 0, 0, 0.3); }
        .notch { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 160px; height: 28px; background: #000; border-radius: 0 0 20px 20px; z-index: 50; }
        .call-btn { width: 58px; height: 58px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .end-call-btn { width: 68px; height: 68px; border-radius: 50%; background: #FF3B30; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(255, 59, 48, 0.3); }
        .listening-pulse { animation: listening-pulse 1.5s ease-in-out infinite; }
        @keyframes listening-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(52, 199, 89, 0.7); } 50% { box-shadow: 0 0 0 15px rgba(52, 199, 89, 0); } }
    </style>
</head>
<body>
<div class="phone-container">
    <div class="notch"></div>
    <div class="status-bar flex items-center justify-between px-8 text-white text-xs font-semibold relative z-40">
        <div id="status-time">9:41</div>
        <div class="flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="white" viewBox="0 0 24 24"><path d="M12.01 21.49L23.64 7c-.45-.34-4.93-4-11.64-4C5.28 3 .81 6.66.36 7l11.63 14.49.01.01.01-.01z"/></svg>
            <div class="w-5 h-2.5 border border-white/50 rounded-sm relative"><div class="absolute inset-y-0 left-0 bg-white w-3 m-0.5"></div></div>
        </div>
    </div>

    <div class="flex flex-col items-center h-full pt-4 pb-40 px-6">
        <div class="text-center">
            <h1 class="text-white text-3xl font-normal tracking-tight">Santa Claus</h1>
            <p id="call-timer" class="text-gray-400 text-base">00:00</p>
        </div>

        <div class="relative my-4">
            <div id="avatar-pulse" class="w-32 h-32 rounded-full flex items-center justify-center text-6xl shadow-2xl" style="background: linear-gradient(135deg, #E53E3E 0%, #C53030 100%);">🎅</div>
        </div>

        <div class="text-center mb-2">
            <p id="status-text" class="text-white text-base font-normal">Tap Talk to Start</p>
            <p id="sub-status" class="text-gray-500 text-xs italic">Awaiting voice...</p>
        </div>

        <div class="flex flex-col items-center w-full mt-auto">
            <div class="grid grid-cols-3 gap-x-8 gap-y-6 mb-8 w-full max-w-xs px-2">
                <div class="flex flex-col items-center"><button class="call-btn">🔇</button><span class="text-white text-[10px] mt-1">mute</span></div>
                <div class="flex flex-col items-center"><button class="call-btn">⌨️</button><span class="text-white text-[10px] mt-1">keypad</span></div>
                <div class="flex flex-col items-center"><button class="call-btn">🔊</button><span class="text-white text-[10px] mt-1">speaker</span></div>
                <div class="flex flex-col items-center"><button class="call-btn">➕</button><span class="text-white text-[10px] mt-1">add call</span></div>
                <div class="flex flex-col items-center">
                    <button id="talk-btn" class="call-btn bg-green-600 border-none">🎤</button>
                    <span id="talk-label" class="text-white text-[10px] mt-1">talk</span>
                </div>
                <div class="flex flex-col items-center"><button class="call-btn">👤</button><span class="text-white text-[10px] mt-1">contacts</span></div>
            </div>
            <button id="end-call" class="end-call-btn">🛑</button>
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
    const avatarPulse = document.getElementById('avatar-pulse');
    const callTimer = document.getElementById('call-timer');

    let recognition;
    let isListening = false;
    let isSantaSpeaking = false;
    let conversationHistory = [];
    let callStartTime = null;
    let currentHearsay = "";

    const santaVoice = new Audio();
    santaVoice.preload = "auto";

    // Audio Unlocking
    const unlock = () => { santaVoice.play().then(() => { santaVoice.pause(); santaVoice.currentTime = 0; }).catch(() => {}); document.removeEventListener('touchstart', unlock); };
    document.addEventListener('touchstart', unlock);

    function initRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) { statusText.textContent = "Browser not supported"; return; }

        recognition = new SpeechRecognition();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = 'en-US';

        recognition.onstart = () => {
            isListening = true;
            currentHearsay = "";
            avatarPulse.classList.add('listening-pulse');
            statusText.textContent = 'Listening...';
            talkBtn.classList.replace('bg-green-600', 'bg-red-600');
            talkLabel.textContent = 'done';
            subStatus.textContent = "Santa is waiting for your words...";
        };

        recognition.onresult = (event) => {
            let interim = "";
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) currentHearsay += event.results[i][0].transcript;
                else interim += event.results[i][0].transcript;
            }
            // Aggressive capture
            window.lastWords = currentHearsay + interim;
            subStatus.textContent = "Heard: " + window.lastWords.split(" ").slice(-5).join(" ");
        };

        recognition.onerror = (e) => {
            console.error(e);
            if (isListening) { try { recognition.abort(); } catch(err) {} setTimeout(() => { if(isListening) recognition.start(); }, 300); }
        };
    }

    initRecognition();

    function sendToSanta() {
        isListening = false;
        avatarPulse.classList.remove('listening-pulse');
        talkBtn.classList.replace('bg-red-600', 'bg-green-600');
        talkLabel.textContent = 'talk';

        try { recognition.abort(); } catch(e) {}

        const finalMessage = (window.lastWords || "").trim();

        if (finalMessage.length > 1) {
            conversationHistory.push({ role: 'user', content: finalMessage });
            getSantaResponse();
        } else {
            statusText.textContent = 'Ready';
            subStatus.textContent = "Didn't catch that, try again!";
        }
    }

    talkBtn.addEventListener('click', () => {
        if (!callStartTime) { callStartTime = Date.now(); setInterval(() => { const elapsed = Math.floor((Date.now() - callStartTime) / 1000); callTimer.textContent = `${Math.floor(elapsed / 60).toString().padStart(2, '0')}:${(elapsed % 60).toString().padStart(2, '0')}`; }, 1000); }
        if (isListening) sendToSanta();
        else if (!isSantaSpeaking) { window.lastWords = ""; recognition.start(); }
    });

    async function getSantaResponse() {
        isSantaSpeaking = true;
        statusText.textContent = 'Santa thinking...';
        subStatus.textContent = "Ho ho ho...";

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
            santaVoice.src = URL.createObjectURL(audioBlob);
            santaVoice.onended = () => { isSantaSpeaking = false; statusText.textContent = 'Your turn'; window.lastWords = ""; recognition.start(); };
            santaVoice.play().then(() => statusText.textContent = 'Santa speaking').catch(() => { statusText.textContent = 'Tap to Listen'; window.addEventListener('touchstart', () => santaVoice.play(), {once: true}); });
        } catch (e) { isSantaSpeaking = false; statusText.textContent = 'Error'; }
    }

    document.getElementById('end-call').onclick = () => location.reload();
</script>
</body>
</html>