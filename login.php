<?php
session_start();

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // TODO: Validate password against your database record
        // e.g., if (password_verify($password, $user['password_hash']))

        // Store user session data if needed
        $_SESSION['user'] = $username;

        // Redirect to homepage
        header("Location: index.php");
        exit();
    } else {
        $message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        obsidian: '#0b0f17',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-obsidian text-white min-h-screen flex items-center justify-center p-4">

    <!-- Card Container -->
    <div class="w-full max-w-4xl bg-slate-900/80 rounded-2xl border border-slate-800 shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[520px]">
        
        <!-- Left Side: Hero / Welcome Panel -->
        <div class="md:w-1/2 bg-gradient-to-br from-blue-900 via-indigo-900 to-obsidian p-8 flex flex-col justify-between relative overflow-hidden">
            <!-- Background Decorative Grid/Glow -->
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px]"></div>
            
            <div class="relative z-10">
                <h2 class="text-3xl font-bold tracking-wide">Welcome back . . .</h2>
                <p class="text-slate-300 text-sm mt-2">Sign in to access your dashboard and continue where you left off.</p>
            </div>

            <div class="relative z-10 my-8">
                <div class="w-16 h-16 rounded-2xl bg-blue-600/30 border border-blue-500/40 flex items-center justify-center backdrop-blur-md">
                    <i data-lucide="log-in" class="w-8 h-8 text-blue-400"></i>
                </div>
            </div>

            <div class="relative z-10 text-xs text-slate-400">
                Don't have an account? 
                <a href="signup.php" class="text-blue-400 hover:underline font-semibold ml-1">Sign Up</a>
            </div>
        </div>

        <!-- Right Side: Form Panel -->
        <div class="md:w-1/2 p-8 flex flex-col justify-center bg-slate-900/90">
            <h3 class="text-2xl font-bold text-white mb-1">Sign In</h3>
            <p class="text-slate-400 text-xs mb-6">Sign in to continue</p>

            <?php if (!empty($message)): ?>
                <div class="mb-4 p-3 rounded-lg bg-blue-950/60 border border-blue-500/40 text-blue-200 text-xs">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" id="loginForm" class="space-y-4">
                
                <!-- Username or Email -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="username" id="loginUsername" placeholder="Username or Email" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 transition">
                </div>

                <!-- Password -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required
                        class="w-full pl-10 pr-16 py-2.5 bg-slate-800/80 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 transition">
                    <button type="button" onclick="togglePasswordVisibility('loginPassword', 'loginToggleIcon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-slate-400 hover:text-white">
                        <span id="loginToggleIcon">Show</span>
                    </button>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-slate-400">
                        <input type="checkbox" name="remember" class="w-4 h-4 accent-blue-600 rounded bg-slate-800 border-slate-700 mr-2">
                        Remember me
                    </label>
                    <a href="#" class="text-blue-400 hover:underline">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm rounded-lg shadow-lg shadow-blue-600/30 transition duration-200">
                    LOG IN
                </button>
            </form>
        </div>
    </div>

    <!-- JavaScript Handling -->
    <script>
        lucide.createIcons();

        function togglePasswordVisibility(inputId, buttonTextId) {
            const input = document.getElementById(inputId);
            const buttonText = document.getElementById(buttonTextId);
            if (input.type === "password") {
                input.type = "text";
                buttonText.textContent = "Hide";
            } else {
                input.type = "password";
                buttonText.textContent = "Show";
            }
        }
    </script>
</body>
</html>