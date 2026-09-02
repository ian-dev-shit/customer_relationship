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
<html lang="en" class="scroll-smooth">
<head>
    <?php include_once 'src/components/head.php'; ?>
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between overflow-x-hidden">

    <!-- REUSABLE HEADER -->
    <?php include_once 'src/components/header.php'; ?>

    <!-- MAIN AUTH SECTION WITH WAREHOUSE BACKGROUND -->
    <main class="relative w-full min-h-[calc(100vh-120px)] flex items-center justify-center overflow-hidden bg-slate-950 text-white py-12">
        
        <!-- Background Image & Dark Overlay -->
        <div class="absolute inset-0 opacity-30">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1600&q=80" alt="Warehouse Background" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-slate-950/80 to-brand-darkblue/50 z-10"></div>

        <!-- Centered OTP Card Container -->
        <div class="relative z-20 max-w-md w-full px-4">
            <div class="bg-slate-950/85 border border-white/15 p-8 sm:p-10 rounded-3xl backdrop-blur-2xl shadow-2xl relative">
                
                <!-- Header Icon & Title -->
                <div class="text-center mb-6">
                    <div class="w-12 h-12 bg-sky-500/20 border border-sky-500/30 rounded-2xl flex items-center justify-center text-sky-400 text-xl mx-auto mb-3 shadow-lg">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h2 class="text-2xl font-extrabold text-white tracking-tight">Security Verification</h2>
                    <p class="text-xs text-slate-400 mt-1">Enter the 6-digit verification code sent to your registered email</p>
                </div>

                <!-- PHP Error Message Display -->
               <?php include_once 'src/components/error_handling.php' ?>

                <!-- OTP Form -->
                <form method="POST" action="otp_verification.php" class="space-y-5">
                    <div>
                        <label for="otp_code" class="block text-xs font-semibold text-slate-300 mb-2 text-center">
                            Enter OTP Code
                        </label>
                        <div class="relative">
                            <input type="text" id="otp_code" name="otp_code" required maxlength="6" autofocus inputmode="numeric" pattern="[0-9]{6}"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-center text-2xl tracking-[0.4em] font-mono focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all placeholder:text-slate-600 placeholder:tracking-normal"
                                   placeholder="000000">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check-circle"></i> Verify Code
                    </button>
                </form>

                <!-- Navigation Controls -->
                <div class="mt-6 pt-5 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                    <a href="login.php" class="hover:text-white transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Login
                    </a>
                </div>

                <!-- Info Alert Banner -->
                <div class="mt-5 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 flex gap-2.5 items-start">
                    <i class="fa-solid fa-circle-info text-amber-400 text-sm shrink-0 mt-0.5"></i>
                    <p class="text-[11px] text-amber-200/80 leading-relaxed">
                        If you didn’t receive the code, please check your spam folder or contact system support.
                    </p>
                </div>

            </div>
        </div>

    </main>

    <!-- REUSABLE FOOTER -->
    <?php include_once 'src/components/footer.php'; ?>

</body>
</html>