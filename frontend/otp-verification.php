<?php
session_start();
include './src/helpers/api_helper.php';

$error = "";
$success = false;
$email = $_SESSION['temp_email'] ?? '';

// If no email in session, redirect back to login
if (empty($email)) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_payload = [
        'email' => $email,
        'otp' => $_POST["otp"] ?? '',
        'otp_type' => $_POST["otp_type"] ?? 'login' // login, registration, or password_reset
    ];

    $response = make_api_request('/login-verify', 'POST', $otp_payload, true);

    if ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
        $_SESSION["access_token"] = $response['data']['access_token'];
        
        $token_parts = explode('.', $response['data']['access_token']);
        $payload = json_decode(base64_decode($token_parts[1]), true);
        
        $_SESSION["username"] = $payload["sub"] ?? $_SESSION["temp_username"] ?? "user";
        $_SESSION["role"] = $payload["role"] ?? "cashier";
        
        // Clear temporary session data
        unset($_SESSION['temp_email']);
        unset($_SESSION['temp_username']);
        
        $success = true;
        
        // Redirect based on role
        if ($_SESSION["role"] === "admin") {
            header("Location: views/admin/dashboard.php");
        } else {
            header("Location: views/customer/dashboard.php");
        }
        exit();
    } else {
        $error = $response['data']['detail'] ?? "Invalid OTP. Please try again.";
    }
}

// Resend OTP handler (via AJAX or direct)
if (isset($_GET['resend'])) {
    $resend_payload = ['email' => $email];
    $response = make_api_request('/auth/resend-otp', 'POST', $resend_payload, true);
    
    if ($response['status_code'] == 200) {
        $success_message = "A new OTP has been sent to your email.";
    } else {
        $error = $response['data']['detail'] ?? "Failed to resend OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .otp-input {
            width: 3.5rem;
            height: 4rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border-radius: 0.75rem;
            border: 2px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transition: all 0.2s ease;
            outline: none;
        }
        .otp-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }
        .otp-input.filled {
            border-color: rgba(245, 158, 11, 0.5);
            background: rgba(245, 158, 11, 0.1);
        }
        .otp-input::-webkit-outer-spin-button,
        .otp-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .otp-input[type="number"] {
            appearance: textfield;
            -moz-appearance: textfield;
        }
    </style>
</head>

