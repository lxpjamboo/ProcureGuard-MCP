<?php
// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Simple validation example
    if (!empty($username) && !empty($email) && !empty($password)) {
        // TODO: Hash password and insert into database (e.g., using PDO/MySQLi)
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $message = "Account created successfully! You can now log in.";
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account</title>
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
                <p class="text-slate-300 text-sm mt-2">Join our platform to manage your tasks and projects effortlessly.</p>
            </div>
            
            <div class="relative z-10 my-8">
                <div class="w-16 h-16 rounded-2xl bg-blue-600/30 border border-blue-500/40 flex items-center justify-center backdrop-blur-md">
                    <i data-lucide="user-plus" class="w-8 h-8 text-blue-400"></i>
                </div>
            </div>

            <div class="relative z-10 text-xs text-slate-400">
                Already have an account? 
                <a href="login.php" class="text-blue-400 hover:underline font-semibold ml-1">Sign In</a>
            </div>
        </div>

        <!-- Right Side: Form Panel -->
        <div class="md:w-1/2 p-8 flex flex-col justify-center bg-slate-900/90">
            <h3 class="text-2xl font-bold text-white mb-1">Create your account</h3>
            <p class="text-slate-400 text-xs mb-6">It's just few minutes and free!</p>

            <?php if (!empty($message)): ?>
                <div class="mb-4 p-3 rounded-lg bg-blue-950/60 border border-blue-500/40 text-blue-200 text-xs">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" id="signupForm" onsubmit="return validateForm()" class="space-y-4">
                
                <!-- Username -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="username" id="username" placeholder="Username" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 transition">
                </div>

                <!-- Email -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" id="email" placeholder="webmail@gmail.com" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 transition">
                </div>

                <!-- Password -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="password" placeholder="Password" required
                        class="w-full pl-10 pr-16 py-2.5 bg-slate-800/80 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 transition">
                    <button type="button" onclick="togglePasswordVisibility('password', 'toggleIcon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-slate-400 hover:text-white">
                        <span id="toggleIcon">Show</span>
                    </button>
                </div>

                <!-- Terms & Conditions -->
                <div class="flex items-center">
                    <input type="checkbox" id="terms" name="terms" required class="w-4 h-4 accent-blue-600 rounded bg-slate-800 border-slate-700">
                    <label for="terms" class="ml-2 text-xs text-slate-400">I agree with terms and conditions.</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm rounded-lg shadow-lg shadow-blue-600/30 transition duration-200">
                    Create my account
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

        function validateForm() {
            const pwd = document.getElementById('password').value;
            if (pwd.length < 6) {
                alert('Password must be at least 6 characters long.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>