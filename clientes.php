<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['usuario']) && $_SESSION['role'] == 'admin') {

$host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_cliente'])) {
    $nome = $_POST['nome'];
    $celular = $_POST['celular'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (nome, celular, email, usuario, senha, role)
            VALUES ('$nome', '$celular', '$email', '$usuario', '$senha', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "Novo cliente adicionado com sucesso!";
    } else {
        echo "Erro: " . $conn->error;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['solicitar_documento'])) {
    $cliente_id = $_POST['cliente_id'];


    $nomes_documentos = $_POST['nome_documento'];


    $sucesso = true;


    foreach ($nomes_documentos as $nome_documento) {
        $nome_documento = $conn->real_escape_string($nome_documento);
        $sql = "INSERT INTO documentos_clientes (cliente_id, nome_documento) VALUES ($cliente_id, '$nome_documento')";

        if ($conn->query($sql) !== TRUE) {
            $sucesso = false;
            break;
        }
    }

    if ($sucesso) {
        echo "Documentos solicitados com sucesso!";
    } else {
        echo "Erro ao solicitar documento.";
    }
}




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_cliente'])) {
    $cliente_id = $_POST['cliente_id'];
    $nome = $_POST['nome'];
    $celular = $_POST['celular'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Se a senha foi fornecida, realiza a atualização da senha
    if (!empty($senha)) {
        $senha = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nome='$nome', celular='$celular', email='$email', usuario='$usuario', senha='$senha' WHERE id=$cliente_id";
    } else {
        // Caso contrário, apenas atualiza os outros campos
        $sql = "UPDATE users SET nome='$nome', celular='$celular', email='$email', usuario='$usuario' WHERE id=$cliente_id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Cliente editado com sucesso!";
    } else {
        echo "Erro: " . $conn->error;
    }
}



if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['excluir_cliente'])) {
    $cliente_id = $_GET['excluir_cliente'];

    $sql = "DELETE FROM users WHERE id=$cliente_id";

    if ($conn->query($sql) === TRUE) {
        echo "Cliente excluído com sucesso!";
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Parâmetros de busca e paginação
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$itens_por_pagina = 10;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// Consulta para contar o total de registros (com busca)
$sql_contagem = "SELECT COUNT(*) as total FROM users WHERE nome LIKE '%$busca%'";
$result_contagem = $conn->query($sql_contagem);
$total_registros = $result_contagem->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $itens_por_pagina);

// Consulta para buscar os clientes (com paginação e busca)
$sql = "SELECT * FROM users WHERE nome LIKE '%$busca%' LIMIT $offset, $itens_por_pagina";
$result = $conn->query($sql);

$clientes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>


        .content-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .form-container, .table-container {
            width: 100%;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
        }

        .form-container h2, .table-container h2 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 12px;
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

        button:hover {
            background-color: #16a085;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 18px 25px !important;

            text-align: left;
        }

        .btn-action {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-action:hover {
            background-color: #2980b9;
        }

        .btn-submit {
            background-color: #2ecc71;
        }

        .btn-submit:hover {
            background-color: #27ae60;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 60%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }





    </style>





<style>








        h2 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 15px;
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







        .box2 {
            background: #ebeef5;
            padding: 5px;
            border-radius: 10px;
        }





        @media (max-width: 768px) {


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



            form {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            select {
                width: 100%; /* Ajuste para ocupar toda a largura */
                margin-bottom: 10px;
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


    </style>










    <style>
    /* RESET GLOBAL */
    * {
    margin: 0px;
    padding: 0;
    box-sizing: border-box;
    }

    /* FONTES */
    body {
    font-family: 'Inter', sans-serif;
    background-color: #f9f9f9;
    color: #3e4e58;
    line-height: 1.6;
    overflow-x: hidden;
    }

    header {
    display: flex;
    align-items: center; /* Alinha os elementos verticalmente no centro */
    justify-content: space-between; /* Distribui os elementos com espaço entre eles */
    padding: 20px 200px; /* Espaço interno do header */
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

    /* NAVIGATION BAR */
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

    /* CONTAINER PRINCIPAL */
    .container {
    padding: 50px 200px !important;
    }

    /* BOX PRINCIPAL */
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
    margin-bottom: 25px;
    }

    /* BOTÕES */
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

    /* SEARCH AREA */
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

    /* TABELAS */
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

    /* CARDS GRÁFICOS */
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

    /* FOOTER */
    footer {

    color: #7f8c8d;
    padding: 40px 60px;
    text-align: center;

    }

    footer p {
    font-size: 1.2rem;
    font-weight: 500;
    }

    /* RESPONSIVO */
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
            padding: 5px 30px;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 0px;
            display: block;
            width: 150px;
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
        justify-content: center; /* Centraliza os itens no menu */
    }

    table th, table td {
        padding: 12px;
        font-size: 1rem;
        text-align: center;
    }

    table th:nth-child(1), table td:nth-child(1) {
        text-align: left;
        width: 50%; /* Mais espaço para o nome do projeto */
    }

    table th:nth-child(2), table td:nth-child(2) {
        width: 20%; /* Ajusta o espaço do status */
    }

    table th:nth-child(3), table td:nth-child(3) {
        width: 30%; /* Ajusta o espaço das ações */
    }
    }

    @media (max-width: 768px) {
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
        width: 60%; /* Mais espaço para o nome do projeto em telas menores */
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
        width: 70%; /* Prioriza espaço para o nome do projeto */
    }

    table th:nth-child(2), table td:nth-child(2) {
        width: 15%; /* Reduz espaço do status */
    }

    table th:nth-child(3), table td:nth-child(3) {
        width: 15%; /* Reduz espaço das ações */
    }
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

          <div style="display: flex; justify-content: space-between; align-items: center;">

            <h2>Clientes Cadastrados</h2>



            <button onclick="document.getElementById('addClientModal').style.display = 'block'" class="btn-submit" style="background-color: #00c076; color: white;">
                + Novo Cliente
            </button>



          </div>

          <form method="GET" action="" style="display: flex; gap: 10px;">
              <input type="text" name="busca" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Buscar cliente...">
              <button type="submit" class="btn-submit" style="background-color: #0974a3; padding: 10px 47px !important;">Buscar</button>
          </form>

            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Celular</th>
                        <th>Usuário</th>
                        <th>Função</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
      <?php if (!empty($clientes)): ?>
          <?php foreach ($clientes as $cliente): ?>
              <tr>
                  <td style="font-size: 0.9em;"><?php echo $cliente['nome']; ?></td>
                  <td style="font-size: 0.9em;"><?php echo $cliente['email']; ?></td>
                  <td style="font-size: 0.9em;"><?php echo $cliente['celular']; ?></td>
                  <td style="font-size: 0.9em;"><?php echo $cliente['usuario']; ?></td>
                  <td style="font-size: 0.9em;"><?php echo $cliente['role']; ?></td>
                  <td>
  <div class="buttons-container">
    <button class="btn-action" onclick="editClient(<?php echo $cliente['id']; ?>)" style="background-color: #0974a3;">
      <i class="fa-solid fa-pen-to-square"></i>
    </button>

    <button class="btn-action" onclick="document.getElementById('solicitar-doc-<?php echo $cliente['id']; ?>').style.display = 'block';" style="background-color: #00C976;">
      <i class="fa-solid fa-file"></i>
    </button>

    <a href="?excluir_cliente=<?php echo $cliente['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
      <button class="btn-action" style="background-color: #cd4739;">
        <i class="fa-solid fa-delete-left"></i>
      </button>
    </a>
  </div>

<style>

/* Container para os botões */
.buttons-container {
  display: flex; /* Utiliza Flexbox para alinha-los lado a lado */
  gap: 10px; /* Adiciona um espaçamento entre os botões */
}

/* Estilo dos botões */
.btn-action {
  padding: 10px;
  border: none;
  cursor: pointer;
  color: white;
  font-size: 14px;
  border-radius: 4px;
}








@media (max-width: 768px) {
    .modal-content button {
        font-size: 12px; /* Reduz o tamanho da fonte */
        padding: 8px 16px; /* Ajusta o padding para botões menores */
    }

    .modal-content input[type="text"] {
        font-size: 12px; /* Ajusta o tamanho do texto no input */
        padding: 8px; /* Reduz o padding interno */
    }

    .modal-content {
      margin-top: 300px;
      width: 240px;
      padding: 15px;
      margin-left: 100px;
    }

    .modal-content label {
        font-size: 12px; /* Reduz o tamanho da fonte nos labels */
    }
}

</style>

<div id="solicitar-doc-<?php echo $cliente['id']; ?>" class="modal">
    <form method="POST" action="" class="modal-content">
        <span class="close" onclick="document.getElementById('solicitar-doc-<?php echo $cliente['id']; ?>').style.display = 'none';">&times;</span>
        <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">

        <h3>Solicitar Documentos</h3>
        <br>
        <div id="documentos-container-<?php echo $cliente['id']; ?>">
            <div class="form-group">

                <label for="nome_documento_<?php echo $cliente['id']; ?>">Nome do Documento:</label>
                <input type="text" name="nome_documento[]" id="nome_documento_<?php echo $cliente['id']; ?>" required>

            </div>
        </div>

        <button type="button" onclick="addDocumento(<?php echo $cliente['id']; ?>)">Adicionar Novo Documento</button>
        <button type="submit" name="solicitar_documento" class="btn-submit">Salvar</button>
    </form>
</div>


<script>
function addDocumento(clienteId) {
    var container = document.getElementById('documentos-container-' + clienteId);
    var newInput = document.createElement('div');
    newInput.classList.add('form-group');
    newInput.innerHTML = `
        <label for="nome_documento_${clienteId}">Nome do Documento:</label>
        <input type="text" name="nome_documento[]" id="nome_documento_${clienteId}" required>

        <button type="button" onclick="removeDocumento(this)" class="btn-action" style="background-color: #cd4739; margin-left: auto;">
            <i class="fa-solid fa-delete-left"></i>
        </button>
    `;
    // Adiciona o novo campo ao contêiner
    container.appendChild(newInput);
}

function removeDocumento(button) {
    var inputField = button.parentElement;
    inputField.remove(); // Remove o campo de documento
}


</script>


<style>

.form-group {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.form-group input {
    width: 80%; /* Ajuste o tamanho do campo conforme necessário */
    padding: 8px;
    margin-right: 10px;
}

.form-group button {

    margin-left: auto; /* Empurra o botão para a direita */
}




</style>




                        </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                  <tr>
                      <td colspan="6" align="center">Nenhum cliente encontrado.</td>
                  </tr>
              <?php endif; ?>
                </tbody>
            </table>

            <!-- Paginação -->
        <div style="margin-top: 20px; text-align: center;">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>" style="margin: 0 5px; color: black; <?php echo $i == $pagina_atual ? 'font-weight: bold;' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>



    </div>

    <div id="editModal" class="modal">
    <form method="POST" action="" class="modal-content">
        <span class="close" onclick="closeModall();">&times;</span>
        <h2>Editar Cliente</h2>
        <input type="hidden" name="cliente_id" id="cliente_id">
        <div class="form-group">
            <label for="edit_nome">Nome:</label>
            <input type="text" name="nome" id="edit_nome" required>
        </div>
        <div class="form-group">
            <label for="edit_celular">Celular:</label>
            <input type="text" name="celular" id="edit_celular">
        </div>
        <div class="form-group">
            <label for="edit_email">Email:</label>
            <input type="email" name="email" id="edit_email">
        </div>
        <div class="form-group">
            <label for="edit_usuario">Usuário:</label>
            <input type="text" name="usuario" id="edit_usuario" required>
        </div>
        <div class="form-group">
            <label for="edit_senha">Nova Senha:</label>
            <input type="password" name="senha" id="edit_senha">
        </div>
        <button type="submit" name="editar_cliente" class="btn-submit">Salvar Alterações</button>
    </form>
</div>


    <script>
        function editClient(id) {
            const cliente = clientesData.find(c => c.id == id);
            if (!cliente) {
                console.error("Cliente não encontrado!");
                return;
            }

            document.getElementById('cliente_id').value = cliente.id;
            document.getElementById('edit_nome').value = cliente.nome;
            document.getElementById('edit_celular').value = cliente.celular;
            document.getElementById('edit_email').value = cliente.email;
            document.getElementById('edit_usuario').value = cliente.usuario;

            document.getElementById('editModal').style.display = 'block';
        }

        function closeModall() {
            document.getElementById('editModal').style.display = 'none';
        }


        const clientesData = <?php echo json_encode($clientes); ?>;
    </script>





    <div id="addClientModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addClientModal').style.display = 'none'">&times;</span>
            <h2>Adicionar Novo Cliente</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" id="nome" required>
                </div>
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="text" name="celular" id="celular">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email">
                </div>
                <div class="form-group">
                    <label for="usuario">Usuário:</label>
                    <input type="text" name="usuario" id="usuario" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" id="senha" required>
                </div>
                <div class="form-group">
                    <label for="role">Função:</label>
                    <select name="role" id="role" required>
                        <option value="user">Usuário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit" name="add_cliente" class="btn-submit">Adicionar Cliente</button>
            </form>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById('addClientModal').style.display = 'none';
        }
    </script>

    </div></div>

    <footer>
        <div class="copyright">&copy; 2024 Tática</div>
        <div class="footer-image"></div>
        </footer>


</body>
</html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
