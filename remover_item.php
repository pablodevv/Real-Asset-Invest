<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$host = "mysql.site2.taticaweb.com.br";
$user = "boss_site2";
$password = "mXpMqNzpj7GYC8H";
$dbname = "boss_site2";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_POST['remover_item'])) {
    $item_id = intval($_POST['remover_item']);

    $sql = "DELETE FROM checklist WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);

    if ($stmt->execute()) {
        echo "Item removido com sucesso!";
    } else {
        echo "Erro ao remover item!";
    }
}
?>
