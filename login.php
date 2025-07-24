<?php
include 'config.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password' AND role='user'");
    if($result->num_rows){
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GameVault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-xl);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .form-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            z-index: 1;
            margin-top: 0.75rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--gray-light);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            margin: 0;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }
        
        .login-btn {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .login-links {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-light);
        }
        
        .login-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 1rem;
        }
        
        .login-links a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .back-btn {
            position: absolute;
            top: 2rem;
            left: 2rem;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }
        
        .back-btn:hover {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 2rem;
                margin: 1rem;
            }
            
            .back-btn {
                top: 1rem;
                left: 1rem;
            }
        }
    </style>
</head>
<body>
    <button class="back-btn" onclick="window.location.href='index.php'">
        <i class="fas fa-arrow-left"></i>
    </button>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>
                    <i class="fas fa-gamepad"></i>
                    Welcome Back
                </h1>
                <p>Sign in to your GameVault account</p>
            </div>
            
            <?php if($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <i class="fas fa-user"></i>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           required 
                           placeholder="Enter your username"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>
            
            <div class="login-links">
                <p>Don't have an account? 
                   <a href="register.php">
                       <i class="fas fa-user-plus"></i>
                       Create Account
                   </a>
                </p>
                <p><a href="index.php">
                    <i class="fas fa-home"></i>
                    Back to Store
                </a></p>
            </div>
        </div>
    </div>

    <script>
        // Add loading animation to form submission
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.querySelector('.login-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            btn.disabled = true;
            
            // Re-enable if there's an error (page doesn't redirect)
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 3000);
        });

        // Add focus animations
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>