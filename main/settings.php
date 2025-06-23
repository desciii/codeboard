<?php
session_start();
include('db.php');

$username = $_SESSION['username'] ?? null;

if (!$username) {
  header("Location: login.php");
  exit();
}

// ✅ Upload Profile Picture Logic
if (isset($_POST['upload'])) {
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $targetDir = "../css/images/";
    $fileName = basename($_FILES['profile_picture']['name']);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Allow only images
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileType, $allowedTypes)) {
      move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile);
      mysqli_query($conn, "UPDATE users SET profile_picture = '$fileName' WHERE username = '$username'");
      header("Location: settings.php?success=1");
      exit();
    } else {
      $error = "Only JPG, JPEG, PNG, GIF files are allowed.";
    }
  } else {
    $error = "Error uploading file.";
  }
}


$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' LIMIT 1");
$user = mysqli_fetch_assoc($userQuery);
$profilePicture = $user['profile_picture'] ?? null;
$imagePath = ($profilePicture && file_exists("../css/images/" . $profilePicture)) ? "../css/images/" . $profilePicture : "../css/images/pfp.png";


if (isset($_POST['change_username'])) {
  $newUsername = mysqli_real_escape_string($conn, $_POST['new_username']);

  $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$newUsername'");
  if (mysqli_num_rows($check) > 0) {
    $error = "Username already taken.";
  } else {

    mysqli_query($conn, "UPDATE users SET username = '$newUsername' WHERE username = '$username'");

    // ✅ ALSO update username in related tables!
    mysqli_query($conn, "UPDATE posts SET username = '$newUsername' WHERE username = '$username'");
    mysqli_query($conn, "UPDATE comments SET username = '$newUsername' WHERE username = '$username'");
    mysqli_query($conn, "UPDATE likes SET username = '$newUsername' WHERE username = '$username'");


    $_SESSION['username'] = $newUsername;
    header("Location: settings.php?success=2");
    exit();
  }
}

if (isset($_GET['success'])) {
  if ($_GET['success'] == 1) echo "<div class='success-msg'>Profile picture updated!</div>";
  if ($_GET['success'] == 2) echo "<div class='success-msg'>Username updated successfully!</div>";
  if ($_GET['success'] == 3) echo "<div class='success-msg'>Password changed successfully!</div>";
}

if (isset($_POST['change_password'])) {
  $currentPassword = $_POST['current_password'];
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];

  $userQuery = mysqli_query($conn, "SELECT password FROM users WHERE username = '$username'");
  $user = mysqli_fetch_assoc($userQuery);
  $hashedPassword = $user['password'];

  if (!password_verify($currentPassword, $hashedPassword)) {
    $error = "Current password is incorrect.";
  } elseif ($newPassword !== $confirmPassword) {
    $error = "New passwords do not match.";
  } else {
    $hashedNew = password_hash($newPassword, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE users SET password = '$hashedNew' WHERE username = '$username'");
    header("Location: settings.php?success=3");
    exit();
  }
}

