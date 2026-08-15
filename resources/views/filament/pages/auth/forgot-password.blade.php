<div class="min-h-screen flex items-center justify-center p-4"
     style="background: url('/images/clouds.png') no-repeat center center fixed; background-size: cover;">

    {{-- Overlay --}}
    <div class="fixed inset-0"
         style="background: linear-gradient(135deg, rgba(22,163,74,0.45), rgba(6,95,70,0.55)); z-index:0;">
    </div>

    <div class="relative z-10 w-full max-w-md">

        {{-- Card --}}
        <div style="background: rgba(255,255,255,0.96); border-radius: 24px;
                    box-shadow: 0 25px 60px rgba(22,163,74,0.3), 0 0 0 1px rgba(255,255,255,0.5);
                    overflow: hidden;">

            {{-- Header --}}
            <div style="background: linear-gradient(135deg, #16a34a, #065f46);
                        padding: 2.5rem 2rem 3rem; text-align: center; position: relative;">

                <div style="width:72px; height:72px;
                            background:rgba(255,255,255,0.2);
                            border:2px solid rgba(255,255,255,0.4);
                            border-radius:50%;
                            display:flex; align-items:center; justify-content:center;
                            margin:0 auto 1rem;
                            box-shadow:0 0 30px rgba(34,197,94,0.4);">

                    {{-- ICONS --}}
                    @if($step === 'otp')
                        <svg style="width:36px;height:36px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                    @elseif($step === 'reset')
                        <svg style="width:36px;height:36px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 15v2"/></svg>
                    @else
                        <svg style="width:36px;height:36px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 7a2 2 0 012 2"/></svg>
                    @endif
                </div>

                <div style="font-family: Georgia, serif; font-size:13px;
                            font-weight:700; color:white;
                            letter-spacing:2px; text-transform:uppercase;">
                    Green Valley College Foundation Inc.
                </div>

                <div style="position:absolute; bottom:-1px; left:0; right:0;
                            height:32px; background:rgba(255,255,255,0.96);
                            border-radius:50% 50% 0 0 / 100% 100% 0 0;">
                </div>
            </div>

            {{-- Body --}}
            <div style="padding: 1.75rem 2.5rem 2.5rem;">

                {{-- TITLE --}}
                <h2 style="font-family:Georgia,serif; font-size:1.4rem;
                           color:#065f46; font-weight:700;">
                    Forgot Password?
                </h2>

                <p style="font-size:0.875rem; color:#4d7c0f; margin-bottom:1.5rem;">
                    Enter your email and we’ll send you an OTP code.
                </p>

                {{-- INPUT --}}
                <input type="email"
                       placeholder="your@email.com"
                       style="width:100%; padding:0.85rem 1rem;
                              border:1.5px solid #d1d5db;
                              border-radius:12px;
                              background:#ecfdf5; outline:none;"
                       onfocus="this.style.borderColor='#16a34a';
                                this.style.boxShadow='0 0 0 3px rgba(34,197,94,0.2)'">

                {{-- BUTTON --}}
                <button style="width:100%; margin-top:1rem; padding:0.9rem;
                               border:none; border-radius:12px;
                               font-weight:700; cursor:pointer;
                               background:linear-gradient(135deg,#16a34a,#065f46);
                               color:white;
                               box-shadow:0 4px 15px rgba(34,197,94,0.4);">
                    Send OTP →
                </button>

                {{-- OTP SAMPLE --}}
                <div style="display:flex; gap:10px; justify-content:center; margin:20px 0;">
                    <input style="width:50px; height:55px; text-align:center;
                                  border:2px solid #d1d5db;
                                  border-radius:10px;
                                  background:#ecfdf5;
                                  color:#065f46;">
                    <input style="width:50px; height:55px; text-align:center;
                                  border:2px solid #d1d5db;
                                  border-radius:10px;
                                  background:#ecfdf5;
                                  color:#065f46;">
                </div>

                {{-- TIMER --}}
                <p style="text-align:center; color:#4d7c0f;">
                    Resend in <span style="color:#16a34a; font-weight:700;">60s</span>
                </p>

                {{-- DONE --}}
                <div style="text-align:center; margin-top:2rem;">
                    <div style="width:80px; height:80px;
                                background:linear-gradient(135deg,#22c55e,#16a34a);
                                border-radius:50%;
                                margin:auto;
                                box-shadow:0 8px 25px rgba(34,197,94,0.4);">
                    </div>

                    <h2 style="color:#065f46; margin-top:1rem;">Success!</h2>

                    <a style="display:inline-block; margin-top:1rem;
                              padding:0.9rem 2rem;
                              border-radius:12px;
                              background:linear-gradient(135deg,#16a34a,#065f46);
                              color:white;
                              text-decoration:none;
                              box-shadow:0 4px 15px rgba(34,197,94,0.4);">
                        Go to Login →
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>