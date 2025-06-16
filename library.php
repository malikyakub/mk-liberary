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
$stmt = $conn->prepare("SELECT filename, original_name, file_type, pages, uploaded_at FROM files WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Your Library - My Library</title>
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
      margin-bottom: 1rem;
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
      vertical-align: middle;
    }

    th {
      background-color: #00796b;
      color: white;
      font-weight: 600;
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    a.button {
      text-decoration: none;
      background: #4300ff;
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: 600;
      transition: background 0.3s ease;
      display: inline-block;
    }

    a.button:hover {
      background: #0065f8;
    }

    .links {
      margin-top: 1.5rem;
    }

    .links a {
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 6px;
      font-weight: 600;
      margin-right: 10px;
      color: white;
      background: #4300ff;
      transition: background 0.3s ease;
      display: inline-block;
    }

    .links a.dashboard {
      background: #28a745;
    }

    .links a:hover {
      opacity: 0.9;
    }

    @media (max-width: 768px) {

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

      .links {
        flex-direction: column;
        gap: 1rem;
      }

      .links a {
        width: 100%;
        margin-bottom: 0.5rem;
        text-align: center;
      }
    }
  </style>
</head>

<body>
  <?php include "header.php"; ?>

  <main>
    <h2>📚 Your Uploaded Books</h2>
    <a href="upload.php" class="button" style="margin-bottom: 1rem; display:inline-block;">➕ Add Book</a>

    <?php if ($result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Pages</th>
            <th>Uploaded</th>
            <th>Download</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td data-label="Name"><?= htmlspecialchars($row["original_name"]) ?></td>
              <td data-label="Type"><?= strtoupper($row["file_type"]) ?></td>
              <td data-label="Pages"><?= (int)$row["pages"] ?></td>
              <td data-label="Uploaded"><?= $row["uploaded_at"] ?></td>
              <td data-label="Download">
                <a class="button" href="uploads/<?= rawurlencode($row["filename"]) ?>" download="<?= htmlspecialchars($row["original_name"]) ?>">⬇ Download</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No books uploaded yet.</p>
    <?php endif; ?>

    <div class="links">
      <a href="upload.php">➕ Upload More</a>
      <a href="dashboard.php" class="dashboard">🏠 Dashboard</a>
    </div>
  </main>
</body>

</html>

<?php $conn->close(); ?>