<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <title>Login - RealAssetInvest</title>
    <style>
        body {
            font-family: 'inter', sans-serif;
            margin-left: 12.5%;
            padding: 0;
            background: url('boss/img/background/bg-3.png') no-repeat center center fixed; /* Fundo de página */
            background-size: cover; /* Faz com que a imagem cubra 100% da tela */
            display: flex;
            flex-direction: column; /* Empilha os elementos verticalmente */
            justify-content: center; /* Centraliza os elementos verticalmente */
            align-items: flex-start; /* Alinha os elementos horizontalmente à esquerda */
            height: 100vh;
            padding-left: 0px; /* Espaçamento da borda esquerda */
        }

        .logo {
            width: 100%;
            max-width: 380px; /* Mesmo tamanho máximo do container */
            margin-bottom: 20px; /* Espaçamento entre o logo e o container */
            margin-left: 5.75%;
            margin-top: -90px;
        }

        .logo img {
            width: 100%; /* Largura total do container */
            height: auto; /* Mantém a proporção da imagem */
        }

        .container {
            background: linear-gradient(12deg, rgba(31,189,147,0.1) 0%, rgba(28,168,131,0) 50%); /* gradiente transparente */
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 13px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 31.25%; /* Largura máxima */
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 14px;
            color: #fff;
            text-align: left;
            margin-bottom: 5px;
        }

        input {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #00c077;
        }

        button {
            background: #00c976;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 5px;
        }

        button:hover {
            background: #022d3e;
        }

        a {
            color: #00c077;
            text-decoration: none;
            margin-top: 20px;
            font-size: 14px;
        }

        footer {
            position: absolute;
            bottom: 20px;
            width: auto;
            text-align: left; /* Texto alinhado à esquerda */
            color: #fff;
            font-size: 12px;
            margin-left: 6.1%;
        }

        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            body {
                margin-left: 0;
                align-items: center; /* Centraliza os itens horizontalmente */
                padding: 20px;
            }

            .logo {
                margin-left: 0;
                margin-top: -50px;
            }

            .container {
                max-width: 90%; /* Reduz o tamanho do container em telas menores */
                padding: 20px;
            }

            footer {
                text-align: center;
                margin-left: 0;
                font-size: 10px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            input, button {
                font-size: 12px;
                padding: 10px;
            }

            label {
                font-size: 12px;
            }

            a {
                font-size: 12px;
            }

            footer {
                font-size: 8px;
            }
        }


    </style>
</head>
<body>
    <!-- Logo flutuante acima do container -->
    <div class="logo">
        <img src="boss/img/icons/logo.png" alt="Logo RealAssetInvest">
    </div>

    <!-- Container do formulário -->
    <div class="container">
        <form action="login.php" method="post">
            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Usuário" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Senha" required>

            <button type="submit">Entrar</button>

            <a href="redefinir_senha.php">Esqueci minha senha</a>

            <div id="error-message">
                <?php if (isset($_GET['error'])) { ?>
                <p class="error-message">
                    <?php echo $_GET['error']; ?>
                </p>
                <?php } ?>
            </div>
        </form>
    </div>

    <!-- Rodapé -->
    <footer>
        <p>&copy; 2024 RealAssetInvest - Todos os direitos reservados.</p>
    </footer>
</body>
</html>
