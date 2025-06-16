<?php
header("Location: login.php");
exit();
?>
<!-- This won't be shown if header works, but stays as fallback -->
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="refresh" content="0;url=login.php">
</head>

<body>If you're not redirected, <a href="login.php">click here</a>.</body>

</html>