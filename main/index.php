<?php
// Redirect to login if not logged in
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
