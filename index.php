<?php
// Initialize the session
session_start();

// Include configuration file
require_once "includes/config.php";
require_once "includes/auth.php";

// Wenn der Benutzer bereits eingeloggt ist, zum Dashboard weiterleiten
if (isLoggedIn()) {
    header("location: dashboard.php");
    exit;
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Bitte gib einen Benutzernamen ein.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Bitte gib ein Passwort ein.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password, $role);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;
                            
                            // Redirect user to appropriate page
                            if (isset($_SESSION["redirect_url"])) {
                                $redirect = $_SESSION["redirect_url"];
                                unset($_SESSION["redirect_url"]);
                                header("location: " . $redirect);
                            } else {
                                header("location: dashboard.php");
                            }
                            exit;
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Ungültiger Benutzername oder Passwort.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Ungültiger Benutzername oder Passwort.";
                }
            } else {
                echo "Oops! Etwas ist schiefgelaufen. Bitte versuche es später erneut.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Close connection
    $conn->close();
}

$page_title = "Login";
$extra_css = ["/assets/css/login.css"];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - Interactive Portal" : "Interactive Portal"; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <?php if (isset($extra_css)): foreach ($extra_css as $css): ?>
    <link rel="stylesheet" href="<?php echo $css; ?>">
    <?php endforeach; endif; ?>
    <style>
        :root {
            --bg-image: url('/assets/img/default-bg.jpg');
        }
        
        /* Wichtige Zusatzstyles für das Login-Formular */
        .login-container {
            opacity: 1 !important; 
            transform: none !important;
            z-index: 100;
            position: relative;
        }
        
        .login-card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }
        
        .error-message {
            background-color: rgba(255, 0, 0, 0.1);
            border-left: 4px solid #ff3333;
            color: #ff3333;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .help-block {
            color: #ff3333;
            font-size: 0.85em;
            margin-top: 5px;
        }
        
        .has-error input {
            border-color: #ff3333;
        }
    </style>
</head>
<body>
    <div class="background-overlay"></div>
    <div class="container">
        <header>
            <!-- Header-Inhalt -->
        </header>
        <main>
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <h2>Willkommen</h2>
                        <p>Melde dich an, um fortzufahren</p>
                    </div>
                    
                    <?php if (!empty($login_err)) { ?>
                        <div class="error-message"><?php echo $login_err; ?></div>
                    <?php } ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="login-form">
                        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                            <label for="username">Benutzername</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="username" id="username" class="form-control" value="<?php echo $username; ?>">
                            </div>
                            <span class="help-block"><?php echo $username_err; ?></span>
                        </div>
                        
                        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                            <label for="password">Passwort</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <span class="help-block"><?php echo $password_err; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Interactive Portal. Alle Rechte vorbehalten.</p>
        </footer>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="/assets/js/main.js"></script>
    
    <script>
        // Spezielle Animation nur für die Login-Seite
        document.addEventListener('DOMContentLoaded', function() {
            // Nur GSAP-Animationen für den Login initialisieren, wenn GSAP geladen ist
            if (typeof gsap !== 'undefined') {
                // Einfacher Fade-In für Login-Container
                gsap.from('.login-container', {
                    duration: 1,
                    opacity: 0,
                    ease: 'power2.out'
                });
                
                // Header-Animation
                gsap.from('header', {
                    duration: 0.8,
                    y: -30,
                    opacity: 0,
                    ease: 'power2.out'
                });
                
                // Footer-Animation
                gsap.from('footer', {
                    duration: 0.8,
                    y: 30,
                    opacity: 0,
                    ease: 'power2.out',
                    delay: 0.5
                });
            }
        });
    </script>
</body>
</html>