<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "mysql.site2.taticaweb.com.br";
$user = "boss_site2";
$password = "mXpMqNzpj7GYC8H";
$dbname = "boss_site2";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}


if (isset($_GET['tipo_id'])) {
    
    $tipo_id = $_GET['tipo_id'];

    $sql_checklist = "SELECT item FROM checklist_preset WHERE tipo_id = ? ORDER BY id ASC";
    $stmt = $conn->prepare($sql_checklist);
    $stmt->bind_param("i", $tipo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $checklist_items = [];
        while ($row = $result->fetch_assoc()) {
            $checklist_items[] = $row['item']; 
        }
        echo json_encode($checklist_items); 
    } else {
        echo "Nenhum item encontrado para o tipo_id: $tipo_id";
    }

    $stmt->close();
    $conn->close();
} else {
    die("Parâmetro tipo_id não fornecido");
}

?>

