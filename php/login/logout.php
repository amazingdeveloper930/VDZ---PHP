<?php
session_start();
session_destroy();
// Redirect to the login page:
header('Location: http://127.0.0.1:8000/');
?>