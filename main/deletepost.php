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

$username = $_SESSION['username'];

if (isset($_POST['delete_post'])) {
  $postId = (int)$_POST['post_id'];

  // Confirm post belongs to the user
  $check = mysqli_query($conn, "SELECT * FROM posts WHERE id = $postId AND username = '$username'");
  if (mysqli_num_rows($check) > 0) {
    // Delete post and related data
    mysqli_query($conn, "DELETE FROM comments WHERE post_id = $postId");
    mysqli_query($conn, "DELETE FROM likes WHERE post_id = $postId");
    mysqli_query($conn, "DELETE FROM post_tags WHERE post_id = $postId");
    mysqli_query($conn, "DELETE FROM posts WHERE id = $postId");
  }
}

header("Location: myposts.php");
exit();
?>
