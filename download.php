<?php
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_GET["file"])) {
    header("Location: login.html");
    exit();
}

$filename = basename($_GET["file"]);
$filepath = __DIR__ . "/uploads/" . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    echo "File not found.";
    exit();
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
header("Content-Length: " . filesize($filepath));
flush();
readfile($filepath);
exit();
