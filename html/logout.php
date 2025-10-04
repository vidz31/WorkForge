<?php
require_once '../includes/helpers.php';

// Logout user
Auth::logout();

// Redirect to home page
Response::redirect('index.php');
?>