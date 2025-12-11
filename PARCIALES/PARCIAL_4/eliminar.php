<?php
include "database.php";

$id = $_GET["id"] ?? null;
if ($id) {
    $sql = "DELETE FROM productos WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
header("Location: index.php");
exit;
?>
