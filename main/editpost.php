<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['username'];
$postId = $_GET['id'] ?? null;

if (!$postId) {
  echo "Post ID is required.";
  exit();
}

// Fetch post
$postQuery = mysqli_query($conn, "SELECT * FROM posts WHERE id = $postId AND username = '$username'");
if (mysqli_num_rows($postQuery) === 0) {
  echo "Post not found or you're not authorized.";
  exit();
}
$post = mysqli_fetch_assoc($postQuery);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $language = mysqli_real_escape_string($conn, $_POST['language']);
  $content = mysqli_real_escape_string($conn, $_POST['content']);

  // Update post
  mysqli_query($conn, "UPDATE posts SET title = '$title', language = '$language', content = '$content' WHERE id = $postId");

  // Optional: Handle tags (clear & re-insert)
  if (isset($_POST['tags'])) {
    $tags = explode(',', $_POST['tags']);
    mysqli_query($conn, "DELETE FROM post_tags WHERE post_id = $postId");

    foreach ($tags as $tag) {
      $tag = trim($tag);
      if ($tag == '') continue;

      // Insert into tags table if not exists
      $tagResult = mysqli_query($conn, "SELECT id FROM tags WHERE name = '$tag'");
      if (mysqli_num_rows($tagResult) == 0) {
        mysqli_query($conn, "INSERT INTO tags (name) VALUES ('$tag')");
        $tagId = mysqli_insert_id($conn);
      } else {
        $tagId = mysqli_fetch_assoc($tagResult)['id'];
      }

      mysqli_query($conn, "INSERT INTO post_tags (post_id, tag_id) VALUES ($postId, $tagId)");
    }
  }

  header("Location: myposts.php");
  exit();
}

// Fetch tags
$tagNames = [];
$tagQuery = mysqli_query($conn, "SELECT t.name FROM tags t 
  INNER JOIN post_tags pt ON pt.tag_id = t.id 
  WHERE pt.post_id = $postId");
while ($tag = mysqli_fetch_assoc($tagQuery)) {
  $tagNames[] = $tag['name'];
}
$tagsString = implode(', ', $tagNames);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Post</title>
  <link rel="stylesheet" href="../css/postcode.css" />
  <link rel="icon" href="../css/images/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<style>
    html, body {
        overflow: hidden;
        height: 100%;
    }

    #content::-webkit-scrollbar {
      width: 8px;
    }

    #content::-webkit-scrollbar-thumb {
      background-color: #4caf50;
      border-radius: 10px;
      border: 2px solid #1a1a1a;
    }

    #content::-webkit-scrollbar-track {
      background-color: #1a1a1a;
    }

    #content {
      flex: 1;
      overflow-y: auto;
      padding-right: 10px;
      scrollbar-width: thin;
      scrollbar-color: #4caf50 #1a1a1a;
    }

</style>
<body>
  <main>
    <a href="myposts.php" class="back-link" style="color: #fff; text-decoration: none;">
    <i class="fa-solid fa-xmark"></i>
    </a>
    <h2>Edit Your Post</h2>
    <form method="POST">
      <label for="title">Title:</label  style="width: 96%; padding: 10px; margin-bottom: 10px;">
      <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

      <label for="language">Language:</label>
      <select name="language" id="language" required  style="padding: 8px; margin-bottom: 10px; width: 100%; font-family: monospace;">
        <option value="HTML" <?php if($post['language'] == 'HTML') echo 'selected'; ?>>HTML</option>
        <option value="CSS" <?php if($post['language'] == 'CSS') echo 'selected'; ?>>CSS</option>
        <option value="JavaScript" <?php if($post['language'] == 'JavaScript') echo 'selected'; ?>>JavaScript</option>
        <option value="PHP" <?php if($post['language'] == 'PHP') echo 'selected'; ?>>PHP</option>
        <option value="Python" <?php if($post['language'] == 'Python') echo 'selected'; ?>>Python</option>
      </select>

      <label for="content">Code:</label>
      <textarea name="content" id="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>

      <label for="tags">Tags (comma separated):</label>
      <input type="text" name="tags" id="tags" value="<?php echo htmlspecialchars($tagsString); ?>">

      <button type="submit">Update Post</button>
    </form>
</main>
</body>
</html>