if (isset($_POST['delete_account'])) {
  $postIdsResult = mysqli_query($conn, "SELECT id FROM posts WHERE username = '$username'");
  while ($post = mysqli_fetch_assoc($postIdsResult)) {
    $postId = $post['id'];
    mysqli_query($conn, "DELETE FROM likes WHERE post_id = '$postId'");
    mysqli_query($conn, "DELETE FROM comments WHERE post_id = '$postId'");
  }

  mysqli_query($conn, "DELETE FROM posts WHERE username = '$username'");

  mysqli_query($conn, "DELETE FROM likes WHERE username = '$username'");
  mysqli_query($conn, "DELETE FROM comments WHERE username = '$username'");
  mysqli_query($conn, "DELETE FROM users WHERE username = '$username'");

  session_destroy();
  header("Location: login.php?deleted=1");
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings</title>
  <link rel="stylesheet" href="../css/dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="icon" href="../css/images/logo.png" type="image/png">

  <style>
    .settings-section {
      background: #1a1a1a;
      padding: 25px 20px;
      border-radius: 10px;
      width: 350px;
      margin-top: 30px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.5);
    }

    .settings-section h3 {
      color: #fff;
      font-size: 20px;
      margin-bottom: 15px;
      text-align: center;
    }

    .settings-section img {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      object-fit: cover;
      margin: 0 auto 15px;
      display: block;
      border: 3px solid #4caf50;
    }

    .settings-section input[type="file"] {
      display: none;
    }

    .custom-file-label {
      background-color: #333;
      color: #ccc;
      padding: 8px 12px;
      border-radius: 5px;
      display: inline-block;
      cursor: pointer;
      margin-bottom: 12px;
      text-align: center;
      transition: background-color 0.2s ease, color 0.2s ease;
      font-size: 14px;
    }

    .custom-file-label:hover {
      background-color: #444;
      color: #fff;
    }

    .settings-section button {
      background-color: #4caf50;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      cursor: pointer;
      display: block;
      margin: 0 auto;
      font-size: 14px;
      transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .settings-section button:hover {
      background-color: #43a047;
      transform: translateY(-1px);
    }

    .error-msg,
    .success-msg {
      text-align: center;
      font-size: 14px;
      margin-top: 10px;
    }

    .error-msg {
      color: #ff4d4d;
    }

    .success-msg {
      color: #4caf50;
    }

    html,
    body {
      height: 100%;
      margin: 0;
      overflow: hidden;
    }

    #container {
      display: flex;
      height: 95vh;
      overflow: hidden;
      border: 3px solid #333;
      border-radius: 12px;
      box-sizing: border-box;
    }

    #sidebar {
      height: 95vh;
      overflow-y: hidden;
      flex-shrink: 0;
    }

    #main {
      flex: 1;
      overflow-y: auto;
      padding-right: 10px;
      scrollbar-width: thin;
      scrollbar-color: #4caf50 #1a1a1a;
    }

    #main::-webkit-scrollbar {
      width: 8px;
    }

    #main::-webkit-scrollbar-thumb {
      background-color: #4caf50;
      border-radius: 10px;
      border: 2px solid #1a1a1a;
    }

    #main::-webkit-scrollbar-track {
      background-color: #1a1a1a;
    }

    #credits {
      width: 20%;
      border-left: 3px solid #1f1818;
      padding-left: 10px;
      font-family: "Inter", sans-serif;
    }

    .post {
      word-wrap: break-word;
      overflow-wrap: break-word;
      background: #1a1a1a;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
    }

    .post pre {
      white-space: pre-wrap;
      word-break: break-word;
      overflow-x: auto;
      background: #0d0d0d;
      padding: 10px;
      border-radius: 5px;
      color: #ccc;
    }

    .like-form {
      margin-top: 8px;
    }

    .like-btn {
      background: none;
      border: none;
      color: #ccc;
      cursor: pointer;
      font-size: 14px;
    }

    .like-btn:hover {
      color: #ff4d4d;
    }

    .settings-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin: 0 auto;
      max-width: 350px;
      /* Match your section width */
    }

    .settings-section {
      background: #1a1a1a;
      padding: 25px 20px;
      border-radius: 10px;
      width: 100%;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.5);
    }

    /* Make the password section span both columns */
    .password-section {
      grid-column: 1 / -1;
    }

    .danger-zone {
      border: 2px solid #ff4d4d;
    }

    .danger-zone button:hover {
      background-color: #e60000;
    }

@media screen and (max-width: 559px) {
  html, body {
    height: auto;
    overflow-x: hidden;
  }

  #container {
    display: block;
    height: 95vh;
    overflow: visible;
    padding: 10px;
  }

  #sidebar {
    display: flex;
    width: 100%;
    margin-bottom: 20px;
    height: auto;
    border-radius: 25px;
  }

  #sidebar-links {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 40px;
    width: 100%;
  }

  #sidebar-links li {
    list-style: none;
  }

  #sidebar-links a {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 48px;
    background-color: #333;
    border-radius: 50%;
    color: transparent;
    font-size: 0;
  }

  #sidebar-links i {
    font-size: 25px;
    color: #fff;
  }

  #sidebar h1 {
    color: transparent;
    font-size: 0;
  }

  #dashboard-link i {
    color: #4caf50 !important;
  }

  #main {
    height: calc(85vh - 150px); /* adjust if needed for your sidebar height */
    width: 95%;
    overflow-y: auto; /* THIS makes #main scrollable */
  }

  #main h1 {
    font-size: 20px;
    text-align: center;
    margin-bottom: 15px;
    align-items: center;
  }

  #createpost {
    display: inline-block;
    width: auto;
    padding: 8px 14px;
    font-size: 14px;
    margin-bottom: 15px;
  }

  .settings-container {
    display: flex;
    width: 80%;
    max-width: 400px;
    margin: 0 auto;
    justify-content: center;
    align-items: center;
  }

  .settings-section {
    width: 100%;
  }


  form {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  form select,
  form button {
    width: 100%;
  }

  .post {
    padding: 12px;
  }

  .post h3 {
    font-size: 14px;
    line-height: 1.3;
  }

  .post pre {
    font-size: 12px;
  }

  .comment-form input {
    width: calc(100% - 35px);
  }

  #credits {
    display: none;
  }
}

