<?php
session_start();
include "db_conn.php";

if (isset($_POST['usuario']) && isset($_POST['senha'])) {

    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $usuario = validate($_POST['usuario']);
    $senha = validate($_POST['senha']);

    $usuario = $conn->real_escape_string($usuario);

    $sql = "SELECT * FROM users WHERE usuario='$usuario'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($senha, $row['senha'])) {
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['id'] = $row['id'];
            $_SESSION['nome'] = $row['nome'];
            $_SESSION['role'] = $row['role'];

            if ($_SESSION['role'] == 'admin') {
                header("Location: admin.php");
                exit();
            } else {
                header("Location: contratos.php");
                exit();
            }

        } else {
            header("Location: index.php?error=Usuário ou senha incorretos");
            exit();
        }

    } else {
        header("Location: index.php?error=Usuário ou senha incorretos");
        exit();
    }

}
?>
