<?php
$host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM users WHERE token = '$token'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nova_senha = $_POST['senha'];
	$hashed_password = password_hash($nova_senha, PASSWORD_DEFAULT);
           $stmt = $conn->prepare("UPDATE users SET senha = ?, token = NULL WHERE token = ?");
    $stmt->bind_param("ss", $hashed_password, $token);
            if ($stmt->execute()) {
                echo "Senha redefinida com sucesso. <a href='index.php'>Clique aqui para fazer login</a>";
            } else {
                echo "Erro ao redefinir a senha.";
            }
        }
    } else {
        echo "Token inválido.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #024057;
        }

        label {
            font-size: 14px;
            color: #024057;
            margin-bottom: 10px;
            display: block;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #00c077;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #024057;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Redefinir Senha</h2>

        <form method="POST">
            <label for="senha">Nova Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua nova senha" required>

            <button type="submit">Redefinir Senha</button>
        </form>
    </div>

</body>
</html>
