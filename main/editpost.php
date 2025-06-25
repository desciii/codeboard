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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Post</title>
  <link rel="stylesheet" href="../css/postcode.css" />
  <link rel="icon" href="../css/images/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <style>
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
</head>
<body>
  <main>
    <a href="myposts.php" style="color: white; text-decoration: none;">
        <i class="fas fa-times" style="font-size: 20px; cursor: pointer;"></i>
    </a>
    <h1>Edit Your Post</h1>
    <form method="POST">
      <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" required style="width: 96%; padding: 10px; margin-bottom: 10px;" />

      <textarea name="content" id="content" rows="10" required style="width: 96%; padding: 10px; margin-bottom: 10px; font-family: monospace;"><?php echo htmlspecialchars($post['content']); ?></textarea>

      <input type="text" name="tags" id="tags" value="<?php echo htmlspecialchars($tagsString); ?>" placeholder="Add tags separated by commas, e.g., php, html" style="width: 96%; padding: 10px; margin-bottom: 10px;"/>

      <select name="language" id="language" required style="padding: 8px; margin-bottom: 10px; width: 100%; font-family: monospace;">
        <option value="PHP" <?php if($post['language'] == 'PHP') echo 'selected'; ?>>PHP</option>
        <option value="CSS" <?php if($post['language'] == 'CSS') echo 'selected'; ?>>CSS</option>
        <option value="HTML" <?php if($post['language'] == 'HTML') echo 'selected'; ?>>HTML</option>
        <option value="JavaScript" <?php if($post['language'] == 'JavaScript') echo 'selected'; ?>>JavaScript</option>
        <option value="SQL" <?php if($post['language'] == 'SQL') echo 'selected'; ?>>SQL</option>
        <option value="Java" <?php if($post['language'] == 'Java') echo 'selected'; ?>>Java</option>
        <option value="C" <?php if($post['language'] == 'C') echo 'selected'; ?>>C</option>
        <option value="C++" <?php if($post['language'] == 'C++') echo 'selected'; ?>>C++</option>
      </select>

      <button type="submit" style="width: 100%;">Update Post</button>
    </form>
  </main>
</body>
</html>