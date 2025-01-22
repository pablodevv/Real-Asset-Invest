<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['usuario'])) {
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Ajuda</title>
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
          margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
            color: #333;
        }


        header {
            text-align: center;
            margin-bottom: 30px;
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
            background: linear-gradient(135deg, #1abc9c, #16a085);
            padding: 15px 20px;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        nav ul {
            display: flex;
            justify-content: center;
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
            padding: 12px 20px;
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


        .formulario-ajuda {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .formulario-ajuda label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 1.1em;
        }

        .formulario-ajuda input,
        .formulario-ajuda textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1em;
            color: #333;
        }

        .formulario-ajuda textarea {
            height: 150px;
            resize: vertical;
        }

        .formulario-ajuda button {
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            font-size: 1.2em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .formulario-ajuda button:hover {
            background-color: #2980b9;
        }


        footer {
            text-align: center;
            font-size: 0.9em;
            margin-top: 40px;
            color: #555;
        }


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

            .formulario-ajuda {
                padding: 20px;
            }

            .formulario-ajuda input,
            .formulario-ajuda textarea {
                font-size: 1em;
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-ChzDgzNRGTfeRM4dlP1wOaEXQ4pxI9jc7VUhvmF3b0sLFXH52+I5f0LjxSK6AoTShH7T/" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <header>
        <h1>Bem-vindo à Página de Ajuda!</h1>
    </header>


    <nav>
        <ul>
            <li><a href="contratos.php"><i class="fas fa-file-contract"></i>Contratos</a></li>
            <li><a href="home.php"><i class="fas fa-file-upload"></i>Documentos</a></li>
<li><a href="minha_conta.php"><i class="fas fa-user"></i>Ajuda</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Sair</a></li>
        </ul>
    </nav>


    <main>
        <h2>Envie sua Mensagem</h2>

        <div class="formulario-ajuda">
            <form action="enviar_mensagem.php" method="post">
                <label for="nome">Seu Nome</label>
                <input type="text" id="nome" name="nome" required>

                <label for="email">Seu E-mail</label>
                <input type="email" id="email" name="email" required>

                <label for="assunto">Assunto</label>
                <input type="text" id="assunto" name="assunto" required>

                <label for="mensagem">Mensagem</label>
                <textarea id="mensagem" name="mensagem" required></textarea>

                <button type="submit">Enviar Mensagem</button>
            </form>
        </div>
    </main>

    <footer>
        &copy; 2024 Tática
    </footer>

    <script src="script.js"></script>

</body>
</html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
