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
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 0.1;
                transform: scale(1.1);
            }
        }
        .listening-pulse {
            animation: listening-pulse 1.5s ease-in-out infinite;
        }
        @keyframes listening-pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(52, 199, 89, 0.7);
            }
            50% {
                box-shadow: 0 0 0 15px rgba(52, 199, 89, 0);
            }
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
            right: -4px;
            top: 3px;
            width: 2px;
            height: 4px;
            background: white;
            border-radius: 0 2px 2px 0;
        }
        .battery-fill {
            position: absolute;
            left: 1px;
            top: 1px;
            bottom: 1px;
            width: 18px;
            background: white;
            border-radius: 1px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="phone-container w-full max-w-sm mx-auto" style="height: 812px; max-height: 90vh;">
        <div class="notch"></div>

        <!-- Status Bar -->
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
            <div id="status-time" class="absolute left-1/2 transform -translate-x-1/2 font-semibold" style="letter-spacing: 0.3px;">9:41</div>
            <div class="flex items-center space-x-2">
                <div class="battery">
                    <div class="battery-fill"></div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center h-full pt-12 pb-8 px-6 relative">
            <!-- Contact Info -->
            <div class="text-center mb-2">
                <h1 class="text-white text-3xl font-normal mb-1 tracking-tight">Santa Claus</h1>
                <p class="text-gray-400 text-base font-normal">mobile</p>
            </div>

            <!-- Call Timer -->
            <div class="text-gray-400 text-base font-normal mb-8" id="call-timer">00:00</div>

            <!-- Avatar -->
            <div class="relative mb-auto mt-12">
                <div id="listening-ring" class="absolute inset-0 rounded-full bg-green-500 opacity-20 avatar-ring" style="display: none;"></div>
                <div id="avatar-pulse" class="w-40 h-40 rounded-full flex items-center justify-center text-8xl" style="background: linear-gradient(135deg, #E53E3E 0%, #C53030 100%); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                    🎅
                </div>
            </div>

            <!-- Status Text -->
            <div class="text-center mb-12">
                <p id="status-text" class="text-white text-base font-normal mb-1">Tap to Start</p>
                <p id="sub-status" class="text-gray-400 text-sm font-normal">Press the microphone to talk</p>
            </div>

            <!-- Control Buttons Grid -->
            <div class="grid grid-cols-3 gap-x-12 gap-y-6 mb-12 w-full max-w-xs">
                <!-- Row 1 -->
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg class="call-btn-icon" fill="white" viewBox="0 0 24 24">
                            <path d="M19 11h-1.7c0 .74-.16 1.43-.43 2.05l1.23 1.23c.56-.98.9-2.09.9-3.28zm-4.02.17c0-.06.02-.11.02-.17V5c0-1.66-1.34-3-3-3S9 3.34 9 5v.18l5.98 5.99zM4.27 3L3 4.27l6.01 6.01V11c0 1.66 1.33 3 2.99 3 .22 0 .44-.03.65-.08l1.66 1.66c-.71.33-1.5.52-2.31.52-2.76 0-5.3-2.1-5.3-5.1H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c.91-.13 1.77-.45 2.54-.9L19.73 21 21 19.73 4.27 3z"/>
                        </svg>
                    </button>
                    <span class="text-white text-xs mt-2 font-normal">mute</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg class="call-btn-icon" fill="white" viewBox="0 0 24 24">
                            <path d="M3 9h4l3-4H6C4.9 5 4 5.9 4 7v2h2V7h2l-2.5 3.5H3V9zm18 6h-2v2h-2l2.5-3.5H22v-1.5h-4l-3 4h4c1.1 0 2-.9 2-2v-2h-2v2zm-8-9h-2v7h2V6zm0 9h-2v2h2v-2z"/>
                        </svg>
                    </button>
                    <span class="text-white text-xs mt-2 font-normal">keypad</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg class="call-btn-icon" fill="white" viewBox="0 0 24 24">
                            <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                        </svg>
                    </button>
                    <span class="text-white text-xs mt-2 font-normal">speaker</span>
                </div>
                <!-- Row 2 -->
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg class="call-btn-icon" fill="white" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                    </button>
                    <span class="text-white text-xs mt-2 font-normal">add call</span>
                </div>
                <div class="flex flex-col items-center">
                    <button id="talk-btn" class="call-btn bg-green-600">
                        <svg class="call-btn-icon" fill="white" viewBox="0 0 24 24">
                            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.91-3c-.49 0-.9.36-.98.85C16.52 14.2 14.47 16 12 16s-4.52-1.8-4.93-4.15c-.08-.49-.49-.85-.98-.85-.61 0-1.09.54-1 1.14.49 3 2.89 5.35 5.91 5.78V20c0 .55.45 1 1 1s1-.45 1-1v-2.08c3.02-.43 5.42-2.78 5.91-5.78.1-.6-.39-1.14-1-1.14z"/>
                        </svg>
                    </button>
                    <span class="text-white text-xs mt-2 font-normal">talk</span>
                </div>
                <div class="flex flex-col items-center">
                    <button class="call-btn">
                        <svg class="call-btn-icon" fill="white" viewBox="0 0 24 24">
                            <path d="M20 0H4v2h16V0zM4 24h16v-2H4v2zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 2.75c1.24 0 2.25 1.01 2.25 2.25s-1.01 2.25-2.25 2.25S9.75 10.24 9.75 9 10.76 6.75 12 6.75zM17 17H7v-1.5c0-1.67 3.33-2.5 5-2.5s5 .83 5 2.5V17z"/>
                        </svg>
                    </button>
                    <span class="text-white text-xs mt-2 font-normal">contacts</span>
                </div>
            </div>

            <!-- End Call Button -->
            <button id="end-call" class="end-call-btn">
                <svg class="w-7 h-7" fill="white" viewBox="0 0 24 24" transform="rotate(135)">
                    <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
        const SUPABASE_URL = '{{ env('VITE_SUPABASE_URL') }}';
        const SUPABASE_ANON_KEY = '{{ env('VITE_SUPABASE_SUPABASE_ANON_KEY') }}';

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

        // Update real time in status bar
        function updateStatusTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            statusTime.textContent = `${hours}:${minutes.toString().padStart(2, '0')}`;
        }
        updateStatusTime();
        setInterval(updateStatusTime, 30000);

        // Update call timer
        function updateCallTimer() {
            if (callStartTime) {
                const elapsed = Math.floor((Date.now() - callStartTime) / 1000);
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                callTimer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }

        // Initialize speech recognition
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';

            recognition.onstart = () => {
                isListening = true;
                listeningRing.style.display = 'block';
                avatarPulse.classList.add('listening-pulse');
                statusText.textContent = 'Listening...';
                subStatus.textContent = 'Speak now';
                talkBtn.classList.add('bg-red-600');
                talkBtn.classList.remove('bg-green-600');
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
            if (!callStartTime) {
                callStartTime = Date.now();
                timerInterval = setInterval(updateCallTimer, 1000);
            }

            if (!isListening) {
                startListening();
            } else {
                stopListening();
            }
        });

        endCallBtn.addEventListener('click', () => {
            stopEverything();
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            statusText.textContent = 'Call Ended';
            subStatus.textContent = 'Ho ho ho! Merry Christmas!';
            setTimeout(() => {
                conversationHistory = [];
                callStartTime = null;
                callTimer.textContent = '00:00';
                statusText.textContent = 'Tap to Start';
                subStatus.textContent = 'Press the microphone to talk';
            }, 3000);
        });

        function startListening() {
            if (recognition && !isSantaSpeaking) {
                if (conversationHistory.length === 0) {
                    getSantaResponse('');
                } else {
                    recognition.start();
                }
            }
        }

        function stopListening() {
            isListening = false;
            listeningRing.style.display = 'none';
            avatarPulse.classList.remove('listening-pulse');
            if (recognition) {
                recognition.stop();
            }
            if (!isSantaSpeaking) {
                statusText.textContent = 'Ready';
                subStatus.textContent = 'Press talk button';
                talkBtn.classList.remove('bg-red-600');
                talkBtn.classList.add('bg-green-600');
            }
        }

        function stopEverything() {
            stopListening();
            isSantaSpeaking = false;
            if (currentAudio) {
                currentAudio.pause();
                currentAudio = null;
            }
        }

        async function getSantaResponse(userMessage) {
            stopListening();
            isSantaSpeaking = true;
            statusText.textContent = 'Santa speaking';
            subStatus.textContent = 'Ho ho ho!';

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

                const audio = new Audio('data:audio/mpeg;base64,' + data.audio);
                currentAudio = audio;

                audio.onended = () => {
                    isSantaSpeaking = false;
                    currentAudio = null;
                    statusText.textContent = 'Your turn';
                    subStatus.textContent = 'Santa is listening';
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
                    statusText.textContent = 'Connection lost';
                    subStatus.textContent = 'Try again';
                };

                audio.play();

            } catch (error) {
                console.error('Error getting Santa response:', error);
                isSantaSpeaking = false;
                statusText.textContent = 'Connection lost';
                subStatus.textContent = 'Press talk to retry';
                talkBtn.classList.remove('bg-red-600');
                talkBtn.classList.add('bg-green-600');
            }
        }
    </script>
</body>
</html>
