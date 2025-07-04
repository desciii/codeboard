<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
  // Check if cookie exists
  if (isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username']; // Restore session from cookie
  } else {
    header("Location: login.php");
    exit();
  }
}

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


    @media screen and (max-width: 619px) {

      html,
      body {
        height: auto;
        overflow-x: hidden;
      }

      #container {
        display: block;
        height: 95vh;
        overflow: visible;
        padding: 5px;
        margin: 0;
        box-sizing: border-box;
      }

      #sidebar {
        display: flex;
        width: 100%;
        max-width: 100%;
        margin-bottom: 15px;
        height: auto;
        border-radius: 10px;
        padding: 8px 5px;
        box-sizing: border-box;
        overflow: hidden;
      }

      #sidebar-links {
        display: flex;
        flex-direction: row;
        align-items: center;
        width: 100%;
        margin: 0;
        list-style: none;
        box-sizing: border-box;
      }

      #sidebar-links li {
        list-style: none;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
      }

      #sidebar-links a {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 60px;
        height: 60px;
        background-color: #333;
        border-radius: 15%;
        color: transparent;
        font-size: 0;
        box-sizing: border-box;
      }

      #sidebar-links i {
        font-size: 26px;
        color: #fff;
      }

      #sidebar h1 {
        display: none;
      }

      #dashboard-link i {
        color: #4caf50 !important;
      }

      #main {
        height: calc(95vh - 120px);
        width: 98%;
        max-width: 98%;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0 5px;
        box-sizing: border-box;
      }

      #main h1 {
        font-size: 18px;
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

    @media screen and (max-width: 412px) {
      #container {
        padding: 2px;
      }

      #sidebar {
        padding: 5px 2px;
      }

      #sidebar-links a {
        width: 45px;
        height: 45px;
      }

      #sidebar-links i {
        font-size: 24px;
      }

      #createpost {
        width: 100px;
        font-size: 12px;
      }
    }


    @media screen and (max-width: 1025px) and (min-width: 619px) {
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
        margin-top: 30px;
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

    #sidebar {
      overflow-y: auto;
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
        <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>
    <!-- End of sidebar -->

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
            <?php
            $codeLines = explode("\n", $post['content']);
            $isLong = count($codeLines) > 30;
            $preview = implode("\n", array_slice($codeLines, 0, 30));
            $full = $post['content'];
            $postUniqueId = 'post-' . $post['id'];
            ?>

            <pre><code id="<?php echo $postUniqueId; ?>"><?php echo htmlspecialchars($isLong ? $preview : $full); ?></code></pre>

            <?php
            $postId = $post['id'];
            $tagsQuery = mysqli_query($conn, "SELECT t.name FROM tags t
                                              INNER JOIN post_tags pt ON t.id = pt.tag_id
                                              WHERE pt.post_id = $postId");

            $tags = [];
            while ($tagRow = mysqli_fetch_assoc($tagsQuery)) {
              $tags[] = '#' . htmlspecialchars($tagRow['name']);
            }
            if (!empty($tags)) {
              echo '<div style="color:#4caf50; font-size:16px; margin-bottom:5px; text-decoration:underline;">' . implode(' ', $tags) . '</div>';
            }
            ?>

            <?php if ($isLong): ?>
              <button
                onclick="toggleCode('<?php echo $postUniqueId; ?>', this)"
                data-full="<?php echo htmlspecialchars(json_encode($full)); ?>"
                data-preview="<?php echo htmlspecialchars(json_encode($preview)); ?>"
                data-state="collapsed"
                style="background:none; border:none; color:#4caf50; cursor:pointer; margin-top:5px;">
                See More
              </button>
            <?php endif; ?>

            <small style="color:#777;">Posted on: <?php echo $post['created_at']; ?></small>
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #333;">
              <strong style="color:#4caf50; font-size:14px;">Comments:</strong>
              <?php
              $commentsQuery = mysqli_query($conn, "SELECT * FROM comments WHERE post_id = $postId ORDER BY created_at ASC");
              if (mysqli_num_rows($commentsQuery) == 0) {
                echo "<p style='color:#888; font-size:13px;'>No comments yet.</p>";
              } else {
                while ($comment = mysqli_fetch_assoc($commentsQuery)) {
                  
                  echo "<div style='margin-top:5px; background:#111; padding:6px 10px; border-radius:6px;'>";
                  echo "<strong style='color:#4caf50; font-size:13px;'>@" . htmlspecialchars($comment['username']) . "</strong>";
                  echo "<p style='color:#ccc; font-size:13px; margin: 2px 0;'>" . htmlspecialchars($comment['content']) . "</p>";
                  echo "<small style='color:#666; font-size:11px;'>" . $comment['created_at'] . "</small>";
                  echo "</div>";
                }
              }
              ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
    <!-- End of main content -->

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
    <!-- End of credits -->

  </div>
  <!-- End of container -->

  <script>
      function toggleCode(codeId, btn) {
        const codeEl = document.getElementById(codeId);
        const full = JSON.parse(btn.dataset.full);
        const preview = JSON.parse(btn.dataset.preview);

        if (btn.dataset.state === "collapsed") {
          codeEl.textContent = full;
          btn.dataset.state = "expanded";
          btn.innerText = "See Less";
        } else {
          codeEl.textContent = preview;
          btn.dataset.state = "collapsed";
          btn.innerText = "See More";
        }
      }
    </script>
    <!-- End of see more -->

</body>

</html>