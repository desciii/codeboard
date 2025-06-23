<?php
session_start();
include('db.php');

$username = $_SESSION['username'] ?? null;
$viewedUser = $_GET['username'] ?? null;

if (!$viewedUser) {
  echo "User not specified.";
  exit();
}

$viewedUserEscaped = mysqli_real_escape_string($conn, $viewedUser);
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE username = '$viewedUserEscaped' LIMIT 1");

if (mysqli_num_rows($userQuery) == 0) {
  echo "User not found.";
  exit();
}

$user = mysqli_fetch_assoc($userQuery);
$profilePicture = $user['profile_picture'] ?? '';
if ($profilePicture && file_exists("../css/images/" . $profilePicture)) {
    $imagePath = "../css/images/" . $profilePicture;
} else {
    $imagePath = "../css/images/pfp.png";
}

$bio = $user['bio'] ?? 'No bio provided.';
$likesQuery = mysqli_query($conn, "SELECT COUNT(*) as totalLikes FROM likes INNER JOIN posts ON likes.post_id = posts.id WHERE posts.username = '$viewedUserEscaped'");
$likesData = mysqli_fetch_assoc($likesQuery);
$totalLikes = $likesData['totalLikes'] ?? 0;

$postQuery = mysqli_query($conn, "SELECT * FROM posts WHERE username = '$viewedUserEscaped' ORDER BY created_at DESC");

$github = $user['github_link'] ?? '';
$facebook = $user['facebook_link'] ?? '';
$tiktok = $user['tiktok_link'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@<?php echo htmlspecialchars($viewedUser); ?>'s Profile</title>
  <link rel="stylesheet" href="../css/dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="icon" href="../css/images/logo.png" type="image/png">
  <style>
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

    .pfp {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 8px;
      vertical-align: middle;
    }

    .profile-header {
      text-align: center;
      background-color: #111;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 25px;
    }

    .profile-header img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
      border: 2px solid #4caf50;
    }

    .profile-header h2 {
      color: #4caf50;
      margin: 5px 0;
    }

    .profile-header p {
      color: #ccc;
      margin: 5px 0;
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

    #main h3 {
      color: #4caf50;
      margin-bottom: 15px;
    }


    @media screen and (max-width: 559px;) {

      html,
      body {
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
        gap: 20px;
        width: 100%;
        height: 100px;
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
        display: none;
      }

      #dashboard-link i {
        color: #4caf50 !important;
      }

      #main {
        height: calc(85vh - 150px);
        width: 95%;
        overflow-y: auto;
      }

      #main h1 {
        font-size: 20px;
        text-align: center;
        margin-bottom: 15px;
      }

      #createpost {
        display: inline-block;
        width: auto;
        padding: 8px 14px;
        font-size: 14px;
        margin-bottom: 15px;
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
        width: calc(90% - 35px) !important;
        justify-content: center;
        margin: 0 auto;
        align-items: center;
      }

      .comment-form i {
        display: none;
      }

      #credits {
        display: none;
      }
    }

@media screen and (max-width: 940px ) and (min-width: 619px) {
  #credits {
    display: none;
  }

  #sidebar-links i {
    font-size: 40px !important;
    color: #fff;
  }

  #sidebar-links a {
    display: flex;
    align-items: center;
    margin-top: 50px;
  }

  #sidebar-links a i {
    font-size: 30px;   
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


}
  </style>
</head>

<body>
  <div id="container">
    <div id="sidebar">
      <h1>Code Board</h1>
      <ul id="sidebar-links">
        <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="profile.php"><i class="fa-solid fa-user-circle"></i> Profile</a></li>
        <li><a href="myposts.php"><i class="fa-solid fa-file-lines"></i> My Posts</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
        <li><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>

    <div id="main">
      <div class="profile-header">
        <img src="<?php echo $imagePath; ?>" alt="pfp">
        <h2 style="color: #add8e6;">@<?php echo htmlspecialchars($viewedUser); ?></h2>
        <p><?php echo htmlspecialchars($bio); ?></p>
        <p>Likes: <?php echo $totalLikes; ?></p>

        <div style="margin-top: 15px;">
          <?php if ($github): ?>
            <a href="<?php echo htmlspecialchars($github); ?>" target="_blank" style="margin-right: 20px; text-decoration: none"><i class="fa-brands fa-github"></i> GitHub</a>
          <?php endif; ?>
          <?php if ($facebook): ?>
            <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank" style="margin-right: 20px; text-decoration: none"><i class="fa-brands fa-facebook"></i> Facebook</a>
          <?php endif; ?>
          <?php if ($tiktok): ?>
            <a href="<?php echo htmlspecialchars($tiktok); ?>" target="_blank" style="margin-right: 20px; text-decoration: none"><i class="fa-brands fa-tiktok"></i> TikTok</a>
          <?php endif; ?>
        </div>
      </div>


      <h3 style="color: white;">Recent Posts</h3>

      <?php if (mysqli_num_rows($postQuery) == 0): ?>
        <div style="color:#ccc; background:#111; padding:12px; border-radius:6px;">
          No posts yet.
        </div>
      <?php else: ?>
        <?php while ($post = mysqli_fetch_assoc($postQuery)): ?>
          <div class="post">
            <h3 style="font-size: 20px;"><?php echo htmlspecialchars($post['title']); ?></h3>
            <br style="font-size: 14px;"><?php echo htmlspecialchars($post['language']); ?> </br>
            <pre><?php echo htmlspecialchars($post['content']); ?></pre>
            <small style="color:#777;">Posted on: <?php echo $post['created_at']; ?></small>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

    <div id="credits" style="text-align:center;">
      <h1>This website is created <br>by @desciii.</h1>
      <img src="../css/images/download (3).jpg" alt="desciii profile picture" style="width:220px; height:220px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid #4caf50; margin-top:30px;">
      <h2 style="color:#4caf50; margin:0;">@desciii</h2>
      <p style="color:#ccc; font-size:14px; margin:5px 0 10px;">Aspiring web developer & programmer.<br>PHP | CSS | HTML | JS | <br>Soon: Python, React </p>
      <div style="margin-top:8px;">
        <a href="https://github.com/desciii" target="_blank" style="color:#4caf50; text-decoration:none; display:block; margin-bottom:5px;"><i class="fa-brands fa-github"></i> GitHub</a>
        <a href="https://www.tiktok.com/@userw7go3r7op1" target="_blank" style="color:#4caf50; text-decoration:none; display:block; margin-bottom:5px;"><i class="fa-brands fa-tiktok"></i> Tiktok</a>
        <a href="https://www.facebook.com/marlouangelo.panungcat/" target="_blank" style="color:#4caf50; text-decoration:none; display:block;"><i class="fa-brands fa-facebook"></i> Facebook</a>
      </div>
    </div>
  </div>
</body>

</html>