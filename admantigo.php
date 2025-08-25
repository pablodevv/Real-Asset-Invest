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

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $sql = "SELECT * FROM contratos WHERE nome_contrato LIKE ?";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $contratos = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $contratos = [];
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <title>Admin - Gerenciamento de Contratos</title>
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f9;
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
            margin-bottom: 20px; 
        }

        footer .footer-image {
            position: absolute;
            left: 0;
            width: 100%;
            height: 105px;
            background: url('boss/img/background/bg-6.png') no-repeat center center;
            background-size: cover;
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

            table {
                font-size: 0.9em;
            }

        }



        input {
            width: 87%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }



        button {
            background-color: #1abc9c;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }



        @media (max-width: 768px) {
            .contratos {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            nav{

              width: 340px;
              margin-left: -25px;


            }


            header{

              width: 340px;
              margin-left: -25px;


            }

            .contratos li {
                width: 90%; 
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
                margin-left: -25px;
                width: 340px;
            }

            form {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            select {
                width: 100%; 
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


        table {
     width: 100%;
     border-collapse: collapse;
 }

 th, td {
     padding: 10px;
     text-align: left;
 }

 th:nth-child(2),
 td:nth-child(2) {
     text-align: center;
     width: 1%;
 }

 th:nth-child(3),
 td:nth-child(3) {
     text-align: right;
     width: 26%;
     padding-right: 15px; 
 }

 .status {
     padding: 5px 10px;
     border-radius: 5px;
     display: inline-block;
 }

 .acao-btn, .acao-btn2 {
     margin-left: 5px;
     padding: 5px 10px;
     border: none;
     border-radius: 5px;
     cursor: pointer;
     font-size: 14px;
 }

 .acao-btn {
     background-color: #007bff;
     color: white;
 }

 .acao-btn:hover {
     background-color: #0056b3;
 }

 .acao-btn2 {
     background-color: #dc3545;
     color: white;
 }

 .acao-btn2:hover {
     background-color: #a71d2a;
 }

 th:nth-child(3) {
     text-align: left;
     padding-left: 100px; 
 }

 td:nth-child(3) {
     text-align: right; 
 }


    </style>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


</head>
<body>
    <div class="padding-body">
      
        <header style="display: flex; justify-content: space-between; align-items: center; background: rgb(255,255,255,0); padding: 0px 8px;">
        <div style="max-width: 460px;">
            <img src="boss/img/icons/logo2.png" alt="Logo" style="height: auto; max-height: 125px; width: 100%; object-fit: contain;">
        </div>
        <div class="box2"><h1 style="font-size: 1.5em; margin: 0; text-align: right; padding: 5px 20px 5px; color: #023c56;"><a style="font-weight: 800;">Área Administrativa ></a><a style="font-weight:300"> Gerenciamento de Projetos</a></h1></div>
        </header>



        <nav>
            <ul>

                <li><a href="admin.php"><i class="fas fa-file-contract"></i>Projetos</a></li>
                <li><a href="tipo_projeto.php" style="width: 190px;"><i class="fa-solid fa-list-check"></i>Tipos de Projeto</a></li>
                <li><a href="clientes.php"><i class="fa-solid fa-users"></i></i>Clientes</a></li>
                <li><a href="admin_documentos.php"><i class="fas fa-file-upload"></i>Documentos</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Sair</a></li>
            </ul>
        </nav>

        <div class="box">



        <main>
            <div style="display: flex; justify-content: space-between; align-items: center; background: rgb(255,255,255,0); padding: 0px 3px;">
                <h2>Gerenciar Projetos</h2>
                <a href="novo_contrato.php" class="novo-btn">+ Novo</a>

            </div>


            <form method="get" action="">
                    <input type="text" name="search" placeholder="Buscar contrato..." value="<?php echo htmlspecialchars($search); ?>" style="width: 80%;">
                      <button type="submit" class="btn-submit" style="background-color: #0974a3; width: 150px;">Buscar</button>
                </form>

            <table>

                <thead>
                    <tr>
                        <th>Nome do Projeto</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($contratos) > 0) : ?>
                        <?php foreach ($contratos as $contrato) : ?>
                            <tr>
                                <td><?php echo $contrato['nome_contrato']; ?></td>
                                <td><span class="status <?php echo $contrato['status']; ?>"><?php echo ucfirst($contrato['status']); ?></span></td>
                                <td>
                                    <button class="acao-btn" onclick="window.location.href='editar_contrato.php?id=<?php echo $contrato['id']; ?>'" style="background-color: #0974a3;">Editar</button>
                                    <button class="acao-btn2" onclick="window.location.href='excluir_contrato.php?id=<?php echo $contrato['id']; ?>'">Excluir</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3">Nenhum contrato encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
           

            </div>
        </main>
        </div>
    </div>
    
        <footer>
        <div class="copyright">&copy; 2024 Tática</div>
        <div class="footer-image"></div>
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

