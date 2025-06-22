<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $language = mysqli_real_escape_string($conn, $_POST['language']);

    $query = "INSERT INTO posts (username, title, content, language, created_at) VALUES ('$username', '$title', '$content', '$language', NOW())";

    if (mysqli_query($conn, $query)) {
        header('Location: dashboard.php');
        exit();
    } else {
        echo "Error posting: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
