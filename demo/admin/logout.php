<?php
session_start();

$_SESSION["loggedin"] = false;
session_destroy();
header("Location: /admin");
exit(0);

?>
