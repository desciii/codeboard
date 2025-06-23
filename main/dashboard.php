<?php
session_start();
include('db.php');

$username = $_SESSION['username'] ?? null;

// ✅ Fetch logged in user info for *their* profile picture
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' LIMIT 1");
$user = mysqli_fetch_assoc($userQuery);

$profilePicture = $user['profile_picture'] ?? null;

if ($profilePicture && file_exists("../css/images/" . $profilePicture)) {
  $imagePath = "../css/images/" . $profilePicture;
} else {
  $imagePath = "../css/images/pfp.png";
}


if (isset($_POST['like'])) {
  $postId = (int)$_POST['post_id'];
  $check = mysqli_query($conn, "SELECT * FROM likes WHERE post_id = $postId AND username = '$username'");

  if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO likes (post_id, username) VALUES ($postId, '$username')");
  } else {
    mysqli_query($conn, "DELETE FROM likes WHERE post_id = $postId AND username = '$username'");
  }
  header("Location: dashboard.php");
  exit();
}


$query = "SELECT posts.*, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) as like_count
          FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);


if (isset($_POST['comment']) && !empty($_POST['comment_text'])) {
  $postId = (int)$_POST['post_id'];
  $commentText = mysqli_real_escape_string($conn, $_POST['comment_text']);
  mysqli_query($conn, "INSERT INTO comments (post_id, username, content) VALUES ($postId, '$username', '$commentText')");
  header("Location: dashboard.php");
  exit();
}

$filter = isset($_GET['language']) ? mysqli_real_escape_string($conn, $_GET['language']) : '';

if ($filter !== '') {
  $query = "SELECT posts.*, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) as like_count
            FROM posts
            WHERE language = '$filter'
            ORDER BY created_at DESC";
} else {
  $query = "SELECT posts.*, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) as like_count
            FROM posts
            ORDER BY created_at DESC";
}

$result = mysqli_query($conn, $query);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
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

