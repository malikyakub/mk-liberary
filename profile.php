<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}
$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
$user_id = $_SESSION["user_id"];

// Fetch user data
$stmt = $conn->prepare("SELECT name, email, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_image);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Profile Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            margin: 0;
        }

        header {
            background: #00796b;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a {
            color: white;
            text-decoration: none;
            margin-left: 1rem;
        }

        main {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #00796b;
            margin-bottom: 1rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 0.5rem 0 0.2rem;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            padding: 0.6rem;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 1.5rem;
            padding: 0.75rem;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
        }

        img.profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 2px;
            object-fit: cover;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <?php include "header.php"; ?>


    <main>
        <h2>👤 Update Your Profile</h2>

        <?php if ($profile_image): ?>
            <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Picture" class="profile-pic" />

        <?php endif; ?>

        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <label for="profile_image">Profile Picture</label>
            <input type="file" name="profile_image" accept="image/*" />

            <label for="name">Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required />

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required />

            <label for="password">New Password <small>(leave blank to keep current)</small></label>
            <input type="password" name="password" placeholder="••••••••" />

            <button type="submit">Save Changes</button>
        </form>
    </main>
</body>

</html>