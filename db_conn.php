<?php
$servername = "mysql.site2.taticaweb.com.br";
    $username = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