@media screen and (max-width: 599px) {
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
    height: calc(85vh - 150px); /* adjust if needed for your sidebar height */
    width: 95%;
    overflow-y: auto; /* THIS makes #main scrollable */
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
    width: calc(90% - 35px)!important;
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

@media screen and (max-width: 1061px) {
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

}

  </style>
</head>

<body>

  <div id="container">
    <div id="sidebar">
      <h1>Code Board</h1>
      <ul id="sidebar-links">
        <li><a href="dashboard.php" id="dashboard-link" style="background-color: #333; color: #fff"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="profile.php"><i class="fa-solid fa-user-circle"></i> Profile</a></li>
        <li><a href="myposts.php"><i class="fa-solid fa-file-lines"></i> My Posts</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
        <li><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div> <!-- End of sidebar -->

    <div id="main">
      <h1>Dashboard - Welcome @<?php echo htmlspecialchars($username); ?></h1>
      <a href="postcode.php" id="createpost" style="width: 115px;"><i class="fa-solid fa-pen-to-square"></i> Post a Code
      </a>
      <br>
      <form method="get" style="margin-bottom: 20px;">
        <select name="language" id="language" style="padding:5px; border-radius:5px; background:#1a1a1a; color:#fff; border:1px solid #333;">
          <option value="">All Languages</option>
          <option value="HTML">HTML</option>
          <option value="CSS">CSS</option>
          <option value="JavaScript">JavaScript</option>
          <option value="PHP">PHP</option>
          <option value="Python">Python</option>
        </select>
        <button type="submit" style="padding:5px 10px;  background-color:#4caf50; color:#fff; border:none; border-radius:5px; cursor:pointer;">Filter</button>
      </form>
      <div id="posts-container" style="margin-top: 20px;">
        <?php if (mysqli_num_rows($result) == 0): ?>
          <div style="color:#ccc; background:#111; padding:12px; border-radius:6px;">
            <?php if ($filter !== ''): ?>
              No posts found for "<strong><?php echo htmlspecialchars($filter); ?></strong>"
            <?php else: ?>
              No posts yet.
            <?php endif; ?>
          </div>
        <?php else: ?>
          <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <?php
            $postUser = mysqli_real_escape_string($conn, $row['username']);
            $authorQuery = mysqli_query($conn, "SELECT profile_picture FROM users WHERE username = '$postUser' LIMIT 1");
            $author = mysqli_fetch_assoc($authorQuery);
            $authorPfp = $author['profile_picture'] ?? null;

            if ($authorPfp && file_exists("../css/images/" . $authorPfp)) {
              $authorPfpPath = "../css/images/" . $authorPfp;
            } else {
              $authorPfpPath = "../css/images/pfp.png";
            }
            ?>
            <div class="post">
              <h3 style="color:#ADD8E6; font-size: 18px;">
                <a href="userprofile.php?username=<?php echo urlencode($row['username']); ?>" style="color:inherit; text-decoration:none;">
                  <img src="<?php echo $authorPfpPath; ?>" class="pfp" alt="pfp">
                  @<?php echo htmlspecialchars($row['username']); ?>
                </a>
              </h3>

              <h3 style="font-size: 20px; color:#4caf50"><?php echo htmlspecialchars($row['title']); ?></h3>
              <br style="font-size: 14px;"><?php echo htmlspecialchars($row['language']); ?></br>

              <form method="post" class="like-form">
                <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="like" class="like-btn"><i class="fa-solid fa-heart"></i> <?php echo $row['like_count']; ?> Likes</button>
              </form>

              <pre><?php echo htmlspecialchars($row['content']); ?></pre>

              <div class="comments" style="margin-top:10px;">
                <?php
                $postId = $row['id'];
                $commentsQuery = mysqli_query($conn, "SELECT * FROM comments WHERE post_id = $postId ORDER BY created_at ASC");
                while ($comment = mysqli_fetch_assoc($commentsQuery)) :
                ?>
                  <div class="comments" style="margin-top:10px;">
                    <?php
                    $postId = $row['id'];
                    $commentsQuery = mysqli_query($conn, "SELECT * FROM comments WHERE post_id = $postId ORDER BY created_at ASC");
                    while ($comment = mysqli_fetch_assoc($commentsQuery)) :
                      $commentUser = mysqli_real_escape_string($conn, $comment['username']);
                      $commentUserQuery = mysqli_query($conn, "SELECT profile_picture FROM users WHERE username = '$commentUser' LIMIT 1");
                      $commentUserData = mysqli_fetch_assoc($commentUserQuery);
                      
                      $commentPfp = "../css/images/pfp.png"; // Default first
                      if ($commentUserData && !empty($commentUserData['profile_picture']) && file_exists("../css/images/" . $commentUserData['profile_picture'])) {
                        $commentPfp = "../css/images/" . $commentUserData['profile_picture'];
                      }
                    ?>
                      <div style="margin-top:5px; padding:8px; background:#111; border-radius:5px; color:#ccc; font-size:13px; display:flex; align-items:center;">
                        <img src="<?php echo $commentPfp; ?>" alt="pfp" style="width:25px; height:25px; border-radius:50%; object-fit:cover; margin-right:8px;">
                        <a href="userprofile.php?username=<?php echo urlencode($comment['username']); ?>" style="color:#4caf50; text-decoration:none; font-weight:bold;">
                          @<?php echo htmlspecialchars($comment['username']); ?>:
                        </a>
                        <span style="margin-left:5px;"><?php echo htmlspecialchars($comment['content']); ?></span>
                      </div>
                    <?php endwhile; ?>
                  </div>
                <?php endwhile; ?>
              </div>
              <form method="post" class="comment-form" style="margin-top:8px;">
                <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                <input type="text" name="comment_text" placeholder="Write a comment..." required
                  style="width:92%; padding:6px; border-radius:5px; border:1px solid #333; background:#1a1a1a; color:#fff;">
                <button type="submit" name="comment" style="background:none; border:none; color:#4caf50; cursor:pointer; margin-left:5px;">
                  <i class="fa-solid fa-paper-plane"></i>
                </button>
              </form>

              <small style="color:#777;">Posted on: <?php echo $row['created_at']; ?></small>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </div> <!-- End of main -->

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
    </div> <!-- End of credits -->
  </div>
</body>

</html>