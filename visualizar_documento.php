<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['usuario']) && $_SESSION['role'] == 'admin') {
    if (isset($_GET['id'])) {
        $doc_id = $_GET['id'];

       $host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";

        $conn = new mysqli($host, $user, $password, $dbname);

        if ($conn->connect_error) {
            die("Erro de conexão: " . $conn->connect_error);
        }

        $sql = "SELECT caminho_arquivo, nome_documento FROM documentos_clientes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doc_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($caminho_arquivo, $nome_documento);
            $stmt->fetch();

            if (file_exists($caminho_arquivo)) {
                $extensao = pathinfo($caminho_arquivo, PATHINFO_EXTENSION);
                $extensao = strtolower($extensao);

                $tipos_imagem = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

                if (in_array($extensao, $tipos_imagem)) {
                    echo '<img src="' . $caminho_arquivo . '" alt="' . $nome_documento . '" style="max-width: 100%; height: auto;">';
                } else {
                    header('Content-Type: application/pdf'); 
                    readfile($caminho_arquivo);
                }
            } else {
                echo "Arquivo não encontrado.";
            }
        } else {
            echo "Documento não encontrado.";
        }

        $conn->close();
    } else {
        echo "ID do documento não fornecido.";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
