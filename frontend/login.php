<?php
session_start();
require_once 'src/helpers/api_helper.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_payload = [
        'email' => $_POST["email"],
        'password' => $_POST["password"]
    ];

    $response = make_api_request('/login', 'POST', $login_payload, true);

    if ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
        $_SESSION["access_token"] = $response['data']['access_token'];
        
        $token_parts = explode('.', $response['data']['access_token']);
        $payload = json_decode(base64_decode($token_parts[1]), true);
        
        $_SESSION["username"] = $payload["sub"] ?? $_POST["username"];
        $_SESSION["role"] = $payload["role"] ?? "cashier";

        header("Location: views/customer/dashboard.php");
        exit();
    } else {
        $error = $response['data']['detail'] ?? "Incorrect username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CargoNet — Customer Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            freight: { 50: '#f0f9ff', 100: '#e0f2fe', 500: '#0ea5e9', 600: '#0284c7', 900: '#0c4a6e', 950: '#082f49' },
            cargo: { amber: '#f59e0b', slate: '#1e293b', dark: '#0f172a', panel: '#111827' }
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    .glass-panel { background: rgba(30, 41, 59, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }
    .fade-in { animation: fadeIn .4s ease-out both; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(10px) } to { opacity:1; transform: translateY(0) } }
    .mesh-bg {
      background-color: #0b1120;
      background-image: radial-gradient(at 0% 0%, hsla(217,91%,30%,1) 0, transparent 50%),
                        radial-gradient(at 100% 0%, hsla(199,85%,24%,1) 0, transparent 50%),
                        radial-gradient(at 100% 100%, hsla(222,70%,20%,1) 0, transparent 50%),
                        radial-gradient(at 0% 100%, hsla(190,80%,20%,1) 0, transparent 50%);
    }
  </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased">

<div class="min-h-screen mesh-bg relative overflow-hidden">
  <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px); background-size: 40px 40px;"></div>

  <div class="grid lg:grid-cols-12 min-h-screen relative z-10">
    <!-- LEFT: Brand -->
    <div class="lg:col-span-7 flex flex-col justify-center px-8 py-12 lg:px-16 xl:px-24">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/10 border border-sky-500/20 text-sky-300 text-xs font-semibold tracking-wide uppercase mb-6 w-fit fade-in">
        <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span> Customer Portal
      </div>
      <h1 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold tracking-tight text-white leading-[1.1] mb-6 fade-in" style="animation-delay:.2s">
        Track & Manage <br>
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-cyan-300">Your Shipments</span>
      </h1>
      <p class="text-lg text-slate-300 max-w-xl mb-10 leading-relaxed fade-in" style="animation-delay:.3s">
        Access your freight history, documents, and real‑time updates – all in one secure customer portal.
      </p>
      <div class="grid sm:grid-cols-3 gap-6 mb-12 fade-in" style="animation-delay:.4s">
        <div class="glass-panel rounded-xl p-5"><div class="text-2xl font-bold text-white mb-1">24/7</div><div class="text-sm text-slate-400">Tracking Availability</div></div>
        <div class="glass-panel rounded-xl p-5"><div class="text-2xl font-bold text-white mb-1">99.5%</div><div class="text-sm text-slate-400">Data Accuracy</div></div>
        <div class="glass-panel rounded-xl p-5"><div class="text-2xl font-bold text-white mb-1">Instant</div><div class="text-sm text-slate-400">Document Access</div></div>
      </div>
    </div>

    <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3 animate-headShake">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-red-600 shrink-0 mt-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <div class="text-sm font-medium text-red-800">
                    <p>awit</p>
                </div>
            </div>
        <?php endif; ?>

    <!-- RIGHT: Login -->
    <div class="lg:col-span-5 flex items-center justify-center p-6 lg:p-12 bg-slate-900/50 backdrop-blur-sm border-l border-white/5">
      <div class="w-full max-w-md fade-in">
        <div class="bg-slate-900/80 border border-slate-700 rounded-2xl shadow-2xl p-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-19.5-3h19.5m-19.5-3h19.5m-19.5-3h19.5M4.5 3h15a2.25 2.25 0 012.25 2.25v.75a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25v-.75A2.25 2.25 0 014.5 3z"/></svg>
            </div>
            <div><h2 class="text-xl font-bold text-white">Customer Access</h2><p class="text-xs text-slate-400">Secure Login</p></div>
          </div>
          <div class="border-t border-slate-700/50 my-6"></div>

          <?php
            if($error){
              
            }
          ?>

          <form method="POST" action="login.php">
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1.5">Email or Customer ID</label>
              <div class="relative">
                <span class="absolute left-3 top-2.5 text-slate-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg></span>
                <input type="email"
                name="email"
                 class="w-full pl-10 pr-4 py-2.5 bg-slate-800 border border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-slate-100 placeholder-slate-500" 
                 placeholder="customer@example.com">
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
              <div class="relative">
                <span class="absolute left-3 top-2.5 text-slate-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg></span>
                <input type="password" name="password" class="w-full pl-10 pr-4 py-2.5 bg-slate-800 border border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-slate-100 placeholder-slate-500" placeholder="••••••••">
              </div>
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white font-semibold shadow-lg shadow-sky-500/20 transition active:scale-[0.98]">
              Sign in to Customer Portal
            </button>
          </form>

          <div class="mt-6 p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 flex gap-3 items-start">
            <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.949 3.374h14.71c1.73 0 2.813-1.874 1.949-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            <p class="text-xs text-amber-200/80 leading-relaxed">This portal is for customers only. Administrators please use the <a href="admin-login.php" class="text-sky-400 underline">admin login</a>.</p>
          </div>
        </div>
        <p class="text-center text-xs text-slate-500 mt-6">© 2026 CargoNet Systems.</p>
      </div>
    </div>
  </div>
</div>


</body>
</html>