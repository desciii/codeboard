<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Post a Code</title>
  <link rel="stylesheet" href="../css/postcode.css" />
  <link rel="icon" href="../css/images/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body>
  <main>
    <a href="dashboard.php" style="color: white; text-decoration: none;">
        <i class="fas fa-times" style="font-size: 20px; cursor: pointer;"></i>
    </a>
    <h1>Share Your Code</h1>
    <form action="saveposts.php" method="post">
      <input type="text" name="title" placeholder="Title of your code" required style="width: 96%; padding: 10px; margin-bottom: 10px;" />

      <textarea name="content" placeholder="Paste your code here..." rows="10" required style="width: 96%; padding: 10px; margin-bottom: 10px; font-family: monospace;"></textarea>

      <input type="text" name="tags" placeholder="Add tags separated by commas, e.g., php, html" style="width: 96%; padding: 10px; margin-bottom: 10px;"/>

      <select name="language" required style="padding: 8px; margin-bottom: 10px; width: 100%; font-family: monospace;">
        <option value="" disabled selected>Select Language</option>
        <option value="HTML">HTML</option>
        <option value="CSS">CSS</option>
        <option value="JavaScript">JavaScript</option>
        <option value="PHP">PHP</option>
        <option value="Python">Python</option>
      </select>

      <button type="submit" style="width: 100%;">Post</button>
    </form>
  </main>
</body>
</html>
