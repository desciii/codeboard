<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Code Board</title>
    <link rel="stylesheet" href="../css/dashboard.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
    <div id="container">
      <div id="sidebar">
        <h1>Code Board</h1>
        <ul id="sidebar-links">
          <li>
            <a href="dashboard.php" id="dashboard-link"
              ><i class="fa-solid fa-gauge"></i> Dashboard</a
            >
          </li>
          <li>
            <a href="myposts.php"
              ><i class="fa-solid fa-file-lines"></i> My Posts</a
            >
          </li>
          <li>
            <a href="settings.php" style="background-color: #333; color: #fff"
              ><i class="fa-solid fa-gear"></i> Settings</a
            >
          </li>
          <li>
            <a href="login.php"
              ><i class="fa-solid fa-right-from-bracket"></i> Logout</a
            >
          </li>
        </ul>
      </div>

      <div id="main">
        <h1>These are your posts</h1>
      </div>

      <div id="credits">
        <h1>This simple web application was made by desciii</h1>
      </div>
    </div>
  </body>
</html>
