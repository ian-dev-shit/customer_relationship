<?php
session_start();
require_once 'src/helpers/api_helper.php';

if (!isset($_SESSION["temp_email"])) {
    header("Location: login.php");
    exit();
}

$error = "";
$email = $_SESSION["temp_email"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_code = trim($_POST["otp_code"] ?? '');

    if (empty($otp_code)) {
        $error = "OTP should not empty.";
    } else {
        // 1. Build query string para sa FastAPI router
        $query_string = http_build_query([
            'email' => $email,
            'otp_code' => $otp_code
        ]);

        $endpoint = '/api/auth/verify-otp?' . $query_string;
        $response = make_api_request($endpoint, 'POST', null, false);

        if ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
            $data = $response['data'];

            $_SESSION["access_token"]  = $data["access_token"];
            $_SESSION["refresh_token"] = $data["refresh_token"] ?? null;
            $_SESSION["user_id"]       = $data["user_id"] ?? null;
            $_SESSION["email"]         = $email;

            // 2. Decode JWT token para sa fallback role
            $user_role = strtolower($data["role"] ?? "");
            if (empty($user_role)) {
                $token_parts = explode('.', $data["access_token"]);
                if (count($token_parts) === 3) {
                    $payload   = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
                    $user_role = strtolower($payload["role"] ?? "customer");
                } else {
                    $user_role = "customer";
                }
            }
            $_SESSION["role"] = $user_role;

            // 3. Fetch Profile sa Supabase gamit ang Header
            $userId = $_SESSION["user_id"] ?? $data["user_id"] ?? null;

            if ($userId) {
                $headers = [
                    'x-user-id: ' . $userId
                ];

                // BAGUHIN DITO: Palitan ng `false` ang 4th parameter ($is_form_data)
                $profile_res = make_api_request('/api/v1/portal/profile', 'GET', null, false, $headers);

                if ($profile_res['status_code'] == 200 && !empty($profile_res['data'])) {
                    $profile = $profile_res['data'];
                    
                    $_SESSION['first_name'] = $profile['first_name'] ?? '';
                    $_SESSION['last_name']  = $profile['last_name'] ?? '';
                    $_SESSION['user_name']  = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')) ?: 'Sales Agent';
                    $_SESSION['agent_id']   = $profile['agent_id'] ?? $profile['id'] ?? 'SA-014';

                    if (!empty($profile['role'])) {
                        $user_role = strtolower($profile['role']);
                        $_SESSION['role'] = $user_role;
                    }
                }
            }

            // Linisin ang temporary session email
            unset($_SESSION["temp_email"]);

            // 4. Dynamic Redirect
            if ($user_role === "admin") {
                header("Location: /src/views/admin/dashboard.php");
            } else if ($user_role === "sales_agent" || $user_role === "sales") {
                header("Location: /src/views/sales_agent/dashboard.php");
            } else {
                header("Location: /src/views/customer/dashboard.php");
            }
            exit();
        } else {
            $err_data = $response['error'] ?? ($response['data']['detail'] ?? "Invalid OTP or Expired.");
            $error    = is_array($err_data) ? json_encode($err_data) : $err_data;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OTP Verification · Rising Red Dragon</title>

  <!-- ===== HEADER SECTION ===== -->
  <!-- This block can be moved to a separate header include file -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/otp.css">
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
          Verify Your <br />
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-cyan-300">Identity</span>
        </h1>

        <p class="text-lg text-slate-300 max-w-xl mb-10 leading-relaxed fade-in" style="animation-delay:.3s">
          We’ve sent a 6-digit code to your registered email address. Enter it below to continue.
        </p>

        <div class="grid sm:grid-cols-3 gap-6 mb-12 fade-in" style="animation-delay:.4s">
          <div class="glass-panel rounded-xl p-5">
            <div class="text-2xl font-bold text-white mb-1">📧</div>
            <div class="text-sm text-slate-400">Code sent to your email</div>
          </div>
          <div class="glass-panel rounded-xl p-5">
            <div class="text-2xl font-bold text-white mb-1">⏱️</div>
            <div class="text-sm text-slate-400">Expires in 2 minutes</div>
          </div>
          <div class="glass-panel rounded-xl p-5">
            <div class="text-2xl font-bold text-white mb-1">🔐</div>
            <div class="text-sm text-slate-400">Secure verification</div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-6 text-sm text-slate-400 fade-in" style="animation-delay:.5s">
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.746 3.746 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
            Protected by AES-256
          </span>
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.746 3.746 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
            Secure channel
          </span>
        </div>

      </div>

      <!-- RIGHT COLUMN – OTP Form -->
      <div class="lg:col-span-5 flex items-center justify-center p-6 lg:p-12 bg-slate-900/50 backdrop-blur-sm border-l border-white/5">

        <div class="w-full max-w-md fade-in" style="animation-delay:.3s">

          <div class="bg-slate-900/80 border border-slate-700 rounded-2xl shadow-2xl p-8">

            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-19.5-3h19.5m-19.5-3h19.5m-19.5-3h19.5M4.5 3h15a2.25 2.25 0 012.25 2.25v.75a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25v-.75A2.25 2.25 0 014.5 3z"/></svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-white tracking-tight">CargoNet</h2>
                <p class="text-xs text-slate-400">Secure Verification</p>
              </div>
            </div>

            <div class="border-t border-slate-700/50 my-6"></div>

                <?php if (!empty($error)): ?>
                  <div class="bg-red-500/10 border border-red-500/50 text-red-400 text-sm p-3 rounded-lg mb-6 break-words">
                      <?= htmlspecialchars(is_array($error) ? json_encode($error) : $error) ?>
                  </div>
              <?php endif; ?>

            <form method="POST" action="otp_verification.php" class="space-y-6">
                <div>
                    <label for="otp_code" class="block text-sm font-medium text-slate-300 mb-2 text-center">
                        Enter OTP Code
                    </label>
                    <input type="text" id="otp_code" name="otp_code" required maxlength="6" autofocus
                          class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none text-white text-center text-2xl tracking-widest font-mono"
                          placeholder="000000">
                </div>

                <button type="submit" 
                        class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 font-medium rounded-lg text-white text-sm transition duration-150">
                    Verify Code
                </button>
            </form>

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

          </div>

          <p class="text-center text-xs text-slate-500 mt-6">© 2026 CargoNet Systems. Global Logistics Solutions.</p>

        </div>
      </div>
    </div>
  </div>

</body>
</html>