<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['usuario']) && $_SESSION['role'] == 'admin') {

    $host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $documento_id = $_POST['documento_id'];
        $acao = $_POST['acao'];


        if ($acao == 'aprovar') {
            $novo_status = 'Aprovado';
            $_SESSION['mensagem'] = 'Documento aprovado com sucesso!';
        } elseif ($acao == 'reprovar') {
            $novo_status = 'Reprovado';
            $_SESSION['mensagem'] = 'Documento reprovado!';
        } else {
            die("Ação inválida");
        }


        $sql = "UPDATE documentos_clientes SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $novo_status, $documento_id);

        if ($stmt->execute()) {

            header("Location: admin_documentos.php");
            exit();
        } else {
            $_SESSION['mensagem'] = 'Erro ao atualizar o documento: ' . $conn->error;
            header("Location: admin_documentos.php");
            exit();
        }

        $stmt->close();
    }

    $conn->close();

} else {
    header("Location: index.php");
    exit();
}

?>