@media screen and (max-width: 940px) {
  #credits {
    display: none;
  }

  #sidebar-links a {
    display: flex;
    align-items: center;
    margin-top: 50px;
  }

  #sidebar-links a i {
    font-size: 40px;   
    color: #fff;       
    gap: 30px;
    margin: 0 auto;
    justify-content: center;
    align-items: center;
  }

  #sidebar-links a::after {
    content: "";
  }

  #sidebar-links a {
    color: transparent;
    font-size: 0;
  }

    .settings-container {
    display: flex;
    width: 70%;
    max-width: 400px;
    margin: 0 auto;
    justify-content: center;
    align-items: center;
  }

}
</style>


</head>

<body>
  <div id="container">
    <div id="sidebar">
      <h1>Code Board</h1>
      <ul id="sidebar-links">
        <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="myposts.php"><i class="fa-solid fa-file-lines"></i> My Posts</a></li>
        <li><a href="settings.php" style="background-color: #333; color: #fff" id="dashboard-link"><i class="fa-solid fa-gear"></i> Settings</a></li>
        <li><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>

    <div id="main">
      <h1>Settings</h1>
      <div class="settings-container">
        <!-- Profile Picture Section (First) -->
        <div class="settings-section">
          <h3 style="color:#fff;">Change Profile Picture</h3>
          <img src="<?php echo $imagePath; ?>" alt="Profile Picture" />
          <form action="settings.php" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_picture" id="profile_picture" required>
            <label for="profile_picture" class="custom-file-label" style="align-items: center; justify-content: center; display: flex; cursor: pointer;">
              <i class="fa-solid fa-file-upload"></i> Choose Profile Picture
            </label>
            <button type="submit" name="upload"><i class="fa-solid fa-upload"></i> Upload</button>
          </form>
          <?php if (isset($error)) echo "<div class='error-msg'>$error</div>"; ?>
          <?php if (isset($_GET['success'])) echo "<div class='success-msg'>Profile picture updated!</div>"; ?>
        </div>

        <!-- Password Section (Second) -->
        <div class="settings-section">
          <h3>Change Password</h3>
          <form action="settings.php" method="post">
            <input type="password" name="current_password" placeholder="Current Password" required
              style="width:95%; padding:8px; border-radius:5px; margin-bottom:8px; border:1px solid #333; background:#111; color:#fff;">
            <input type="password" name="new_password" placeholder="New Password" required
              style="width:95%; padding:8px; border-radius:5px; margin-bottom:8px; border:1px solid #333; background:#111; color:#fff;">
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required
              style="width:95%; padding:8px; border-radius:5px; margin-bottom:10px; border:1px solid #333; background:#111; color:#fff;">
            <button type="submit" name="change_password"><i class="fa-solid fa-key"></i> Update Password</button>
          </form>
        </div>

        <!-- Username Section (Third) -->
        <div class="settings-section">
          <h3>Change Username</h3>
          <form action="settings.php" method="post">
            <input type="text" name="new_username" placeholder="New Username" required
              style="width:100%; padding:8px; border-radius:5px; margin-bottom:10px; border:1px solid #333; background:#111; color:#fff;">
            <button type="submit" name="change_username"><i class="fa-solid fa-pen"></i> Update Username</button>
          </form>
        </div>

        <!-- Delete Account Section (Fourth) -->
        <div class="settings-section danger-zone">
          <i class="fa-solid fa-triangle-exclamation" style="font-size: 30px; display: flex; align-items: center; justify-content: center; margin: 0 auto;"></i>
          <h3 style="color: #ff4d4d;">Danger Zone</h3>
          <form action="settings.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete your account? This cannot be undone.');">
            <button type="submit" name="delete_account" style="background-color:#ff4d4d; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer;">
              <i class="fa-solid fa-trash"></i> Delete Account
            </button>
          </form>
        </div>

      </div>
    </div>
    <div id="credits" style="text-align:center;">
      <h1>This website is created <br>by @desciii.</h1>
      <img src="../css/images/download (3).jpg" alt="desciii profile picture" style="width:220px; height:220px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid #4caf50; margin-top:30px;">
      <h2 style="color:#4caf50; margin:0;">@desciii</h2>
      <p style="color:#ccc; font-size:14px; margin:5px 0 10px;">Aspiring web developer & programmer.<br>PHP | CSS | HTML | JS | <br>Soon: Python, React </p>
      <div style="margin-top:8px;">
        <a href="https://github.com/desciii" target="_blank" style="color:#4caf50; text-decoration:none; display:block; margin-bottom:5px;"><i class="fa-brands fa-github"></i> GitHub</a>
        <a href="https://www.tiktok.com/@userw7go3r7op1" target="_blank" style="color:#4caf50; text-decoration:none; display:block; margin-bottom:5px;"><i class="fa-brands fa-tiktok""></i> Tiktok</a>
        <a href=" https://www.facebook.com/marlouangelo.panungcat/" target="_blank" style="color:#4caf50; text-decoration:none; display:block;"><i class="fa-brands fa-facebook"></i> Facebook</a>
      </div>
    </div>
  </div>
</body>

</html>