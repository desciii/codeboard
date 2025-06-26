<?php
session_start();
include('db.php');

$savedUsername = $_COOKIE['username'] ?? '';
$popupMessage = ''; // Hold popup message to trigger in JS later

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
    $_SESSION['username'] = $username;

      if (!empty($_POST['remember'])) {
          setcookie("username", $username, time() + (86400 * 30), "/");
          } else {
              setcookie("username", "", time() - 3600, "/");
          }
          header("Location: dashboard.php");
                exit();
            } else {
            $popupMessage = "Incorrect password!";
        }
    } else {
        $popupMessage = "Account does not exist!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Roboto:wght@400;500&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" href="../css/images/logo.png" type="image/png">
  <style>
  #rememberme {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #ccc;
  font-family: 'Roboto', sans-serif;
  gap: 8px;
  cursor: pointer;
}

#rememberme input[type="checkbox"] {
  appearance: none;
  width: 16px;       
  height: 16px;
  border: 2px solid #4caf50;
  border-radius: 4px;
  background-color: transparent;
  position: relative;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

#rememberme input[type="checkbox"]::before {
  content: "";
  position: absolute;
  top: 2px;
  left: 4px;
  width: 4px;
  height: 5px;
  border: solid #4caf50;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
  opacity: 0;
  transition: opacity 0.2s ease;
}

#rememberme input[type="checkbox"]:checked {
  background-color: #4caf50;
}

#rememberme input[type="checkbox"]:checked::before {
  opacity: 1;
  border-color: white;
}
</style>
  </head>
  <body>
    <main>
      <br />
      <h1>Welcome to Code Board</h1>
      <h2>Login to your account</h2>

      <div id="form-container">
        <form action="login.php" method="post" id="login">
          <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($savedUsername); ?>" required />
          <input
            type="password"
            name="password"
            placeholder="Password"
            required
          />
          <label id="rememberme"><input type="checkbox" name="remember"> Remember Me</label>
          <button type="submit" id="loginbutton">Login</button>
          <p>or</p>
        </form>
        <a href="register.php" id="registerbutton" class="button">Register</a>
      </div>
    </main>

    <!-- Popup Notification -->
    <div id="popup" style="
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #ff4d4d;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
        font-family: 'Roboto', sans-serif;
        z-index: 9999;
      ">
      <span id="popup-message"></span>
    </div>

    <script>
      function showPopup(message) {
        const popup = document.getElementById('popup');
        const popupMsg = document.getElementById('popup-message');
        popupMsg.textContent = message;
        popup.style.display = 'block';
        setTimeout(() => {
          popup.style.display = 'none';
        }, 3000);
      }

      // Trigger popup from PHP message if exists
      <?php if (!empty($popupMessage)) : ?>
        window.onload = function() {
          showPopup("<?php echo $popupMessage; ?>");
        };
      <?php endif; ?>
    </script>
  </body>
</html>
