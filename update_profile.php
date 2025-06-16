<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $errors = [];

    // Validate name and email
    if (empty($name) || empty($email)) {
        $errors[] = "Name and email are required.";
    }

    // Password change check
    if (!empty($password)) {
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }
    }

    // Handle profile image upload
    $profile_image = null;
    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) {
        $allowed_types = ["image/jpeg", "image/png"];
        if (!in_array($_FILES["profile_image"]["type"], $allowed_types)) {
            $errors[] = "Only JPG and PNG images are allowed.";
        } else {
            $ext = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
            $new_name = "user_" . $user_id . "." . $ext;
            $upload_dir = "uploads/profile_images/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $upload_path = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $upload_path)) {
                $profile_image = $upload_path;
            } else {
                $errors[] = "Failed to upload profile image.";
            }
        }
    }

    // Update database if no errors
    if (empty($errors)) {
        $sql = "UPDATE users SET name = ?, email = ?" .
            (!empty($password) ? ", password = ?" : "") .
            ($profile_image ? ", profile_image = ?" : "") .
            " WHERE id = ?";

        $stmt = $conn->prepare($sql);

        // Bind parameters dynamically
        if (!empty($password) && $profile_image) {
            $stmt->bind_param("ssssi", $name, $email, $hashed_password, $profile_image, $user_id);
        } elseif (!empty($password)) {
            $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
        } elseif ($profile_image) {
            $stmt->bind_param("sssi", $name, $email, $profile_image, $user_id);
        } else {
            $stmt->bind_param("ssi", $name, $email, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION["user_name"] = $name;
            header("Location: profile.php?success=1");
            exit();
        } else {
            $errors[] = "Database update failed.";
        }
    }

    $_SESSION["update_errors"] = $errors;
    header("Location: profile.php");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
