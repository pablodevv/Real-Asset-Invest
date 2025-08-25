<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

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

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao_documento_cliente'])) {
        $acao = $_POST['acao_documento_cliente'];
        $documento_cliente_id = $_POST['documento_cliente_id'] ?? null;

        if ($acao === 'aprovar' && $documento_cliente_id) {
            $stmt = $conn->prepare("UPDATE documentos_clientes SET status = 'Aprovado', motivo_reprovacao = NULL WHERE id = ?");
            $stmt->bind_param("i", $documento_cliente_id);
            $stmt->execute();
            $_SESSION['msg'] = "Documento aprovado com sucesso!";
        } elseif ($acao === 'reprovar' && $documento_cliente_id && isset($_POST['motivo_reprovacao'])) {
            $motivo_reprovacao = $_POST['motivo_reprovacao'];
            $stmt = $conn->prepare("UPDATE documentos_clientes SET status = 'Reprovado', motivo_reprovacao = ? WHERE id = ?");
            $stmt->bind_param("si", $motivo_reprovacao, $documento_cliente_id);
            $stmt->execute();
            $_SESSION['msg'] = "Documento reprovado com sucesso!";
        }
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $sql = "SELECT u.id AS cliente_id, u.nome AS nome_cliente,
                   dc.id AS documento_cliente_id, dc.nome_documento, dc.status, dc.motivo_reprovacao, dc.caminho_arquivo
            FROM users u
            LEFT JOIN documentos_clientes dc ON u.id = dc.cliente_id
            WHERE (u.nome LIKE ? OR dc.nome_documento LIKE ?)
            ORDER BY u.nome, dc.status";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[$row['cliente_id']]['nome_cliente'] = $row['nome_cliente'];
        if ($row['documento_cliente_id']) {
            $clientes[$row['cliente_id']]['documentos'][] = $row;
        }
    }


        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao_documento'])) {
            $acao = $_POST['acao_documento'];
            $documento_id = $_POST['documento_id'] ?? null;
            $nome_documento = $_POST['nome_documento'] ?? '';
            $cliente_id = $_POST['cliente_id'] ?? null;

            if ($acao == 'adicionar' && !empty($nome_documento)) {
                $stmt = $conn->prepare("INSERT INTO documentos_padrao (nome_documento) VALUES (?)");
                $stmt->bind_param("s", $nome_documento);
                $stmt->execute();
                $_SESSION['msg'] = "Documento pré-preenchido adicionado com sucesso!";
            } elseif ($acao == 'editar' && $documento_id && !empty($nome_documento)) {
                $stmt = $conn->prepare("UPDATE documentos_padrao SET nome_documento = ? WHERE id = ?");
                $stmt->bind_param("si", $nome_documento, $documento_id);
                $stmt->execute();
                $_SESSION['msg'] = "Documento pré-preenchido editado com sucesso!";
            } elseif ($acao == 'remover' && $documento_id) {
                $delete_clientes = $conn->prepare("DELETE FROM documentos_clientes WHERE documento_padrao_id = ?");
                $delete_clientes->bind_param("i", $documento_id);
                $delete_clientes->execute();

                $delete_documento = $conn->prepare("DELETE FROM documentos_padrao WHERE id = ?");
                $delete_documento->bind_param("i", $documento_id);
                $delete_documento->execute();
                $_SESSION['msg'] = "Documento pré-preenchido removido com sucesso!";
            } elseif ($acao == 'associar' && $documento_id && $cliente_id) {
    $stmt = $conn->prepare("SELECT nome_documento FROM documentos_padrao WHERE id = ?");
    $stmt->bind_param("i", $documento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doc_row = $result->fetch_assoc();
    $nome_documento = $doc_row['nome_documento'];

    $stmt = $conn->prepare("INSERT INTO documentos_clientes (cliente_id, documento_padrao_id, nome_documento, status) VALUES (?, ?, ?, 'pendente')");
    $stmt->bind_param("iis", $cliente_id, $documento_id, $nome_documento);
    $stmt->execute();

    $_SESSION['msg'] = "Documento associado e solicitado com sucesso!";
} elseif ($acao == 'remover_associacao' && $documento_id && $cliente_id) {
                $stmt = $conn->prepare("DELETE FROM documentos_clientes WHERE cliente_id = ? AND documento_padrao_id = ?");
                $stmt->bind_param("ii", $cliente_id, $documento_id);
                $stmt->execute();
                $_SESSION['msg'] = "Associação removida com sucesso!";
            }
        }

        $documentos_pre = [];
        $result = $conn->query("SELECT * FROM documentos_padrao ORDER BY nome_documento");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $documentos_pre[] = $row;
            }
        }


    $conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gerenciamento de Documentos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>

        .cliente-container {
          margin-bottom: 15px;
  padding: 15px 15px 0px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
        }
        .cliente-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .cliente-header button {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .cliente-header button:hover {
            background-color: #2980b9;
        }
        .documentos {
            display: none;
            margin-top: 10px;
        }
        .documentos.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            padding: 10px;

        }
        table th {
            background-color: #1abc9c;
            color: white;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .aprovado {
            background-color: #2ecc71;
            color: white;
        }
        .reprovado {
            background-color: #e74c3c;
            color: white;
        }
        .pendente {
            background-color: #f39c12;
            color: white;
        }



        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        textarea {
            font-family: 'Arial', sans-serif;
            font-size: 1em;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        .modal-content button {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .modal-content button:hover {
            background-color: #2980b9;
        }


        @media (max-width: 600px) {
            .modal-content {
                width: 90%;
            }
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

        .box2 {
            background: #ebeef5;
            padding: 5px;
            border-radius: 10px;
        }

        @media (max-width: 768px) {

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


        select[name="cliente_id"] {
            width: 30%;
            padding: 10px 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }


        select[name="cliente_id"]:focus {
            border-color: #4CAF50;
            outline: none;
        }


        select[name="cliente_id"] option:first-child {
            color: #888;
        }


        select[name="cliente_id"] option {
            padding: 10px;
            background-color: #fff;
            color: #333;
        }



        @media (max-width: 768px) {
            select[name="cliente_id"] {
                font-size: 14px;
                padding: 8px 12px;
            }
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
margin-bottom: 25px;
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
            margin-bottom: 20px; 
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


    </style>
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


            <h2>Documentos Pré-Preenchidos</h2>
      <?php if (isset($_SESSION['msg'])): ?>
          <h3 class="alert" style="color: #00b76b;">
              <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
          </h3>
      <?php endif; ?>
            <form method="POST" action="admin_documentos.php">
                <input type="hidden" name="acao_documento" value="adicionar">
                <input type="text" name="nome_documento" placeholder="Novo Documento" required>

                <button type="submit" style="background-color: #0974a3;">Adicionar Documento</button>
            </form>

            <ul>
    <?php foreach ($documentos_pre as $doc_pre): ?>
        <li style="margin-top: 40px;">
            <form method="POST" action="admin_documentos.php" style="display: inline; width: 100%;">
                <input type="hidden" name="acao_documento" value="editar">
                <input type="hidden" name="documento_id" value="<?php echo $doc_pre['id']; ?>">

                <input type="text" name="nome_documento" value="<?php echo htmlspecialchars($doc_pre['nome_documento']); ?>" required style="width: 75%; display: inline-block;">

                <button type="submit" style="background-color: #0974a3; display: inline-block;">Editar</button>
</form>

                <form method="POST" action="admin_documentos.php" style="display: inline-block;">
                    <input type="hidden" name="acao_documento" value="remover">
                    <input type="hidden" name="documento_id" value="<?php echo $doc_pre['id']; ?>">
                    <button type="submit" style="background-color: #cd4739;">Remover</button>
                </form>


            <form method="POST" action="admin_documentos.php" style="display: inline; width: 100%; margin-top: 10px;">
                <input type="hidden" name="acao_documento" value="associar">
                <input type="hidden" name="documento_id" value="<?php echo $doc_pre['id']; ?>">

                <select name="cliente_id" required style="width: 75%; display: inline-block;">
                    <option value="">Selecionar Cliente</option>
                    <?php foreach ($clientes as $cliente_id => $cliente): ?>
                        <option value="<?php echo $cliente_id; ?>"><?php echo htmlspecialchars($cliente['nome_cliente']); ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" style="background-color: #00C976; display: inline-block;">Associar</button>
            </form>
        </li>

    <?php endforeach; ?>
</ul>

            <br>

            <style>

            form {
                margin-bottom: 20px;
            }

            form input[type="text"],
            form select {
                width: 100%;
                max-width: 75%; 
                padding: 10px;
                font-size: 16px;
                margin-bottom: 10px; 
                border: 1px solid #ddd;
                border-radius: 5px;
                box-sizing: border-box;
            }

            form button {
                padding: 10px 20px;
                font-size: 16px;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            form button:hover {
                opacity: 0.9;
            }

            form button[style*="background-color: #0974a3;"] {
                background-color: #0974a3;
            }

            form button[style*="background-color: #cd4739;"] {
                background-color: #cd4739;
            }

            form button[style*="background-color: #00C976;"] {
                background-color: #00C976;
            }

            @media (max-width: 768px) {
                form input[type="text"],
                form select,
                form button {
                    width: 100%; 
                    max-width: none;
                    margin-bottom: 10px; 
                }


            }



            ul li form {
                display: flex;
                flex-wrap: wrap;
                gap: 10px; 
                align-items: center;
            }


            </style>

            <h2>Documentos Enviados</h2>
<form method="GET" action="admin_documentos.php">
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar por cliente ou documento" />
    <button type="submit" style="background-color: #0974a3;">Buscar</button>
</form>


<?php if (empty($clientes)): ?>
    <p>Nenhum cliente com documentos enviados encontrado.</p>
<?php else: ?>
  <?php foreach ($clientes as $cliente_id => $cliente): ?>
    <div class="cliente-container">
        <div class="cliente-header">
            <span><strong>Cliente:</strong> <?php echo htmlspecialchars($cliente['nome_cliente']); ?></span>
            <button class="toggle-documentos" style="background-color: #0974a3;">Exibir Documentos</button>
        </div>
        <div class="documentos">
            <?php if (!empty($cliente['documentos'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cliente['documentos'] as $doc): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo $doc['caminho_arquivo']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($doc['nome_documento']); ?>
                                    </a>
                                </td>
                                <td class="<?php echo $doc['status']; ?>"><?php echo htmlspecialchars($doc['status']); ?></td>
                                <td>
                                    <?php if ($doc['status'] == 'Aguardando Aprovação'): ?>
                                      <form method="POST" action="admin_documentos.php" style="display: inline;">
      <input type="hidden" name="documento_cliente_id" value="<?php echo $doc['documento_cliente_id']; ?>"> 
      <input type="hidden" name="acao_documento_cliente" value="aprovar">
      <button type="submit" style="background-color: #00C976;">Aprovar</button>
  </form>


  <form method="POST" action="admin_documentos.php" style="display: inline;">
      <input type="hidden" name="acao_documento_cliente" value="reprovar">
      <input type="hidden" name="documento_cliente_id" value="<?php echo $doc['documento_cliente_id']; ?>"> 

      <button type="submit" style="background-color: #cd4739;">Reprovar</button>
      <textarea name="motivo_reprovacao" placeholder="Motivo da reprovação" required></textarea>
  </form>



<?php elseif ($doc['status'] == 'Reprovado'): ?>
                                        <span>Motivo: <?php echo htmlspecialchars($doc['motivo_reprovacao']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Não há documentos enviados.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

<?php endif; ?>

        </main>
    </div>
</div>


<footer>
    <div class="copyright">&copy; 2024 Tática</div>
    <div class="footer-image"></div>
    </footer>

<script>
    document.querySelectorAll('.toggle-documentos').forEach(button => {
        button.addEventListener('click', function() {
            const documentosDiv = this.closest('.cliente-container').querySelector('.documentos');
            documentosDiv.classList.toggle('active');
        });
    });
</script>

</body>
</html>

<?php
} else {
    header("Location: login.php");
    exit();
}

