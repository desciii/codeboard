<?php
session_start();
include('db.php');

$username = $_SESSION['username'] ?? null;

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
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Code Board</title>
  <link rel="stylesheet" href="../css/dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    #main { overflow-x: hidden; overflow-y: auto; height: 100vh; padding-right: 10px; }
    .post { word-wrap: break-word; overflow-wrap: break-word; background:#1a1a1a; padding:15px; margin-bottom:15px; border-radius:8px; }
    .post pre { white-space: pre-wrap; word-break: break-word; overflow-x: auto; background:#0d0d0d; padding:10px; border-radius:5px; color:#ccc; }
    .like-form { margin-top: 8px; }
    .like-btn { background: none; border: none; color: #ccc; cursor: pointer; font-size: 14px; }
    .like-btn:hover { color: #ff4d4d; }
  </style>
</head>
<body>
  <div id="container">
    <div id="sidebar">
      <h1>Code Board</h1>
      <ul id="sidebar-links">
        <li><a href="dashboard.php" id="dashboard-link" style="background-color: #333; color: #fff"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="myposts.php"><i class="fa-solid fa-file-lines"></i> My Posts</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
        <li><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
      </ul>
    </div>

    <div id="main">
      <h1>Welcome to Code Board</h1>
      <a href="postcode.php" id="createpost" style="width: 115px;"><i class="fa-solid fa-pen-to-square"></i> Post a Code</a>

      <div id="posts-container" style="margin-top: 20px;">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <div class="post">
            <h3 style="color:#ADD8E6; font-size: 18px">@<?php echo htmlspecialchars($row['username']);?></h3>
            <h3 style="font-size: 14px;">Title: <?php echo htmlspecialchars($row['title']);?><br> Language: <?php echo htmlspecialchars($row['language']);?> </h3>

            <form method="post" class="like-form">
              <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
              <button type="submit" name="like" class="like-btn"><i class="fa-solid fa-heart"></i> <?php echo $row['like_count']; ?> Likes</button>
            </form>
            
            <pre><?php echo htmlspecialchars($row['content']); ?></pre>
            <small style="color:#777;">Posted on: <?php echo $row['created_at']; ?></small>

            
          </div>
        <?php endwhile; ?>
      </div>
    </div>

    <div id="credits"><h1>This web application was made by desciii</h1></div>
  </div>
</body>
</html>

