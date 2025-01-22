<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['id']) && isset($_SESSION['usuario']) && $_SESSION['role'] == 'admin') {

    $host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    if (!isset($_GET['tipo_projeto_id'])) {
        echo "Tipo de projeto não especificado.";
        exit();
    }

    $tipo_id = intval($_GET['tipo_projeto_id']);

    $tipo_query = $conn->prepare("SELECT nome FROM tipos_projeto WHERE id = ?");
    $tipo_query->bind_param("i", $tipo_id);
    $tipo_query->execute();
    $tipo_result = $tipo_query->get_result();

    if ($tipo_result->num_rows === 0) {
        echo "Tipo de projeto não encontrado.";
        exit();
    }

    $tipo = $tipo_result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['novo_item'])) {
            $novo_item = trim($_POST['novo_item']);

            if (!empty($novo_item)) {
                $add_item_query = $conn->prepare("INSERT INTO checklist_preset (tipo_id, item) VALUES (?, ?)");
                $add_item_query->bind_param("is", $tipo_id, $novo_item);

                if ($add_item_query->execute()) {
                    $mensagem = "Item adicionado com sucesso!";
                } else {
                    $mensagem = "Erro ao adicionar o item.";
                }
            } else {
                $mensagem = "O item não pode estar vazio.";
            }
        } elseif (isset($_POST['edit_item_id']) && isset($_POST['edit_item_text'])) {
            $edit_item_id = intval($_POST['edit_item_id']);
            $edit_item_text = trim($_POST['edit_item_text']);

            if (!empty($edit_item_text)) {
                $edit_item_query = $conn->prepare("UPDATE checklist_preset SET item = ? WHERE id = ?");
                $edit_item_query->bind_param("si", $edit_item_text, $edit_item_id);

                if ($edit_item_query->execute()) {
                    $mensagem = "Item editado com sucesso!";
                } else {
                    $mensagem = "Erro ao editar o item.";
                }
            } else {
                $mensagem = "O item não pode estar vazio.";
            }
        }
    }

    if (isset($_GET['delete_item_id'])) {
        $item_id = intval($_GET['delete_item_id']);

        $delete_item_query = $conn->prepare("DELETE FROM checklist_preset WHERE id = ?");
        $delete_item_query->bind_param("i", $item_id);

        if ($delete_item_query->execute()) {
            $mensagem = "Item deletado com sucesso!";
        } else {
            $mensagem = "Erro ao deletar o item.";
        }

        header("Location: " . $_SERVER['PHP_SELF'] . "?tipo_projeto_id=" . $tipo_id);
        exit();
    }

    $checklist_query = $conn->prepare("SELECT id, item FROM checklist_preset WHERE tipo_id = ?");
    $checklist_query->bind_param("i", $tipo_id);
    $checklist_query->execute();
    $checklist_result = $checklist_query->get_result();
    $checklist_itens = $checklist_result->fetch_all(MYSQLI_ASSOC);

    $conn->close();

} else {
    header("Location: index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Preencher Checklist - <?php echo htmlspecialchars($tipo['nome']); ?></title>
    <style>

        .mensagem {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .mensagem.success {
            background-color: #d4edda;
            color: #155724;
        }
        .mensagem.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .box ul {
            list-style: none;
            padding: 0;
        }
        .box ul li {
            background: #fff;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding-top: 0;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #00c076;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background-color: #27ae60;
        }
        .voltar {
            margin-top: 20px;
            display: inline-block;
            color: #007bff;
            text-decoration: none;
        }
        .voltar:hover {
            text-decoration: underline;
        }






















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


 .acao-btn2 {
     background-color: #dc3545;
     color: white;
 }

 .acao-btn2:hover {
     background-color: #a71d2a;
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
padding: 50px 80px;
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
background-color: #ffffff;
color: #7f8c8d;
padding: 40px 60px;
text-align: center;
border-top: 2px solid #f1f1f1;
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
    padding: 20px;
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

    <h1>Checklist para: <?php echo htmlspecialchars($tipo['nome']); ?></h1>

    <?php if (isset($mensagem)) : ?>
        <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

    <h2>Itens do Checklist</h2>
<ul>
    <?php if (count($checklist_itens) > 0): ?>
        <?php foreach ($checklist_itens as $item): ?>
            <li>
              <br>
                <?php echo htmlspecialchars($item['item']); ?>

                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
    <!-- Campo de Texto e Botão Salvar -->
    <form class="edit-form" method="POST" style="display: flex; align-items: center; gap: 5px;">
        <input type="hidden" name="edit_item_id" value="<?php echo $item['id']; ?>">
        <input type="text" name="edit_item_text" value="<?php echo htmlspecialchars($item['item']); ?>">
        <button type="submit" class="acao-btn">Salvar</button>
    </form>

    <!-- Botão Excluir -->
    <form method="POST" style="display: inline;">
        <input type="hidden" name="delete_item" value="<?php echo $item['id']; ?>">
        <a href="?tipo_projeto_id=<?php echo $tipo_id; ?>&delete_item_id=<?php echo $item['id']; ?>" class="acao-btn2">
            <i class="fa-solid fa-delete-left"></i>
        </a>
    </form>
</div>

            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>Nenhum item encontrado.</li>
    <?php endif; ?>
</ul>
<style>

@media (max-width: 768px) {
    .acao-btn, .acao-btn2 {
        padding: 6px 10px; /* Reduz o tamanho dos botões */
        font-size: 12px;   /* Diminui a fonte */
    }
}

/* Responsividade para telas muito pequenas (máximo 480px) */
@media (max-width: 480px) {
    /* Botões ficam empilhados verticalmente */
    div {
        flex-direction: column;
        gap: 0px;
    }

    .acao-btn, .acao-btn2 {
        width: 100%; /* Botões ocupam toda a largura */
        text-align: center;
    }

    input[type="text"] {
        width: 100%; /* Campo de texto ocupa toda a largura */
    }
}

</style>

    <form method="POST" action="">
        <label for="novo_item">Adicionar novo item ao checklist:</label>
        <input type="text" id="novo_item" name="novo_item" placeholder="Digite o nome do item">
        <button type="submit">Adicionar</button>
    </form>

    <a href="tipo_projeto.php" class="voltar">&larr; Voltar para Tipos de Projeto</a>


  </div>
  </div>




  <footer>
      <div class="copyright">&copy; 2024 Tática</div>
      <div class="footer-image"></div>
      </footer>
</body>
</html>
