<?php
session_start();
include('db.php');

$popupMessage = '';
$redirectToLogin = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);

    // Check if username already exists
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $popupMessage = "Username already exists!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, password, status, dob) VALUES ('$username', '$hashedPassword', '$status', '$dob')";

        if (mysqli_query($conn, $query)) {
            $popupMessage = "Registration successful! Redirecting...";
            $redirectToLogin = true; // Set to true → handled by JS
        } else {
            $popupMessage = "Insert failed: " . mysqli_error($conn);
        }
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Code Board</title>
    <link rel="stylesheet" href="../css/login.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Roboto:wght@400;500&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <main>
      <a href="login.php" style="text-decoration: none; color: blue; font-size: 13px">Go back to login</a>
      <br />
      <h1>Welcome to Code Board</h1>
      <h2>Let's start with your registration</h2>

      <div id="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="register">
          <input type="text" name="username" placeholder="Please enter a Username" required />
          <input type="password" name="password" placeholder="Please enter a Password" required />
          <br />
          <div id="sdob-container">
            <div class="field-group">
              <label for="status">Status:</label>
              <select name="status" id="status" required>
                <option value="" disabled selected>Status</option>
                <option value="Student">Student</option>
                <option value="Developer">Developer</option>
                <option value="Teacher">Teacher</option>
              </select>
            </div>

            <div class="field-group">
              <label for="dob">Date of Birth:</label>
              <input type="date" id="dob" name="dob" required />
            </div>
          </div>
          <button type="submit">Register</button>
        </form>
      </div>
    </main>

    <!-- Popup Notification -->
    <div id="popup" style="
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #4caf50;
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

      <?php if (!empty($popupMessage)) : ?>
        window.onload = function() {
          showPopup("<?php echo $popupMessage; ?>");
          <?php if ($redirectToLogin) : ?>
            setTimeout(() => {
              window.location.href = "login.php";
            }, 2500); // redirect after popup
          <?php endif; ?>
        };
      <?php endif; ?>
    </script>
  </body>
</html>
