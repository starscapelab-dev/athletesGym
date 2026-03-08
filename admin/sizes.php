<?php
$pageTitle = "Sizes";
require "header.php";
require "db.php"; 
?>

<!DOCTYPE html>
<html>
<head><title>Sizes</title></head>
<body>
  <h2>Sizes</h2>
  <a href="size_add.php">Add Size</a>
  <table>
    <tr><th>ID</th><th>Name</th><th>Action</th></tr>
    <?php foreach($pdo->query("SELECT * FROM sizes") as $row): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td>
        <a href="size_edit.php?id=<?= $row['id'] ?>">Edit</a> | 
        <a href="size_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
