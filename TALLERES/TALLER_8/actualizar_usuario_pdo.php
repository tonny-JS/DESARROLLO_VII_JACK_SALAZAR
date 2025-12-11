<?php
require_once "config_pdo.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "Usuario actualizado con éxito.";
        } else {
            echo "ERROR: No se pudo ejecutar $sql. " . $stmt->errorInfo()[2];
        }
    }
    unset($stmt);
}
unset($pdo);
?>

<form method="post">
    <label>ID</label><input type="number" name="id" required>
    <label>Nombre</label><input type="text" name="nombre" required>
    <label>Email</label><input type="email" name="email" required>
    <input type="submit" value="Actualizar Usuario">
</form>
