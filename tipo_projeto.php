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

    $sqlCreateTable = "CREATE TABLE IF NOT EXISTS tipos_projeto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $conn->query($sqlCreateTable);

    $sqlCreateChecklistTable = "CREATE TABLE IF NOT EXISTS checklist_tipo_projeto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tipo_projeto_id INT NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tipo_projeto_id) REFERENCES tipos_projeto(id) ON DELETE CASCADE
    )";

    $conn->query($sqlCreateChecklistTable);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_tipo_projeto'])) {
        $nome = $conn->real_escape_string($_POST['nome_tipo_projeto']);

        $sqlInsertTipoProjeto = "INSERT INTO tipos_projeto (nome) VALUES ('$nome')";

        if ($conn->query($sqlInsertTipoProjeto)) {
            $tipoProjetoId = $conn->insert_id;
            header("Location: preencher_checklist.php?tipo_projeto_id=$tipoProjetoId");
            exit();
        } else {
            echo "Erro ao criar tipo de projeto: " . $conn->error;
        }
    }

    if (isset($_GET['excluir_id'])) {
        $tipoProjetoId = (int) $_GET['excluir_id'];

        $sqlDeleteTipoProjeto = "DELETE FROM tipos_projeto WHERE id = $tipoProjetoId";
        if ($conn->query($sqlDeleteTipoProjeto)) {
            header("Location: tipo_projeto.php");
            exit();
        } else {
            echo "Erro ao excluir tipo de projeto: " . $conn->error;
        }
    }


    $sqlSelectTiposProjeto = "SELECT * FROM tipos_projeto ORDER BY created_at DESC";
    $resultTiposProjeto = $conn->query($sqlSelectTiposProjeto);
    ?>

    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
        <title>Admin - Gerenciamento de Tipos de Projeto</title>
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f4f4f9;
                color: #333;
                margin: 0;
                padding: 0;
            }



            h1 {
                font-size: 2em;
                margin-bottom: 20px;
                color: #023c56;
            }



            .novo-btn {
                background-color: #00c076;
                color: white;
                padding: 8px 15px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                text-decoration: none;
                font-size: 1em;
            }

            .excluir-btn{

              background-color: #dc3545;
              color: white;
              padding: 5px 15px;
              border: none;
              border-radius: 5px;
              cursor: pointer;
              text-decoration: none;
              font-size: 1em;
              display: inline-block;
              margin: 15px 0px;

            }

            .novo-btn:hover {
                background-color: #27ae60;
            }

            form {
                margin-top: 20px;
            }

            input[type="text"] {
                width: calc(100% - 20px);
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            button {
                background-color: #007bff;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            button:hover {
                background-color: #0056b3;
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
                padding: 8px 15px;
                border-radius: 5px;
                font-size: 1.2em;
                cursor: pointer;
                transition: background-color 0.3s ease;
                margin-bottom: 0px;
                display: block;
                text-align: center;
                text-decoration: none;
            }

            .novo-btn:hover {
                background-color: #27ae60;
            }



            .box2 {
                background: #ebeef5;
                padding: 5px;
                border-radius: 10px;
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


                .contratos li {
                    width: 90%; 
                    height: auto;
                    margin: 15px 0;
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



        <style>
        
    * {
    margin: 0px;
    padding: 0;
    box-sizing: border-box;
    }

    
    body {
    font-family: 'Inter', sans-serif;
    background-color: #f9f9f9;
    color: #3e4e58;
    line-height: 1.6;
    overflow-x: hidden;
    }

    header {
        display: flex;
        align-items: center; 
        justify-content: space-between; 
        padding: 20px 200px; 
        background: #fff;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 2px solid #f1f1f1;
    }

    header img {
        max-height: 70px;
        object-fit: contain;
    }

    header h1 {
        font-size: 2rem;
        color: #2c3e50;
        font-weight: 700;
        margin-top: 10px;
    }

 
    nav {
    background: linear-gradient(308deg, rgba(2,60,86,1) 0%, rgba(30,117,101,1) 100%);
    padding: 17px 30px;
    margin-bottom: 30px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
    }

    nav ul {
    display: flex;
    justify-content: center;
    list-style: none;
    flex-wrap: wrap;

    }

    nav ul li {
    }

    nav ul li a {
    text-decoration: none;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    padding: 13px 20px 13px 21px;
    border-radius: 10px;
    transition: all 0.3s ease;
    margin-top: 10px;
    }

    nav ul li a:hover {
    background-color: #f1f1f1;
    color: #2c3e50;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

   
    .container {
    padding: 50px 200px !important;
    }

    .box {
    background: linear-gradient(145deg, #ffffff, #f1f1f1);
    padding: 45px;
    border-radius: 15px;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 40px;
    }

    .box:hover {
    transform: translateY(-5px);
    box-shadow: 0 40px 80px rgba(0, 0, 0, 0.1);
    }

    .box h2 {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    }

    .btn {
    background-color: #3498db;
    color: white;
    font-weight: 600;
    padding: 12px 30px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    }

    .btn:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
    }

    .btn-primary {
    background-color: #1abc9c;
    }

    .btn-primary:hover {
    background-color: #16a085;
    }

    .search-container {
    display: flex;

    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    }

    .search-container input {
    width: 350px;
    padding: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background-color: #ffffff;
    color: #3e4e58;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    }

    .search-container input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 8px rgba(52, 152, 219, 0.4);
    }

    .search-container button {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 5px 30px;
    font-size: 1.1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-left: 10px;
    }

    .search-container button:hover {
    background-color: #2980b9;
    }

    table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    }

    table th,
    table td {
    padding: 18px 25px;
    text-align: left;
    border-bottom: 1px solid #f1f1f1;
    font-size: 1.1rem;
    }

    table th {
    background-color: #ecf0f1;
    color: #2c3e50;
    font-weight: 700;
    }

    table td {
    background-color: #ffffff;
    color: #3e4e58;
    }

    .status {
    padding: 8px 15px;
    border-radius: 5px;
    font-weight: 600;
    text-transform: capitalize;
    }

    .status.ativo {
    background-color: #00c076;
    color: white;
    }

    .status.inativo {
    background-color: #b1b1b1;
    color: white;
    }

    .status.finalizado {
    background-color: #dc3545;
    color: white;
    }

    .card {
    background: #ffffff;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 30px 70px rgba(0, 0, 0, 0.05);
    margin-bottom: 40px;
    transition: all 0.3s ease;
    }

    .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 40px 90px rgba(0, 0, 0, 0.1);
    }

    .card-header {
    background-color: #ecf0f1;
    color: #2c3e50;
    padding: 20px;
    border-radius: 15px 15px 0 0;
    font-size: 1.3rem;
    }

    .card-body {
    padding: 25px;
    }

    footer {

    color: #7f8c8d;
    padding: 40px 60px;
    text-align: center;

    }

    footer p {
    font-size: 1.2rem;
    font-weight: 500;
    }

    @media (max-width: 1200px) {
    .container {
        padding: 20px 40px;
    }

    .box {
        padding: 25px;
    }

    .search-container input {
        width: 100%;
    }

    .search-container button {
        width: 100%;
        margin-top: 10px;
    }
    }

    @media (max-width: 768px) {
    .container {
        padding: 20px 20px !important;
    }

    header {
        padding: 20px;
        flex-direction: column;
        align-items: center;
    }

    header h1 {
        font-size: 2rem;
        text-align: center;
        margin-top: 10px;
    }

    nav ul {
        flex-direction: column;
    }

    nav ul li {
        margin-bottom: 10px;
    }

    .box {
        padding: 15px;
    }

    table th, table td {
        padding: 12px;
        font-size: 1rem;
    }

    footer {
        padding: 20px;
    }
    }

    @media (max-width: 480px) {
    .search-container input {
        width: 100%;
        margin-bottom: 15px;
    }

    .search-container button {
        width: 100%;
        margin-top: 3px;
        margin-left: 0;
    }


    .novo-btn {

      width: 100% !important;
      margin-top: 30px;
      max-width: 359px;

    }

    header h1 {
        font-size: 1.8rem;
    }
    }

    .novo-btn {
                background-color: #00c076;
                color: white;
                padding: 5px 15px;
    border-radius: 5px;
    font-size: 1em;
                cursor: pointer;
                transition: background-color 0.3s ease;
                margin-bottom: 0px;
                display: block;
                text-align: center;
                text-decoration: none;
                margin-left: auto;
            }

            .novo-btn:hover {
                background-color: #27ae60;
            }



    footer {
                position: relative;
                text-align: center;
                font-size: 0.9em;
                margin-top: 40px;
                color: #555;
                margin: 0;
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
                margin: 0;
            }




            .acao-btn {
                background-color: #0974a3;
                color: white;
                padding: 8px 15px;
                border-radius: 5px;
                font-size: 1em;
                cursor: pointer;
                border: none;
                transition: background-color 0.3s ease;
            }

            .acao-btn2 {
                background-color: #cd4739;
                color: white;
                padding: 8px 15px;
                border-radius: 5px;
                font-size: 1em;
                cursor: pointer;
                border: none;
                transition: background-color 0.3s ease;
            }

            .acao-btn:hover {
                background-color: #2980b9;
            }



            .acao-btn, .acao-btn2 {
         padding: 8px 15px;
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


    th:nth-child(3), td:nth-child(3) {

        width: 22%;
        padding-right: 15px;
    }


    th:nth-child(2), td:nth-child(2) {
        text-align: center;
        width: 1%;

      }





      @media (max-width: 1200px) {
        header {
            padding: 20px;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        header img {
            margin: 0;
        }

        header h1 {
            margin: 10px 0 0;
            font-size: 1.8rem;
        }

        nav ul {
            flex-wrap: wrap;
            justify-content: center; 
        }

        table th, table td {
            padding: 12px;
            font-size: 1rem;
            text-align: center;
        }

        table th:nth-child(1), table td:nth-child(1) {
            text-align: left;
            width: 50%;
        }

        table th:nth-child(2), table td:nth-child(2) {
            width: 20%;
        }

        table th:nth-child(3), table td:nth-child(3) {
            width: 30%; 
        }
    }

    @media (max-width: 768px) {


      .novo-btn, .excluir-btn {
    display: block !important;
    margin: 7px 0px !important;

  }


        header h1 {
            font-size: 1.5rem;
        }

        nav ul {
            flex-direction: column;
            gap: 20px;
        }

        nav ul li {
            margin-bottom: 10px;
        }

        table th, table td {
            font-size: 0.9rem;
        }

        table th:nth-child(1), table td:nth-child(1) {
            text-align: left;
            width: 60%; 
        }

        table th:nth-child(2), table td:nth-child(2) {
            width: 20%;
        }

        table th:nth-child(3), table td:nth-child(3) {
            width: 20%;
                    padding-left: 0px !important;
        }
    }

    @media (max-width: 480px) {
        header h1 {
            font-size: 1.2rem;
        }

        nav ul {
            flex-direction: column;
            align-items: center;
        }

        table th, table td {
            font-size: 0.8rem;
            padding: 10px;
        }

        table th:nth-child(1), table td:nth-child(1) {
            text-align: left;
            width: 70%; 
        }

        table th:nth-child(2), table td:nth-child(2) {
            width: 15%; 
        }

        table th:nth-child(3), table td:nth-child(3) {
            width: 15%; 
        }
    }



    .novo-btn, .excluir-btn {
    display: inline-block; 
    padding: 8px 12px; 
    margin: 0 5px; 
    border: none; 
    border-radius: 5px; 
    text-decoration: none;
    font-size: 14px; 
    cursor: pointer;
    transition: background-color 0.3s ease;
    color: white; 
}


td {
    vertical-align: middle; 
    white-space: nowrap; 
}


.novo-tipo{

padding-top: 25px;


}

        </style>


        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    </head>
    <body>

      <header>
          <img src="boss/img/icons/logo2.png" alt="Logo">
          <h1>Área Administrativa</h1>
      </header>

      <nav>
          <ul>
              <li><a href="admin.php"><i class="bi bi-house-door"></i> Projetos</a></li>
              <li><a href="tipo_projeto.php"><i class="bi bi-bar-chart"></i> Tipos de Projeto</a></li>
              <li><a href="clientes.php"><i class="bi bi-person"></i> Clientes</a></li>
              <li><a href="admin_documentos.php"><i class="bi bi-gear"></i> Documentos</a></li>
  <li><a href="logout.php"><i class="bi bi-gear"></i> Sair</a></li>
          </ul>
      </nav>



        <div class="container">
          <div class="box">

            <h2>Gerenciar Tipos de Projeto</h2>

            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($tipoProjeto = $resultTiposProjeto->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($tipoProjeto['nome']) ?></td>

                            <td><a class="novo-btn" href="preencher_checklist.php?tipo_projeto_id=<?= $tipoProjeto['id'] ?>">Editar</a>
                            <a class="excluir-btn" href="?excluir_id=<?= $tipoProjeto['id'] ?>"
                   onclick="return confirm('Tem certeza de que deseja excluir este tipo de projeto? Esta ação não pode ser desfeita.')">
                   <i class="fa-solid fa-delete-left"></i>
                </a> </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="novo-tipo">
            <form method="POST">
                <h2>Criar Novo Tipo de Projeto</h2>
                <input type="text" name="nome_tipo_projeto" placeholder="Nome do Tipo de Projeto" required>
                <button type="submit" name="novo_tipo_projeto">Criar Tipo de Projeto</button>
            </form>
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

    <?php
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>

