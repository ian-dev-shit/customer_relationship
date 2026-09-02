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
<html lang="en" class="scroll-smooth">
<head>
    <?php include_once 'src/components/head.php'; ?>
</head>
<body class="bg-slate-950 text-slate-900 font-sans antialiased min-h-screen flex flex-col justify-between overflow-x-hidden">

    <?php include_once 'src/components/header.php'; ?>

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
                <div class="w-full max-w-md">
                    <div class="bg-slate-950/80 border border-white/15 p-8 sm:p-10 rounded-3xl backdrop-blur-2xl shadow-2xl relative">
                        
                        <div class="text-center mb-8">
                            <div class="w-12 h-12 bg-brand-blue/20 border border-brand-blue/30 rounded-2xl flex items-center justify-center text-brand-blue text-xl mx-auto mb-3 shadow-lg">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white">PRIORITY HANDLING</h3>
                            <p class="text-slate-400 text-xs mt-1">Enter your internal credentials to access the console</p>
                        </div>

                        <?php include_once 'src/components/error_handling.php'; ?>
                        
                        <form method="POST" action="login.php" class="space-y-5">
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

                            <button type="submit" class="w-full bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-right-to-bracket"></i> Sign In 
                            </button>
                        </form>


                    </div>
                </div>

            </div>
        </div>

    </main>

    <?php include 'src/components/footer.php'; ?>

    <script src="assets/js/footer.js"></script>
</body>
</html>