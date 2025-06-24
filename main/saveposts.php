<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_SESSION['username'];
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $content = mysqli_real_escape_string($conn, $_POST['content']);
  $language = mysqli_real_escape_string($conn, $_POST['language']);


  mysqli_query($conn, "INSERT INTO posts (username, title, content, language, created_at) VALUES ('$username', '$title', '$content', '$language', NOW())");

  $postId = mysqli_insert_id($conn); // ✅ postId we need for post_tags


  if (isset($_POST['tags']) && !empty($_POST['tags'])) {
    $tagsInput = mysqli_real_escape_string($conn, $_POST['tags']);
    $tagsArray = array_map('trim', explode(',', $tagsInput));

    foreach ($tagsArray as $tagName) {
      if (empty($tagName)) continue;
      

      mysqli_query($conn, "INSERT IGNORE INTO tags (name) VALUES ('$tagName')");
      
      $tagResult = mysqli_query($conn, "SELECT id FROM tags WHERE name = '$tagName' LIMIT 1");
      $tagRow = mysqli_fetch_assoc($tagResult);
      $tagId = $tagRow['id'];

      mysqli_query($conn, "INSERT INTO post_tags (post_id, tag_id) VALUES ($postId, $tagId)");
    }
  }

  header("Location: dashboard.php");
  exit();
}
?>
