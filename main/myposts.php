<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Check if cookie exists
    if (isset($_COOKIE['username'])) {
        $_SESSION['username'] = $_COOKIE['username']; // Restore session from cookie
    } else {
        header("Location: login.php");
        exit();
    }
}

$username = $_SESSION['username'];

// Handle like toggle
if (isset($_POST['like'])) {
  $postId = (int)$_POST['post_id'];
  $check = mysqli_query($conn, "SELECT * FROM likes WHERE post_id = $postId AND username = '$username'");

  if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO likes (post_id, username) VALUES ($postId, '$username')");
  } else {
    mysqli_query($conn, "DELETE FROM likes WHERE post_id = $postId AND username = '$username'");
  }
  header("Location: myposts.php");
  exit();
}

// Handle comment submit
if (isset($_POST['comment']) && !empty($_POST['comment_text'])) {
  $postId = (int)$_POST['post_id'];
  $commentText = mysqli_real_escape_string($conn, $_POST['comment_text']);
  mysqli_query($conn, "INSERT INTO comments (post_id, username, content) VALUES ($postId, '$username', '$commentText')");
  header("Location: myposts.php");
  exit();
}

// Query posts with like count
$query = "SELECT posts.*, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) as like_count
          FROM posts 
          WHERE posts.username = ? 
          ORDER BY posts.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' LIMIT 1");
$user = mysqli_fetch_assoc($userQuery);

$profilePicture = $user['profile_picture'] ?? null;

if ($profilePicture && file_exists("../css/images/" . $profilePicture)) {
    $imagePath = "../css/images/" . $profilePicture;
} else {
    $imagePath = "../css/images/pfp.png";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Posts</title>
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
    background: #1a1a1a;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    overflow-wrap: break-word;
    word-break: break-word;
    }

    .post pre {
    background: #0d0d0d;
    padding: 10px;
    border-radius: 5px;
    color: #ccc;
    white-space: pre-wrap;
    word-break: break-word;
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

    .comment {
    margin-top: 5px;
    padding: 8px;
    background: #111;
    border-radius: 5px;
    color: #ccc;
    font-size: 13px;
    }

    .comment strong {
    color: #4caf50;
    }

    .comment-form {
    margin-top: 8px;
    }

    .comment-form input[type="text"] {
    width: 92%;
    padding: 6px;
    border-radius: 5px;
    border: 1px solid #333;
    background: #1a1a1a;
    color: #fff;
    }

    .comment-form button {
    background: none;
    border: none;
    color: #4caf50;
    cursor: pointer;
    margin-left: 5px;
    }

    .comment-form button:hover {
    color: #63e276;
    }

    .pfp {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 8px;
    vertical-align: middle;
    }

    @media screen and (max-width: 360px) {
    #container {
    padding: 2px;
    }

    #sidebar {
    padding: 5px 2px;
    }

    #sidebar-links {
    padding: 0 5px; 
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

    #yo {
    margin-bottom: 5px;
    width: 20%;
    }

    }

    @media screen and (max-width: 1025px ) and (min-width: 619px) {
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

    #sidebar {
    overflow-y: auto;
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

</style>
</head>

<body>
  <div id="container">
    <div id="sidebar">
      <h1>Code Board</h1>
      <ul id="sidebar-links">
        <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="profile.php"><i class="fa-solid fa-user-circle"></i> Profile</a></li>
        <li><a href="myposts.php" id="dashboard-link" style="background-color:#333; color:#fff;"><i class="fa-solid fa-file-lines"></i> My Posts</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
        <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div> <!-- End of sidebar -->

    <div id="main">
      <h1>Your Posts</h1>

      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <div class="post">
            <h3 style="color:#ADD8E6; font-size: 18px;">
              <img src="<?php echo $imagePath; ?>" class="pfp" alt="pfp">@<?php echo htmlspecialchars($row['username']);?>
            </h3>
            <h3 style="font-size: 20px; color:#4caf50;"><?php echo htmlspecialchars($row['title']); ?></h3>
            <form method="get" action="editpost.php" style="display:inline;">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <button type="submit" id="yo" style="background:#4caf50; color:#fff; padding:0px 12px; border:none; border-radius:4px; cursor:pointer; margin-top:5px; height: 40px;">
                <i class="fa-solid fa-pen-to-square"></i> Edit
              </button>
            </form>
            <form method="post" action="deletepost.php" onsubmit="return confirm('Are you sure you want to delete this post?');" style="display:inline;">
              <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
              <button type="submit" name="delete_post" id="yo" style="background:#e53935; color:#fff; padding:-3px 12px; border:none; border-radius:4px; cursor:pointer; height: 40px;">
                <i class="fa-solid fa-trash"></i> Delete
              </button>
            </form>
            <form method="post" class="like-form">
              <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
              <button type="submit" name="like" class="like-btn"><i class="fa-solid fa-heart"></i> <?php echo $row['like_count']; ?> Likes</button>
            </form>
            
            <?php
            $codeLines = explode("\n", $row['content']);
            $isLong = count($codeLines) > 30;
            $preview = implode("\n", array_slice($codeLines, 0, 30));
            $full = $row['content'];
            $postUniqueId = 'post-' . $row['id'];
            ?>

            <pre><code id="<?php echo $postUniqueId; ?>"><?php echo htmlspecialchars($isLong ? $preview : $full); ?></code></pre>
            
            <?php
            $postId = $row['id'];
            $tagsQuery = mysqli_query($conn, "SELECT t.name FROM tags t 
                                              INNER JOIN post_tags pt ON t.id = pt.tag_id
                                              WHERE pt.post_id = $postId");

            $tags = [];
            while ($tagRow = mysqli_fetch_assoc($tagsQuery)) {
              $tags[] = '#' . htmlspecialchars($tagRow['name']);
            }
            if (!empty($tags)) {
              echo '<div style="color:#4caf50;; font-size:13px; margin-bottom:5px;">' . implode(' ', $tags) . '</div>';
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

            <div class="comments">
              <?php
              $postId = $row['id'];
              $commentsQuery = mysqli_query($conn, "SELECT * FROM comments WHERE post_id = $postId ORDER BY created_at ASC");
              while ($comment = mysqli_fetch_assoc($commentsQuery)) :
              ?>

                  <div class="comment">
                  <strong>@<?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['content']); ?>
                </div>
              <?php endwhile; ?>
            </div>

            <form method="post" class="comment-form">
              <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
              <input type="text" name="comment_text" placeholder="Write a comment..." required>
              <button type="submit" name="comment"><i class="fa-solid fa-paper-plane"></i></button>
            </form>

            <small style="color:#777;">Posted on: <?php echo $row['created_at']; ?></small>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No posts yet. <a href="postcode.php" style="color:#4caf50;">Post one now!</a></p>
      <?php endif; ?>
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
    </div>
  </div> <!-- End of container -->

  <!--See more code button-->
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
</body>

</html>