<!-- Main Background -->
<body class="relative min-h-screen flex items-center justify-center p-4 bg-cover bg-center bg-no-repeat" 
      style="background-image: url('assets/image/espresso.jpg');">
    
    <!-- Dark tint -->
    <div class="absolute inset-0 bg-slate-950/75 backdrop-blur-sm z-0"></div>

    <!-- Transparent / Glassmorphism Container -->
    <div class="relative bg-white/10 backdrop-blur-md p-8 rounded-2xl shadow-[0_8px_32px_0_rgba(0,0,0,0.37)] border border-white/10 w-full max-w-md z-10">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 text-2xl font-bold mb-3 border border-amber-500/20">
                🔐
            </div>
            <h1 class="text-xl font-bold text-white tracking-tight">Verify Your Identity</h1>
            <p class="text-slate-300 text-xs mt-1">Enter the 6-digit code sent to your email</p>
            <?php if (!empty($email)): ?>
                <p class="text-slate-400 text-[11px] mt-2 bg-white/5 px-3 py-1.5 rounded-lg inline-block">
                    📧 <?php echo htmlspecialchars($email); ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-500/20 text-red-300 p-3.5 rounded-xl text-xs font-medium mb-5 border border-red-500/30 flex items-center gap-2">
                <span>⚠️</span> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-500/20 text-green-300 p-3.5 rounded-xl text-xs font-medium mb-5 border border-green-500/30 flex items-center gap-2">
                <span>✅</span> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="otp-verification.php" class="space-y-6" id="otpForm">
            <input type="hidden" name="otp_type" value="login">
            
            <!-- OTP Input Fields -->
            <div>
                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-widest mb-3 text-center">
                    Enter 6-Digit Code
                </label>
                <div class="flex justify-center gap-3" id="otpContainer">
                    <input type="number" max="9" min="0" class="otp-input" id="otp1" maxlength="1" required autofocus>
                    <input type="number" max="9" min="0" class="otp-input" id="otp2" maxlength="1" required>
                    <input type="number" max="9" min="0" class="otp-input" id="otp3" maxlength="1" required>
                    <input type="number" max="9" min="0" class="otp-input" id="otp4" maxlength="1" required>
                    <input type="number" max="9" min="0" class="otp-input" id="otp5" maxlength="1" required>
                    <input type="number" max="9" min="0" class="otp-input" id="otp6" maxlength="1" required>
                </div>
                <!-- Hidden input to store combined OTP -->
                <input type="hidden" name="otp" id="otpHidden" value="">
            </div>

            <!-- OTP Timer & Resend -->
            <div class="flex items-center justify-between text-xs">
                <div class="text-slate-400">
                    Code expires in <span id="timer" class="text-amber-400 font-semibold">02:00</span>
                </div>
                <button type="button" id="resendBtn" onclick="resendOTP()" 
                    class="text-amber-400 hover:text-amber-300 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Resend Code
                </button>
            </div>

            <div class="pt-2">
                <!-- Premium Accent Color Button -->
                <button type="submit" id="verifyBtn"
                    class="w-full bg-amber-600 hover:bg-amber-500 text-white font-medium py-2.5 text-sm rounded-xl transition duration-200 shadow-lg shadow-amber-950/50 flex items-center justify-center gap-2">
                    Verify & Continue <span>→</span>
                </button>
            </div>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-6 pt-6 border-t border-white/5">
            <a href="login.php" class="text-[11px] text-slate-400 hover:text-slate-300 transition">
                ← Back to Login
            </a>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-4">
            <p class="text-[11px] text-slate-400">Secured Workspace Connection.</p>
        </div>
    </div>

    <script>
        // Auto-focus and move between OTP inputs
        const otpInputs = document.querySelectorAll('.otp-input');
        const otpHidden = document.getElementById('otpHidden');
        
        otpInputs.forEach((input, index) => {
            // Auto-focus next input on typing
            input.addEventListener('input', function(e) {
                const value = this.value;
                
                // Only allow single digit
                if (value.length > 1) {
                    this.value = value.slice(0, 1);
                }
                
                // Add filled class if value exists
                if (this.value) {
                    this.classList.add('filled');
                } else {
                    this.classList.remove('filled');
                }
                
                // Move to next input if current is filled
                if (this.value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                // Update hidden input with combined OTP
                updateOTPHidden();
            });
            
            // Handle backspace to move to previous input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].value = '';
                    otpInputs[index - 1].classList.remove('filled');
                    updateOTPHidden();
                }
            });
            
            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                const digits = pastedData.replace(/\D/g, '').slice(0, 6);
                
                digits.split('').forEach((digit, i) => {
                    if (i < otpInputs.length) {
                        otpInputs[i].value = digit;
                        if (digit) {
                            otpInputs[i].classList.add('filled');
                        }
                    }
                });
                
                // Focus on next empty input or last filled
                const lastFilledIndex = Math.min(digits.length, otpInputs.length - 1);
                if (lastFilledIndex < otpInputs.length - 1) {
                    otpInputs[lastFilledIndex + 1].focus();
                } else {
                    otpInputs[otpInputs.length - 1].focus();
                }
                
                updateOTPHidden();
            });
        });
        
        function updateOTPHidden() {
            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value || '';
            });
            otpHidden.value = otp;
        }
        
        // Auto-submit when all 6 digits are filled
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('otpForm');
            const verifyBtn = document.getElementById('verifyBtn');
            
            // Listen for changes on all OTP inputs
            otpInputs.forEach(input => {
                input.addEventListener('input', function() {
                    updateOTPHidden();
                    // Auto-submit if all fields are filled
                    const allFilled = Array.from(otpInputs).every(inp => inp.value !== '');
                    if (allFilled) {
                        // Submit automatically after a small delay
                        setTimeout(() => {
                            form.submit();
                        }, 300);
                    }
                });
            });
        });
        
        // Timer countdown (2 minutes)
        let timeLeft = 120;
        const timerElement = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = 'Expired';
                resendBtn.disabled = false;
                resendBtn.classList.remove('opacity-50');
                return;
            }
            
            timeLeft--;
        }
        
        const timerInterval = setInterval(updateTimer, 1000);
        
        // Resend OTP function
        function resendOTP() {
            if (resendBtn.disabled) return;
            
            resendBtn.disabled = true;
            resendBtn.textContent = 'Sending...';
            
            // Reset timer
            timeLeft = 120;
            clearInterval(timerInterval);
            const newInterval = setInterval(updateTimer, 1000);
            // Store new interval reference
            window.timerInterval = newInterval;
            
            // AJAX request to resend OTP
            fetch('otp-verification.php?resend=1', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Parse success message from response (simplified)
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const successMsg = doc.querySelector('.bg-green-500\\/20');
                
                if (successMsg) {
                    // Show success message
                    const existingSuccess = document.querySelector('.bg-green-500\\/20');
                    if (existingSuccess) {
                        existingSuccess.remove();
                    }
                    
                    const container = document.querySelector('.bg-white\\/10');
                    const form = document.getElementById('otpForm');
                    const successDiv = document.createElement('div');
                    successDiv.className = 'bg-green-500/20 text-green-300 p-3.5 rounded-xl text-xs font-medium mb-5 border border-green-500/30 flex items-center gap-2';
                    successDiv.innerHTML = `<span>✅</span> A new OTP has been sent to your email.`;
                    
                    container.insertBefore(successDiv, form);
                    
                    setTimeout(() => {
                        if (successDiv.parentNode) {
                            successDiv.remove();
                        }
                    }, 5000);
                }
                
                resendBtn.textContent = 'Resend Code';
                resendBtn.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                resendBtn.textContent = 'Resend Code';
                resendBtn.disabled = false;
            });
        }
        
        // Initial timer state
        resendBtn.disabled = true;
        
        // Update timer when page loads
        updateTimer();
        
        // Handle page refresh timer reset
        window.addEventListener('beforeunload', function() {
            if (window.timerInterval) {
                clearInterval(window.timerInterval);
            }
        });
    </script>

</body>
</html>