<?php
include "database.php";

$id = $_GET["id"] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM productos WHERE id=?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;
?>
