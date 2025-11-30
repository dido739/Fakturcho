<?php
// reset_password.php
require_once 'config.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
$valid_token = false;
$email = '';

// Check if token is valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":token", $token);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        $email = $reset['email'];
        $valid_token = true;
    } else {
        $error = "Невалиден или изтекъл линк за възстановяване";
    }
} else {
    $error = "Липсва токен за възстановяване";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password) || empty($confirm_password)) {
        $error = "Моля, попълнете всички полета";
    } elseif ($password !== $confirm_password) {
        $error = "Паролите не съвпадат";
    } elseif (strlen($password) < 6) {
        $error = "Паролата трябва да е поне 6 символа";
    } else {
        // Update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":email", $email);
        
        if ($stmt->execute()) {
            // Delete used token
            $deleteQuery = "DELETE FROM password_resets WHERE email = :email";
            $deleteStmt = $db->prepare($deleteQuery);
            $deleteStmt->bindParam(":email", $email);
            $deleteStmt->execute();
            
            $success = "Паролата е променена успешно! Можете да влезете в системата с новата парола.";
            $valid_token = false; // Prevent form from showing again
            header("refresh:3;url=login.php");
        } else {
            $error = "Грешка при промяна на паролата. Моля, опитайте отново.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Нова парола - Фактурчо</title>
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

    .reset-container {
      background: var(--card-bg);
      border-radius: 15px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 450px;
      overflow: hidden;
    }

    .reset-header {
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

    .reset-header h1 {
      font-size: 1.8rem;
      margin-bottom: 5px;
    }

    .reset-header p {
      opacity: 0.8;
    }

    .reset-form {
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

    .reset-links {
      text-align: center;
      margin-top: 20px;
    }

    .reset-links a {
      color: var(--secondary);
      text-decoration: none;
      transition: color 0.3s;
    }

    .reset-links a:hover {
      color: var(--primary);
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="reset-container">
    <div class="reset-header">
      <div class="logo-container">
        <img src="logo.png" alt="Фактурчо" class="logo-img">
        <h1>Фактурчо</h1>
      </div>
      <p>Задаване на нова парола</p>
    </div>

    <div class="reset-form">
      <?php if ($error): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
      <?php endif; ?>

      <?php if ($valid_token && !$success): ?>
        <form method="POST" action="">
          <div class="form-group">
            <label for="password">Нова парола</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <small style="color: #64748B; font-size: 0.8rem;">Паролата трябва да е поне 6 символа</small>
          </div>

          <div class="form-group">
            <label for="confirm_password">Потвърдете новата парола</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fas fa-key"></i> Задай нова парола
          </button>
        </form>
      <?php endif; ?>

      <div class="reset-links">
        <p><a href="login.php">← Обратно към входа</a></p>
      </div>
    </div>
  </div>
</body>
</html>