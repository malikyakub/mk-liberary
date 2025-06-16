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
$week_ago = date("Y-m-d H:i:s", strtotime("-7 days"));
$stmt = $conn->prepare("SELECT id, original_name, filename, file_type, pages, uploaded_at FROM files WHERE user_id = ? AND uploaded_at >= ? ORDER BY uploaded_at DESC");
$stmt->bind_param("is", $user_id, $week_ago);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard - My Library</title>
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
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        h2 {
            color: #00796b;
        }

        form {
            background: rgba(114, 218, 208, 0.5);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        input[type="file"] {
            flex-grow: 1;
            padding: 0.5rem;
            border-radius: 5px;
            border: none;
            font-size: 1rem;
        }

        button {
            padding: 0.6rem 1.2rem;
            border: none;
            background-color: #4300ff;
            color: white;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #0065f8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #00796b;
            color: white;
            font-weight: 600;
        }

        .download-btn {
            display: inline-block;
            background: #00796b;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .download-btn:hover {
            background: #004d40;
        }

        tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            form {
                flex-direction: column;
            }

            input[type="file"],
            button {
                width: 100%;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
                width: 100%;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 1rem;
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                padding: 1rem;
            }

            td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border: none;
                border-bottom: 1px solid #eee;
            }

            td:last-child {
                border-bottom: none;
            }

            td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #00796b;
                flex-basis: 40%;
            }
        }
    </style>
</head>

<body>
    <?php include "header.php"; ?>

    <main>
        <section>
            <h2>Upload New Book</h2>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="file" required accept=".pdf,.ppt,.pptx,.xls,.xlsx" />
                <button type="submit">Upload</button>
            </form>
        </section>

        <section>
            <h2>Books Uploaded in the Last Week</h2>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Pages</th>
                            <th>Uploaded At</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="Name"><?= htmlspecialchars($row["original_name"]) ?></td>
                                <td data-label="Type"><?= strtoupper($row["file_type"]) ?></td>
                                <td data-label="Pages"><?= (int)$row["pages"] ?></td>
                                <td data-label="Uploaded At"><?= $row["uploaded_at"] ?></td>
                                <td data-label="Download">
                                    <a class="download-btn" href="download.php?file=<?= urlencode($row["filename"]) ?>" download>
                                        📥 Download
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No books uploaded in the last 7 days.</p>
            <?php endif; ?>
        </section>
    </main>
</body>

</html>

<?php $conn->close(); ?>