<?php
// forgot_password.php
require_once 'config.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}
$vendorAutoload = 'vendor/autoload.php';
$error = '';
$success = '';
$smtp_username = "your_email@gmail.com"; // Replace with actual email
$smtp_password = "your_app_password"; // Replace with actual app password
$transport = (new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
                ->setUsername($smtp_username)
                ->setPassword($smtp_password);           

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Моля, въведете имейл адрес";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if email exists
        $query = "SELECT id, name FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing tokens for this email
            $deleteQuery = "DELETE FROM password_resets WHERE email = :email";
            $deleteStmt = $db->prepare($deleteQuery);
            $deleteStmt->bindParam(":email", $email);
            $deleteStmt->execute();
            
            // Insert new token
            $insertQuery = "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bindParam(":email", $email);
            $insertStmt->bindParam(":token", $token);
            $insertStmt->bindParam(":expires_at", $expires);
            
            if ($insertStmt->execute()) {
                $message = (new Swift_Message('Password Reset Request'))
                ->setFrom([$smtp_username => 'Your Website'])
                ->setTo([$email])
                ->setBody(
                    "Click the link to reset your password: " . $link . "\n\n" .
                    "This link will expire in 1 hour.\n\n" .
                    "If you didn't request this, please ignore this email."
                );

            $result = $mailer->send($message);
            } else {
                $error = "Грешка при генериране на линк за възстановяване";
            }
        } else {
            $error = "Потребител с този имейл не съществува";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Забравена парола - Фактурчо</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Same styles as login.php */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    :root {
      --primary: #0E2038;
      --secondary: #146DAF;
      --accent: #769F86;
      --light-accent: #7CCC87;
      --dark: #05112C;
      --darker: #061D42;
      --text: #333333;
      --light-text: #FFFFFF;
      --bg: #F8FAFC;
      --card-bg: #FFFFFF;
      --border: #E2E8F0;
    }

    body {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .forgot-container {
      background: var(--card-bg);
      border-radius: 15px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 450px;
      overflow: hidden;
    }

    .forgot-header {
      background: var(--primary);
      color: white;
      padding: 30px;
      text-align: center;
    }

    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 15px;
    }

    .logo-img {
      height: 40px;
      width: auto;
    }

    .forgot-header h1 {
      font-size: 1.8rem;
      margin-bottom: 5px;
    }

    .forgot-header p {
      opacity: 0.8;
    }

    .forgot-form {
      padding: 30px;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--primary);
    }

    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--secondary);
      box-shadow: 0 0 0 3px rgba(20, 109, 175, 0.1);
    }

    .btn {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-primary {
      background: var(--secondary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary);
    }

    .alert {
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }

    .alert-error {
      background: #FEE2E2;
      color: #991B1B;
      border: 1px solid #FECACA;
    }

    .alert-success {
      background: #D1FAE5;
      color: #065F46;
      border: 1px solid #A7F3D0;
    }

    .forgot-links {
      text-align: center;
      margin-top: 20px;
    }

    .forgot-links a {
      color: var(--secondary);
      text-decoration: none;
      transition: color 0.3s;
    }

    .forgot-links a:hover {
      color: var(--primary);
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="forgot-container">
    <div class="forgot-header">
      <div class="logo-container">
        <img src="logo.png" alt="Фактурчо" class="logo-img">
        <h1>Фактурчо</h1>
      </div>
      <p>Възстановяване на парола</p>
    </div>

    <div class="forgot-form">
      <?php if ($error): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> 
          <div style="display: inline-block; text-align: left;">
            <?php echo $success; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!$success): ?>
        <form method="POST" action="">
          <div class="form-group">
            <label for="email">Имейл адрес</label>
            <input type="email" class="form-control" id="email" name="email" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <small style="color: #64748B; font-size: 0.8rem;">
              Ще ви изпратим линк за възстановяване на паролата на този имейл.
            </small>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Изпрати линк за възстановяване
          </button>
        </form>
      <?php endif; ?>

      <div class="forgot-links">
        <p><a href="login.php">← Обратно към входа</a></p>
      </div>
    </div>
  </div>
</body>
</html>