<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];

    $sql = "SELECT * FROM users WHERE usuario = '$usuario' OR email = '$usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50));

        $sql = "UPDATE users SET token = '$token' WHERE id = " . $user['id'];
        $conn->query($sql);

        $url = "https://site2.taticaweb.com.br/nova_senha.php?token=$token";

        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/SMTP.php';


        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'pablomunheco2005@gmail.com';
            $mail->Password = 'awfx iwvr txlz ofyg';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('no-reply@realassetinvest.com.br', 'RealAssetInvest');
            $mail->addAddress($user['email']);
            $mail->isHTML(true);
            $mail->Subject = 'Redefinir Senha';
            $mail->Body    = "Clique no link para redefinir sua senha: <a href='$url'>$url</a>";

            if ($mail->send()) {
                echo "Um link para redefinir sua senha foi enviado para o seu e-mail.";
            } else {
                echo "Falha ao enviar o e-mail.";
            }
        } catch (Exception $e) {
            echo "Erro ao enviar e-mail. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $erro = "Usuário ou e-mail não encontrado.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci minha senha</title>
    <style>


        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
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

        .error-message {
            color: red;
            text-align: center;
        }


    </style>








<style>

* {
    margin: 0px;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #f4f4f9;
    /* padding: 6% 14% 7%;*/
    color: #333;
}


header {
    text-align: center;
    margin-bottom: 20px;
}

h1 {
    color: #333;
    font-size: 2.5em;
    margin-bottom: 10px;
}

h2 {
    color: #333;
    font-size: 1.8em;
    margin-bottom: 15px;
}


nav {
    background: linear-gradient(308deg, rgba(2,60,86,1) 0%, rgba(30,117,101,1) 100%);
    padding: 8px 25px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 25px;
}

nav ul {
    display: flex;
    justify-content: right;
    list-style: none;
}

nav ul li {
    margin: 0 25px;
}

nav ul li a {
    text-decoration: none;
    color: white;
    font-size: 1.2em;
    font-weight: 600;
    padding: 12px 0px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

nav ul li a i {
    margin-right: 8px;
    font-size: 1.4em;
}

nav ul li a:hover {
    background-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
}


table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 12px;
    text-align: left;
    /*border: 1px solid #ddd;*/
    font-size: 1.1em;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #0B5F7C;
    color: white;
}

table td {
    background-color: #fff;
}

 .padding-body {
    padding: 6% 17% 2%;

}

.status {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
}

.ativo {
    background-color: #00c076;
    color: white;
}

.finalizado {
    background-color: #e74c3c;
    color: white;
}

.inativo {
    background-color: #f39c12;
    color: white;
}

.acao-btn {
    background-color: #0974a3;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 1em;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease;
}

.acao-btn2 {
    background-color: #cd4739;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 1em;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease;
}

.acao-btn:hover {
    background-color: #2980b9;
}


.novo-btn {
    background-color: #00c076;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 1.2em;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-bottom: 0px;
    display: block;
    width: 150px;
    text-align: center;
    text-decoration: none;
}

.novo-btn:hover {
    background-color: #27ae60;
}

.box {
    background: #fff;
    padding: 50px;
    border-radius: 10px;
    box-shadow: 0 13px 20px rgba(0, 0, 0, 0.2);
}

.box2 {
    background: #ebeef5;
    padding: 5px;
    border-radius: 10px;
}

footer {
    position: relative;
    text-align: center;
    font-size: 0.9em;
    margin-top: 40px;
    color: #555;
}

footer .copyright {
    position: relative;
    margin-bottom: 20px; /* Espaçamento acima da imagem */
}

footer .footer-image {
    position: absolute;
    left: 0;
    width: 100%;
    height: 105px;
    background: url('boss/img/background/bg-6.png') no-repeat center center;
    background-size: cover;
}


/*footer {
    text-align: center;
    font-size: 0.9em;
    margin-top: 40px;
    color: #555;
}*/


@media (max-width: 768px) {
    nav ul {
        flex-direction: column;
        align-items: center;
    }

    nav ul li {
        margin: 10px 0;
    }

    nav ul li a {
        font-size: 1.1em;
    }

    table {
        font-size: 0.9em;
    }

}




@media (max-width: 768px) {
    .contratos {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .contratos li {
        width: 90%; /* Reduz a largura em telas menores */
        height: auto;
        margin: 15px 0;
    }

    header {
        flex-wrap: wrap;
        text-align: center;
    }

    header div {
        max-width: 100%;
    }

    nav ul {
        flex-direction: column;
        align-items: flex-start;
    }

    nav ul li {
        margin: 5px 0;
    }

    .box {
        padding: 20px;
        margin-left: -45px;
        width: 340px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    select {
        width: 100%; /* Ajuste para ocupar toda a largura */
        margin-bottom: 10px;
    }

    table {
        font-size: 0.8em;
        overflow-x: auto;
        display: block;
    }

    .novo-btn {
        width: auto;
        margin: 0 auto;
    }
}

@media (max-width: 480px) {
    .contratos li {
        font-size: 0.9em;
        padding: 10px;
    }

    .status {
        font-size: 0.8em;
        padding: 5px 8px;
    }

    h1 {
        font-size: 1.5em;
    }

    nav ul li a {
        font-size: 1em;
    }
}



</style>



</head>
<body>

<div class="padding-body">
        <!--<header>
            <h1>Área Administrativa - Gerenciamento de Documentos</h1>
        </header>-->

        <header style="display: flex; justify-content: space-between; align-items: center; background: rgb(255,255,255,0); padding: 0px 8px;">
        <!-- Logo alinhado à esquerda -->
        <div style="max-width: 460px;">
            <img src="boss/img/icons/logo2.png" alt="Logo" style="height: auto; max-height: 125px; width: 100%; object-fit: contain;">
        </div>
        <!-- Título alinhado à direita -->
        <div class="box2"><h1 style="font-size: 1.5em; margin: 0; text-align: right; padding: 5px 20px 5px; color: #023c56;"><a style="font-weight: 800;">Início ></a><a style="font-weight:300"> Esqueci Minha Senha</a></h1></div>
        </header>



        <nav>
            <ul>

                <li><a href="index.php"><i class="fas fa-file-contract"></i>Início</a></li>

            </ul>
        </nav>

        <div class="box">



        <main>



    <div class="container"">
        <h2>Esqueci minha senha</h2>

        <form method="POST">
            <label for="usuario">Usuário ou E-mail:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário ou e-mail" required>

            <button type="submit">Enviar Link de Redefinição</button>
        </form>

        <div class="error-message">
            <?php if ($erro) echo $erro; ?>
        </div>
    </div>

    </main>

    </div>
</div>



<footer>
        <div class="copyright">&copy; 2024 Tática</div>
        <div class="footer-image"></div>
        </footer>

</body>
</html>
