<?php
session_start();
include('db.php');

$username = $_SESSION['username'] ?? null;
if (!$username) {
    header("Location: login.php");
    exit();
}

$profilePicture = null;
$imagePath = "../css/images/pfp.png";
$bio = "";
$postCount = 0;
$likeCount = 0;

$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' LIMIT 1");

if ($userQuery && mysqli_num_rows($userQuery) > 0) {
    $user = mysqli_fetch_assoc($userQuery);
    $profilePicture = $user['profile_picture'] ?? null;
    $bio = $user['bio'] ?? "";

    $github = $user['github_link'] ?? '';
    $facebook = $user['facebook_link'] ?? '';
    $tiktok = $user['tiktok_link'] ?? '';

    if ($profilePicture && file_exists("../css/images/" . $profilePicture)) {
        $imagePath = "../css/images/" . $profilePicture;
    }
}

$postQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_posts FROM posts WHERE username = '$username'");
if ($postQuery) {
    $postCount = mysqli_fetch_assoc($postQuery)['total_posts'] ?? 0;
}

$postQuery = mysqli_query($conn, "SELECT id FROM posts WHERE username = '$username'");
$postIds = [];
while ($row = mysqli_fetch_assoc($postQuery)) {
    $postIds[] = $row['id'];
}

$likeCount = 0;

if (count($postIds) > 0) {
    $postIdsStr = implode(',', $postIds);
    $likeQuery = mysqli_query($conn, "SELECT COUNT(*) as totalLikes FROM likes WHERE post_id IN ($postIdsStr)");
    $likeData = mysqli_fetch_assoc($likeQuery);
    $likeCount = $likeData['totalLikes'];
}

if (isset($_POST['update_bio'])) {
    $newBio = mysqli_real_escape_string($conn, $_POST['bio']);
    mysqli_query($conn, "UPDATE users SET bio = '$newBio' WHERE username = '$username'");
    header("Location: profile.php?success=1");
    exit();
}

if (isset($_POST['update_links'])) {
    $github = mysqli_real_escape_string($conn, $_POST['github']);
    $facebook = mysqli_real_escape_string($conn, $_POST['facebook']);
    $tiktok = mysqli_real_escape_string($conn, $_POST['tiktok']);

    mysqli_query($conn, "UPDATE users SET github_link = '$github', facebook_link = '$facebook', tiktok_link = '$tiktok' WHERE username = '$username'");
    header("Location: profile.php?success=links");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
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

    .profile-container {
    max-width: 500px;
    margin: 40px auto;
    background: #111;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #333;
    color: #ccc;
    text-align: center;
    }

    .profile-container img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #4caf50;
    margin-bottom: 10px;
    }

    .profile-container h2 {
    color: #4caf50;
    margin-bottom: 15px;
    }

    .profile-container p {
    font-size: 15px;
    margin: 6px 0;
    }

    .profile-container textarea {
    width: 100%;
    background: #1a1a1a;
    border: 1px solid #333;
    color: #ccc;
    border-radius: 8px;
    resize: vertical;
    margin-top: 10px;
    }

    .profile-container button {
    margin-top: 10px;
    background-color: #4caf50;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    }

    .profile-container button:hover {
    background-color: #43a047;
    }

    .profile-container a {
    color: #4caf50;
    text-decoration: none;
    display: inline-block;
    margin-top: 8px;
    }

    .profile-container a i {
    margin-right: 5px;
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
    justify-content: space-evenly; 
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
    color: #fff !important; 
    }

    #sidebar h1 {
    display: none;
    }

    #dashboard-link i {
    color: #4caf50 !important;
    }

    #main {
    height: calc(95vh - 120px);
    width: 95%;
    overflow-y: auto;
    padding-top: 0px;
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

    @media screen and(min-width: 619px) {
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
    margin-top: 50px;
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
    margin-top: 50px;
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

    </style>
</head>

<body>

    <div id="container">
        <div id="sidebar">
            <h1>Code Board</h1>
            <ul id="sidebar-links">
                <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                <li><a href="profile.php" id="dashboard-link" style="background-color: #333; color: #fff"><i class="fa-solid fa-user-circle"></i> Profile</a></li>
                <li><a href="myposts.php"><i class="fa-solid fa-file-lines"></i> My Posts</a></li>
                <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                <li><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </div> <!-- End of sidebar -->

        <div id="main">
            <h1>Profile</h1>
            <div class="profile-container">
                
                <img src="<?php echo $imagePath; ?>" alt="Profile Picture">
                <h2 style="color: #add8e6;">@<?php echo htmlspecialchars($username); ?></h2>

                <p><strong>Posts:</strong> <?php echo $postCount; ?></p>
                <p><strong>Likes:</strong> <?php echo $likeCount; ?></p>

                <?php if ($bio): ?>
                    <p style="margin-top:15px;"><?php echo nl2br(htmlspecialchars($bio)); ?></p>
                <?php endif; ?>

                <div style="margin-top: 15px;">
                    <?php if ($github): ?>
                        <a href="<?php echo htmlspecialchars($github); ?>" target="_blank" style="margin-right: 20px"><i class="fa-brands fa-github"></i> GitHub</a>
                    <?php endif; ?>
                    <?php if ($facebook): ?>
                        <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank" style="margin-right: 20px"><i class="fa-brands fa-facebook"></i> Facebook</a>
                    <?php endif; ?>
                    <?php if ($tiktok): ?>
                        <a href="<?php echo htmlspecialchars($tiktok); ?>" target="_blank" style="margin-right: 20px"><i class="fa-brands fa-tiktok"></i> TikTok</a>
                    <?php endif; ?>
                </div>
            </div> <!-- End of profile-container -->
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
    </div> <!-- End of container -->
</body>

</html>