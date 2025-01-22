<?php

$host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


if (isset($_GET['id'])) {
    $cliente_id = $_GET['id'];


    $sql = "SELECT * FROM clientes WHERE id = $cliente_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $cliente = $result->fetch_assoc();
        echo json_encode($cliente);
    } else {

        echo json_encode(['error' => 'Cliente não encontrado']);
    }
} else {
    
    echo json_encode(['error' => 'ID não fornecido']);
}

$conn->close();
?>
