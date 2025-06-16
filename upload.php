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

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {
    $user_id = $_SESSION["user_id"];
    $file = $_FILES["file"];
    $allowed = ["pdf", "ppt", "xls", "pptx", "xlsx"];

    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        $message = "❌ Only PDF, PPT, and XLS files allowed.";
    } elseif ($file["error"] === 0) {
        $original_name = $file["name"];
        $new_name = uniqid() . '.' . $ext;
        $target_path = "uploads/" . $new_name;

        if (move_uploaded_file($file["tmp_name"], $target_path)) {
            $pages = 0;

            // Count pages if it's a PDF using `pdfinfo`
            if ($ext === "pdf") {
                $escapedPath = escapeshellarg($target_path);
                $output = shell_exec("pdfinfo $escapedPath 2>/dev/null");

                if ($output && preg_match("/Pages:\s+(\d+)/i", $output, $matches)) {
                    $pages = (int)$matches[1];
                }
            }

            $stmt = $conn->prepare("INSERT INTO files (user_id, filename, original_name, file_type, pages) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssi", $user_id, $new_name, $original_name, $ext, $pages);
            $stmt->execute();
            $stmt->close();

            $message = "✅ File uploaded successfully.";
        } else {
            $message = "❌ Failed to upload.";
        }
    } else {
        $message = "❌ Upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Upload File - My Library</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #e0f7fa;
        }

        main {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        h2 {
            color: #00796b;
            margin-bottom: 1rem;
        }

        form {
            background: rgba(114, 218, 208, 0.5);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        input[type="file"] {
            padding: 0.75rem;
            border-radius: 5px;
            border: none;
            font-size: 1rem;
        }

        button {
            padding: 0.75rem;
            border: none;
            background: linear-gradient(90deg, #4300FF, #0065F8);
            color: white;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0065f8;
        }

        .message {
            font-weight: 600;
            color: #333;
            padding: 0.5rem 0;
        }

        a.back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: #00796b;
            text-decoration: none;
            font-weight: 600;
        }

        a.back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            form {
                padding: 1.5rem;
            }

            input[type="file"],
            button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include "header.php"; ?>

    <main>
        <h2>Upload a Book</h2>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required accept=".pdf,.ppt,.pptx,.xls,.xlsx" />
            <button type="submit">Upload</button>
        </form>
        <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
    </main>
</body>

</html>