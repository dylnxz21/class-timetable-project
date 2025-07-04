<?php
session_start();
session_destroy(); // Destroys the session, logs user out
header("Location: login.html"); // Redirect to login page
exit();
?> 
