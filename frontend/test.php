 <!-- ERROR ALERT BANNER -->
            <?php if (!empty($error)): ?>
              <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 flex items-start gap-3 text-red-400 text-sm animate-fade-in break-words">
                <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span><?= htmlspecialchars(is_array($error) ? json_encode($error) : $error) ?></span>
              </div>
            <?php endif; ?>







            <!-- MAIN AUTH SECTION WITH BACKGROUND -->
    <main class="relative w-full min-h-[calc(100vh-73px)] flex items-center justify-center overflow-hidden bg-slate-950 text-white">
        
        <div class="absolute inset-0 opacity-30">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1600&q=80" alt="Warehouse" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/90 via-slate-950/70 to-brand-darkblue/40 z-10"></div>

        <!-- Foreground Content -->
        <div class="relative z-20 max-w-7xl mx-auto px-6 w-full py-12 lg:py-16">
            <div class="flex justify-center">

                <!-- Agent Login Card -->
                <div id="login-card" class="w-full max-w-md reveal">
                    <div class="bg-slate-950/80 border border-white/15 p-8 sm:p-10 rounded-3xl backdrop-blur-2xl shadow-2xl relative">
                        
                        <div class="text-center mb-8">
                            <div class="w-12 h-12 bg-brand-blue/20 border border-brand-blue/30 rounded-2xl flex items-center justify-center text-brand-blue text-xl mx-auto mb-3 shadow-lg">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white">Sales Agent Sign In</h3>
                            <p class="text-slate-400 text-xs mt-1">Enter your internal credentials to access the console</p>
                        </div>

                        
                        <form action="login.php" method="POST" class="space-y-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" required 
                                           class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all"
                                           placeholder="name@priority-ph.com">
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1.5">
                                    <label class="block text-xs font-semibold text-slate-300">Password</label>
                                </div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="password" name="password" required 
                                           class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all"
                                           placeholder="••••••••">
                                </div>
                            </div>

                            <button type="submit" id="loginBtn" class="w-full bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-right-to-bracket"></i> Sign In
                            </button>

                            <div id="loginStatus" class="hidden p-3 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl text-center text-xs"></div>
                        </form>


                         <!-- Step 2: OTP Verification Form (Hidden Initially) -->
                        <form method="POST" action="otp_verification.php" class="space-y-5 hidden">
                            <div class="text-center mb-2">
                                <div class="w-12 h-12 bg-emerald-500/20 border border-emerald-500/30 rounded-2xl flex items-center justify-center text-emerald-400 text-xl mx-auto mb-3 shadow-lg">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <h3 class="text-xl font-extrabold text-white">Two-Factor Verification</h3>
                                <p class="text-slate-400 text-xs mt-1">Enter the 6-digit code sent to <span id="otpEmailDisplay" class="text-sky-400 font-mono">your email</span></p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">One-Time Password (OTP)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </span>
                                    <input type="text" name="otp_code" required maxlength="6" autofocus inputmode="numeric" pattern="[0-9]{6}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all tracking-[0.5em] text-center font-mono"
                                           placeholder="••••••">
                                </div>
                               
                            </div>

                            <button type="submit"  class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check"></i> Verify & Sign In
                            </button>


                             <!-- Back to Login – now points to login.html -->
                              <div class="mt-6 pt-6 border-t border-white/5 text-center">
                                <a href="login.html" class="text-xs text-slate-400 hover:text-slate-300 transition">
                                  ← Back to Login
                                </a>
                              </div>

                              <div class="mt-3 p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 flex gap-3 items-start">
                                <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.949 3.374h14.71c1.73 0 2.813-1.874 1.949-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                <p class="text-xs text-amber-200/80 leading-relaxed">
                                  If you didn’t receive the code, check your spam folder or request a new one.
                                </p>
                              </div>

                            
                        </form>