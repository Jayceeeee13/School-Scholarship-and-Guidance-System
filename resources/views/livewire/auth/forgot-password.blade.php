<div class="fp-root">

    <div class="fp-card">
        <div class="fp-stripe"></div>
        <div class="fp-inner">

            {{-- ===== LOGO / HEADER ===== --}}
            <div class="fp-logo-row">
                {{--
                    Replace the src below with your actual logo path.
                    Examples:
                      src="{{ asset('images/logo.png') }}"
                      src="{{ asset('images/gvcfi-logo.png') }}"
                --}}
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Green Valley College Foundation Inc."
                    class="fp-logo-img"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >
                {{-- Fallback icon shown only if image fails to load --}}
                <div class="fp-logo-fallback" style="display:none;">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                </div>
                <div class="fp-school-name">Green Valley College<br>Foundation Inc.</div>
            </div>

            {{-- Progress Steps --}}
            @if($step !== 'done')
            <div class="fp-steps">
                <div class="fp-step-item">
                    <div class="fp-step-circle {{ $step === 'email' ? 'active' : 'done' }}">
                        @if($step === 'email') 1 @else ✓ @endif
                    </div>
                    <div class="fp-step-label {{ $step === 'email' ? 'active' : 'done' }}">Email</div>
                </div>
                <div class="fp-step-connector {{ in_array($step, ['otp','reset']) ? 'done' : '' }}"></div>
                <div class="fp-step-item">
                    <div class="fp-step-circle {{ $step === 'otp' ? 'active' : ($step === 'reset' ? 'done' : 'idle') }}">
                        @if($step === 'reset') ✓ @else 2 @endif
                    </div>
                    <div class="fp-step-label {{ $step === 'otp' ? 'active' : ($step === 'reset' ? 'done' : '') }}">Verify</div>
                </div>
                <div class="fp-step-connector {{ $step === 'reset' ? 'done' : '' }}"></div>
                <div class="fp-step-item">
                    <div class="fp-step-circle {{ $step === 'reset' ? 'active' : 'idle' }}">3</div>
                    <div class="fp-step-label {{ $step === 'reset' ? 'active' : '' }}">Reset</div>
                </div>
            </div>
            @endif

            {{-- ===== STEP 1: EMAIL ===== --}}
            @if($step === 'email')
            <h2 class="fp-heading">Forgot Password?</h2>
            <p class="fp-subtext">Enter your email address and we'll send you a 6-digit OTP code to verify your identity.</p>

            <div class="fp-input-wrap">
                <label class="fp-label">Email Address</label>
                <input
                    type="email"
                    wire:model="email"
                    placeholder="yourname@greenvalley.edu.ph"
                    wire:keydown.enter="sendOtp"
                    class="fp-input {{ $errors->has('email') ? 'error' : '' }}"
                    style="padding-right:1rem;"
                >
                @error('email')
                    <div class="fp-error-msg">⚠ {{ $message }}</div>
                @enderror
            </div>

            <button wire:click="sendOtp" wire:loading.attr="disabled" class="fp-btn">
                <span wire:loading.remove wire:target="sendOtp">
                    Send OTP Code
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                </span>
                <span wire:loading wire:target="sendOtp">Sending...</span>
            </button>

            <a href="{{ filament()->getLoginUrl() }}" class="fp-link">← Back to Sign In</a>
            @endif

            {{-- ===== STEP 2: OTP ===== --}}
            @if($step === 'otp')
            <h2 class="fp-heading">Check Your Email</h2>
            <p class="fp-subtext">
                We sent a 6-digit code to <strong>{{ $maskedEmail }}</strong>. Enter it below to continue.
            </p>

            <div class="fp-otp-row">
                @for($i = 0; $i < 6; $i++)
                <input
                    type="text"
                    maxlength="1"
                    inputmode="numeric"
                    class="fp-otp-box otp-input {{ $errors->has('otp') ? 'error' : '' }}"
                    data-index="{{ $i }}"
                >
                @endfor
            </div>

            <input type="hidden" id="otp-combined">

            @error('otp')
                <div class="fp-error-msg" style="text-align:center;">⚠ {{ $message }}</div>
            @enderror

            <div class="fp-timer-row">
                <span class="fp-timer-text" id="timer-display">
                    Resend in <span class="fp-timer-num" id="timer-count">60s</span>
                </span>
                <button wire:click="resendOtp" id="resend-btn" class="fp-resend-btn">Resend OTP</button>
            </div>

            <button wire:click="verifyOtp" wire:loading.attr="disabled" class="fp-btn">
                <span wire:loading.remove wire:target="verifyOtp">
                    Verify Code
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                </span>
                <span wire:loading wire:target="verifyOtp">Verifying...</span>
            </button>

            <button wire:click="backToEmail" class="fp-link" style="background:none;border:none;cursor:pointer;width:100%;">
                ← Use a different email
            </button>
            @endif

            {{-- ===== STEP 3: RESET ===== --}}
            @if($step === 'reset')
            <h2 class="fp-heading">Set New Password</h2>
            <p class="fp-subtext">Almost done! Choose a strong new password for your account.</p>

            <div class="fp-input-wrap">
                <label class="fp-label">New Password</label>
                <div style="position:relative;">
                    <input
                        type="password"
                        wire:model.live="password"
                        id="new-password"
                        placeholder="At least 8 characters"
                        class="fp-input {{ $errors->has('password') ? 'error' : '' }}"
                    >
                    <button type="button" onclick="toggleEye('new-password', this)" class="fp-eye-btn">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                <div class="fp-strength-bar"><div class="fp-strength-fill" id="strength-fill"></div></div>
                <p class="fp-strength-label" id="strength-label"></p>
                @error('password') <div class="fp-error-msg">⚠ {{ $message }}</div> @enderror
            </div>

            <div class="fp-input-wrap">
                <label class="fp-label">Confirm Password</label>
                <div style="position:relative;">
                    <input
                        type="password"
                        wire:model="password_confirmation"
                        id="confirm-password"
                        placeholder="Repeat your password"
                        class="fp-input"
                    >
                    <button type="button" onclick="toggleEye('confirm-password', this)" class="fp-eye-btn">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            <button wire:click="resetPassword" wire:loading.attr="disabled" class="fp-btn">
                <span wire:loading.remove wire:target="resetPassword">
                    Reset Password
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </span>
                <span wire:loading wire:target="resetPassword">Resetting...</span>
            </button>
            @endif

            {{-- ===== STEP 4: DONE ===== --}}
            @if($step === 'done')
            <div class="fp-done-wrap">
                <div class="fp-done-check">
                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
                <h2 class="fp-heading">Password Reset!</h2>
                <p class="fp-subtext" style="margin-bottom:2rem;">Your password has been successfully updated. You can now sign in with your new credentials.</p>
                <a href="{{ filament()->getLoginUrl() }}" class="fp-go-btn">
                    Go to Sign In
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                </a>
            </div>
            @endif

        </div>{{-- /.fp-inner --}}
    </div>{{-- /.fp-card --}}

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@300;400;500;600&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .fp-root {
        font-family: 'DM Sans', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: #16a34a;
        min-height: 100vh;
    }

    .fp-bg-orb { display: none; }

    .fp-card {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 440px;
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 32px 80px rgba(0,0,0,0.35);
    }

    .fp-stripe {
        height: 6px;
        background: linear-gradient(90deg, #0e7a50, #16a34a, #0e7490, #0369a1);
    }

    .fp-inner {
        padding: 2.25rem 2.5rem 2.5rem;
    }

    /* ===== LOGO ROW ===== */
    .fp-logo-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f0f0ec;
    }

    /* Actual logo image — circular, fixed size */
    .fp-logo-img {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: contain;
        border: 2px solid #e8f0e8;
        background: #f7faf7;
        flex-shrink: 0;
        padding: 3px;
    }

    /* Fallback icon (shown if image 404s) */
    .fp-logo-fallback {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #0f2a1e;
        border: 2px solid #1a3d2b;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .fp-logo-fallback svg {
        width: 24px;
        height: 24px;
        stroke: #4ade80;
    }

    .fp-school-name {
        font-family: 'Playfair Display', serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f2a1e;
        line-height: 1.35;
    }

    /* Progress Steps */
    .fp-steps {
        display: flex;
        align-items: center;
        margin-bottom: 1.75rem;
    }
    .fp-step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        flex: 0 0 auto;
    }
    .fp-step-connector {
        height: 2px;
        flex: 1;
        background: #e8ede9;
        margin-bottom: 18px;
        transition: background 0.3s;
    }
    .fp-step-connector.done { background: #16a34a; }
    .fp-step-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .fp-step-circle.active { background: #0f2a1e; color: #4ade80; }
    .fp-step-circle.done   { background: #16a34a; color: white; }
    .fp-step-circle.idle   { background: #f0f4f0; color: #9aab9e; }
    .fp-step-label {
        font-size: 0.64rem;
        font-weight: 500;
        color: #9aab9e;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .fp-step-label.active { color: #0f2a1e; }
    .fp-step-label.done   { color: #16a34a; }

    /* Typography */
    .fp-heading {
        font-family: 'Playfair Display', serif;
        font-size: 1.55rem;
        font-weight: 700;
        color: #0f2a1e;
        margin-bottom: 0.35rem;
        line-height: 1.25;
    }
    .fp-subtext {
        font-size: 0.855rem;
        color: #637a6a;
        line-height: 1.6;
        margin-bottom: 1.75rem;
        font-weight: 300;
    }
    .fp-subtext strong { font-weight: 600; color: #0e7a50; }
    .fp-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        color: #3d5244;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 0.45rem;
    }

    /* Inputs */
    .fp-input-wrap { margin-bottom: 1.2rem; }
    .fp-input {
        width: 100%;
        padding: 0.875rem 3rem 0.875rem 1rem;
        border: 1.5px solid #dde8df;
        border-radius: 10px;
        font-size: 0.935rem;
        color: #0f2a1e;
        background: #f7faf7;
        outline: none;
        font-family: 'DM Sans', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .fp-input:focus {
        border-color: #0e7a50;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(14,122,80,0.1);
    }
    .fp-input.error { border-color: #ef4444; }

    .fp-eye-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #9aab9e;
        padding: 4px;
        display: flex;
        transition: color 0.2s;
    }
    .fp-eye-btn:hover { color: #0e7a50; }

    .fp-error-msg {
        font-size: 0.75rem;
        color: #ef4444;
        margin-top: 0.35rem;
        font-weight: 500;
    }

    /* Button */
    .fp-btn {
        width: 100%;
        padding: 0.9rem 1rem;
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        letter-spacing: 0.02em;
        cursor: pointer;
        background: #0f2a1e;
        color: #ffffff;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .fp-btn:hover    { background: #1a3d2b; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(15,42,30,0.25); }
    .fp-btn:active   { transform: translateY(0); }
    .fp-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .fp-btn span     { display: flex; align-items: center; gap: 8px; }

    .fp-link {
        display: block;
        text-align: center;
        margin-top: 1.1rem;
        font-size: 0.825rem;
        color: #8a9a8e;
        text-decoration: none;
        cursor: pointer;
        transition: color 0.2s;
        background: none;
        border: none;
        font-family: 'DM Sans', sans-serif;
    }
    .fp-link:hover { color: #0e7a50; }

    /* OTP */
    .fp-otp-row {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-bottom: 0.35rem;
    }
    .fp-otp-box {
        width: 52px;
        height: 58px;
        border: 1.5px solid #dde8df;
        border-radius: 10px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f2a1e;
        background: #f7faf7;
        outline: none;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.2s;
    }
    .fp-otp-box:focus {
        border-color: #0e7a50;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(14,122,80,0.1);
        transform: translateY(-2px);
    }
    .fp-otp-box.error  { border-color: #ef4444; }
    .fp-otp-box.filled { border-color: #16a34a; background: #f0faf4; }

    .fp-timer-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1rem 0 1.5rem;
        font-size: 0.8rem;
    }
    .fp-timer-text { color: #8a9a8e; }
    .fp-timer-num  { color: #0e7a50; font-weight: 600; }
    .fp-resend-btn {
        display: none;
        background: none;
        border: none;
        color: #0e7a50;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        text-decoration: underline;
    }
    .fp-resend-btn:hover { color: #0f2a1e; }

    /* Strength bar */
    .fp-strength-bar  { height: 3px; background: #e8ede9; border-radius: 2px; margin-top: 8px; overflow: hidden; }
    .fp-strength-fill { height: 100%; border-radius: 2px; width: 0%; transition: all 0.3s; }
    .fp-strength-label { font-size: 0.72rem; margin-top: 4px; font-weight: 600; min-height: 16px; }

    /* Done */
    .fp-done-wrap { text-align: center; padding: 1rem 0 0.5rem; }
    .fp-done-check {
        width: 72px;
        height: 72px;
        background: #0f2a1e;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        animation: popIn 0.5s cubic-bezier(0.175,0.885,0.32,1.275);
    }
    .fp-done-check svg { width: 32px; height: 32px; stroke: #4ade80; }

    .fp-go-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.85rem 2.5rem;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        background: #0f2a1e;
        color: #fff;
        text-decoration: none;
        transition: all 0.2s;
    }
    .fp-go-btn:hover { background: #1a3d2b; transform: translateY(-1px); }

    @keyframes popIn {
        from { transform: scale(0); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
    </style>

    <script>
    function initOtp() {
        const boxes = document.querySelectorAll('.otp-input');
        const combined = document.getElementById('otp-combined');
        if (!boxes.length || !combined || combined.dataset.initialized) return;
        combined.dataset.initialized = 'true';

        function updateCombined() {
            const val = Array.from(boxes).map(b => b.value).join('');
            combined.value = val;
            @this.set('otp', val);
        }

        boxes.forEach((box, i) => {
            box.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '');
                box.classList.toggle('filled', !!box.value);
                if (e.target.value && i < boxes.length - 1) boxes[i + 1].focus();
                updateCombined();
            });
            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !box.value && i > 0) {
                    boxes[i - 1].focus();
                    boxes[i - 1].value = '';
                    boxes[i - 1].classList.remove('filled');
                    updateCombined();
                }
                if (e.key === 'ArrowLeft'  && i > 0)               boxes[i - 1].focus();
                if (e.key === 'ArrowRight' && i < boxes.length - 1) boxes[i + 1].focus();
            });
            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                text.split('').forEach((char, j) => {
                    if (boxes[j]) { boxes[j].value = char; boxes[j].classList.add('filled'); }
                });
                boxes[Math.min(text.length, boxes.length - 1)]?.focus();
                updateCombined();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initOtp);
    document.addEventListener('livewire:navigated', initOtp);

    let timerInterval;
    function startTimer(seconds) {
        clearInterval(timerInterval);
        const countEl   = document.getElementById('timer-count');
        const displayEl = document.getElementById('timer-display');
        const resendBtn = document.getElementById('resend-btn');
        if (!countEl) return;
        resendBtn.style.display = 'none';
        displayEl.style.display = 'inline';
        let s = seconds;
        timerInterval = setInterval(() => {
            s--;
            if (countEl) countEl.textContent = s + 's';
            if (s <= 0) {
                clearInterval(timerInterval);
                if (displayEl) displayEl.style.display = 'none';
                if (resendBtn) resendBtn.style.display  = 'inline';
            }
        }, 1000);
    }

    document.addEventListener('livewire:initialized', () => {
        @this.on('otp-sent', () => { startTimer(60); initOtp(); });
    });

    @if($step === 'otp')
    document.addEventListener('DOMContentLoaded', () => startTimer(60));
    @endif

    const passInput = document.getElementById('new-password');
    if (passInput) {
        passInput.addEventListener('input', e => checkStrength(e.target.value));
    }

    function checkStrength(val) {
        const fill  = document.getElementById('strength-fill');
        const label = document.getElementById('strength-label');
        if (!fill || !label) return;
        let score = 0;
        if (val.length >= 8)          score++;
        if (/[A-Z]/.test(val))        score++;
        if (/[0-9]/.test(val))        score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
            { w: '0%',   c: '#e8ede9', t: '',       tc: '' },
            { w: '25%',  c: '#ef4444', t: 'Weak',   tc: '#ef4444' },
            { w: '50%',  c: '#f97316', t: 'Fair',   tc: '#f97316' },
            { w: '75%',  c: '#eab308', t: 'Good',   tc: '#eab308' },
            { w: '100%', c: '#16a34a', t: 'Strong', tc: '#16a34a' },
        ];
        const l = levels[score] || levels[0];
        fill.style.width      = l.w;
        fill.style.background = l.c;
        label.textContent     = l.t;
        label.style.color     = l.tc;
    }

    function toggleEye(id, btn) {
        const input = document.getElementById(id);
        if (!input) return;
        input.type      = input.type === 'password' ? 'text' : 'password';
        btn.style.color = input.type === 'text' ? '#0e7a50' : '#9aab9e';
    }
    </script>

</div>{{-- /.fp-root --}}