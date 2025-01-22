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

if (isset($_GET['id'])) {
    $id_contrato = $_GET['id'];


    $sql = "DELETE FROM contratos WHERE id = $id_contrato";

    if ($conn->query($sql) === TRUE) {
        
        header("Location: admin.php"); 
        exit();
    } else {
        echo "Erro ao excluir contrato: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Contrato</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Confirmar Exclusão</h2>
        <p>Tem certeza que deseja excluir este contrato?</p>
        <a href="excluir_contrato.php?id=<?php echo $_GET['id']; ?>" class="btn-confirmar">Sim, excluir</a>
        <a href="admin.php" class="btn-cancelar">Cancelar</a>
    </div>
</body>
</html>
