<?php
session_start();
require_once 'src/helpers/api_helper.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Email and Password should not empty";
    } else {
        $login_payload = [
            'email' => $email,
            'password' => $password
        ];

        // 1. I-post sa tamang FastAPI router endpoint gamit ang JSON false
        $response = make_api_request('/api/auth/login', 'POST', $login_payload, false);

        // 2. Case A: Kung OTP flow ang setup (Step 1 Complete)
        if ($response['status_code'] == 200 && isset($response['data']['status']) && $response['data']['status'] === 'otp_sent') {
            $_SESSION["temp_email"] = $email;
            header("Location: otp_verification.php");
            exit();
        } 
        // 3. Case B: Direct Token Response Kung walang OTP
        elseif ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
            $token = $response['data']['access_token'];
            $_SESSION["access_token"] = $token;

            // Decode JWT Payload para sa User info at Role
            $token_parts = explode('.', $token);
            if (count($token_parts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
                $_SESSION["email"] = $payload["email"] ?? $email;
                $_SESSION["role"] = $payload["role"] ?? "customer";
            }

            if ($_SESSION["role"] === 'sales' || $_SESSION["role"] === 'sales_agent') {
                header("Location: src/views/sales_agent/dashboard.php"); // Path ng Sales Dashboard mo

            } else if($_SESSION["role"] === 'admin' || $_SESSION["role"] === 'administrator') {
                header("Location: src/views/admin/dashboard.php"); // Path ng Admin Dashboard mo
            } 
            else if($_SESSION["role"] === 'customer') {
                header("Location: src/views/customer/dashboard.php"); // Path ng Customer Dashboard mo
            } 
            else {
                $error = "Unauthorized role. Please contact support.";
            }
            exit();
        } 
        // 4. Handling ng Error
        else {
            $error = $response['error'] ?? ($response['data']['detail'] ?? "Maling email o password.");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login · Rising Red Dragon</title>

  <!-- ===== HEADER SECTION ===== -->
  <!-- This block can be moved to a separate header include file -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/login.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- ===== END HEADER SECTION ===== -->
</head>

<body class="bg-slate-950 text-slate-100 antialiased">

  <div class="min-h-screen mesh-bg relative overflow-hidden">

    <!-- Decorative grid -->
    <div class="absolute inset-0 opacity-20 pointer-events-none"
         style="background-image:
           linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
           linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
           background-size: 40px 40px;">
    </div>

    <div class="grid lg:grid-cols-12 min-h-screen relative z-10">

      <!-- LEFT COLUMN – Branding -->
      <div class="lg:col-span-7 flex flex-col justify-center px-8 py-12 lg:px-16 xl:px-24">

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/10 border border-sky-500/20 text-sky-300 text-xs font-semibold tracking-wide uppercase mb-6 w-fit fade-in" style="animation-delay:.1s">
          <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
          Enterprise Logistics OS
        </div>

        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold tracking-tight text-white leading-[1.1] mb-6 fade-in" style="animation-delay:.2s">
          Global Freight Command <br />
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-cyan-300">Center & CRM</span>
        </h1>

        <p class="text-lg text-slate-300 max-w-xl mb-10 leading-relaxed fade-in" style="animation-delay:.3s">
          Unified customer relationship management, contract SLA enforcement, e‑documentation compliance, and predictive freight analytics — all in one secure operations hub.
        </p>

        <div class="grid sm:grid-cols-3 gap-6 mb-12 fade-in" style="animation-delay:.4s">
          <div class="glass-panel rounded-xl p-5">
            <div class="text-2xl font-bold text-white mb-1">15,000+</div>
            <div class="text-sm text-slate-400">Monthly Shipments</div>
          </div>
          <div class="glass-panel rounded-xl p-5">
            <div class="text-2xl font-bold text-white mb-1">98.5%</div>
            <div class="text-sm text-slate-400">On-Time Delivery</div>
          </div>
          <div class="glass-panel rounded-xl p-5">
            <div class="text-2xl font-bold text-white mb-1">500+</div>
            <div class="text-sm text-slate-400">Enterprise Clients</div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-6 text-sm text-slate-400 fade-in" style="animation-delay:.5s">
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.746 3.746 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
            ISO 27001 Certified
          </span>
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.746 3.746 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
            SOC 2 Compliant
          </span>
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.746 3.746 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
            AES-256 Encrypted
          </span>
        </div>

      </div>

      <!-- RIGHT COLUMN – Login Form -->
      <div class="lg:col-span-5 flex items-center justify-center p-6 lg:p-12 bg-slate-900/50 backdrop-blur-sm border-l border-white/5">

        <div class="w-full max-w-md fade-in" style="animation-delay:.3s">

          <div class="bg-slate-900/80 border border-slate-700 rounded-2xl shadow-2xl p-8">

            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-19.5-3h19.5m-19.5-3h19.5m-19.5-3h19.5M4.5 3h15a2.25 2.25 0 012.25 2.25v.75a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25v-.75A2.25 2.25 0 014.5 3z"/></svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-white tracking-tight">CargoNet</h2>
                <p class="text-xs text-slate-400">Authorized Access Only</p>
              </div>
            </div>

            <div class="border-t border-slate-700/50 my-6"></div>

            <!-- ERROR ALERT BANNER -->
            <?php if (!empty($error)): ?>
              <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 flex items-start gap-3 text-red-400 text-sm animate-fade-in break-words">
                <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span><?= htmlspecialchars(is_array($error) ? json_encode($error) : $error) ?></span>
              </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="login.php" class="space-y-5">

              <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                <div class="relative">
                  <span class="absolute left-3 top-2.5 text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                  </span>
                  <input type="email" name="email" required
                         class="w-full pl-10 pr-4 py-2.5 bg-slate-800 border border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-slate-100 placeholder-slate-500 transition"
                         placeholder="ops@cargonet.com" />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                  <span class="absolute left-3 top-2.5 text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                  </span>
                  <input type="password" name="password" required
                         class="w-full pl-10 pr-4 py-2.5 bg-slate-800 border border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-slate-100 placeholder-slate-500 transition"
                         placeholder="••••••••" />
                </div>
              </div>

              <button type="submit"
                      class="w-full py-2.5 rounded-lg bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white font-semibold shadow-lg shadow-sky-500/20 transition active:scale-[0.98]">
                Secure Login
              </button>
            </form>

            <div class="mt-6 p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 flex gap-3 items-start">
              <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.949 3.374h14.71c1.73 0 2.813-1.874 1.949-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
              <p class="text-xs text-amber-200/80 leading-relaxed">
                This system is restricted to authorized operators. All access attempts are monitored.
              </p>
            </div>

          </div>

          <p class="text-center text-xs text-slate-500 mt-6">© 2026 CargoNet Systems. Global Logistics Solutions.</p>

        </div>
      </div>
    </div>
  </div>

</body>
</html>