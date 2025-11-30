<?php
// register.php
require_once 'config.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Моля, попълнете всички полета";
    } elseif ($password !== $confirm_password) {
        $error = "Паролите не съвпадат";
    } elseif (strlen($password) < 6) {
        $error = "Паролата трябва да е поне 6 символа";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = "Потребител с този имейл вече съществува";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Регистрацията е успешна! Можете да влезете в системата.";
                header("refresh:2;url=login.php");
            } else {
                $error = "Грешка при регистрация. Моля, опитайте отново.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Регистрация - Фактурчо</title>
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

    .register-container {
      background: var(--card-bg);
      border-radius: 15px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 450px;
      overflow: hidden;
    }

    .register-header {
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

    .register-header h1 {
      font-size: 1.8rem;
      margin-bottom: 5px;
    }

    .register-header p {
      opacity: 0.8;
    }

    .register-form {
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

    .register-links {
      text-align: center;
      margin-top: 20px;
    }

    .register-links a {
      color: var(--secondary);
      text-decoration: none;
      transition: color 0.3s;
    }

    .register-links a:hover {
      color: var(--primary);
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <div class="register-header">
      <div class="logo-container">
        <img src="logo.png" alt="Фактурчо" class="logo-img">
        <h1>Фактурчо</h1>
      </div>
      <p>Създаване на нов профил</p>
    </div>

    <div class="register-form">
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

      <form method="POST" action="">
        <div class="form-group">
          <label for="name">Име и фамилия</label>
          <input type="text" class="form-control" id="name" name="name" required 
                 value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>

        <div class="form-group">
          <label for="email">Имейл адрес</label>
          <input type="email" class="form-control" id="email" name="email" required 
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
          <label for="password">Парола</label>
          <input type="password" class="form-control" id="password" name="password" required>
          <small style="color: #64748B; font-size: 0.8rem;">Паролата трябва да е поне 6 символа</small>
        </div>

        <div class="form-group">
          <label for="confirm_password">Потвърдете паролата</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="fas fa-user-plus"></i> Регистрация
        </button>
      </form>

      <div class="register-links">
        <p>Вече имате профил? <a href="login.php">Влезте в системата</a></p>
      </div>
    </div>
  </div>
</body>
</html>