<?php
session_start();


if (isset($_SESSION['id']) && isset($_SESSION['usuario']) && $_SESSION['role'] == 'admin') {

  setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil.1252');
  date_default_timezone_set('America/Sao_Paulo');

if (isset($_GET['id'])) {
    $contrato_id = $_GET['id'];


    $host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }


    $sql_contrato = "SELECT id, nome_contrato, descricao, status, data_criacao
                     FROM contratos
                     WHERE id = ?";


    $stmt = $conn->prepare($sql_contrato);
    $stmt->bind_param("i", $contrato_id);
    $stmt->execute();


    $result = $stmt->get_result();
    $contrato = $result->fetch_assoc();


    if ($contrato) {

        $nome_contrato = $contrato['nome_contrato'];
        $descricao = $contrato['descricao'];
        $status = $contrato['status'];
        $data_criacao = $contrato['data_criacao'];

        $sql = "SELECT DATE_FORMAT(data_criacao, '%d/%m/%Y %H:%i:%s') AS data_formatada FROM contratos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $contrato_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();



    } else {
        $mensagem = "Contrato não encontrado.";
    }


    $sql_checklist = "SELECT item, data, imagem1, imagem2, imagem3 FROM checklist WHERE contrato_id = ?";
    $stmt_checklist = $conn->prepare($sql_checklist);
    $stmt_checklist->bind_param("i", $contrato_id);
    $stmt_checklist->execute();
    $result_checklist = $stmt_checklist->get_result();


    $checklist = [];
    $imagens = [];

    if ($result_checklist->num_rows > 0) {
        while ($row = $result_checklist->fetch_assoc()) {
            $checklist[] = $row['item'];
            $imagens[] = [
    'imagem1' => $row['imagem1'],
    'imagem2' => $row['imagem2'],
    'imagem3' => $row['imagem3'],
    'data_conclusao' => $row['data']
];

        }
    }


    $stmt->close();
    $stmt_checklist->close();
    $conn->close();
} else {
    $mensagem = "ID do contrato não fornecido.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Contrato</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            padding: 0;
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 2.5em;
            font-weight: bold;
            color: #2c3e50;
            transition: color 0.3s ease;
        }

        h1:hover {
            color: #3498db;
        }

        .contrato-detalhes {
            margin: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .contrato-detalhes h2 {
            font-size: 2em;
            margin-bottom: 15px;
        }

        .contrato-detalhes p {
            font-size: 1.1em;
            margin: 10px 0;
        }

        .checklist ul {
            list-style: none;
            padding: 0;
        }

        .checklist li {
            display: flex;
            align-items: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f4f4f4;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
        }

        .checklist li i {
            font-size: 1.5em;
            margin-right: 15px;
            color: #16a085;
        }

        .checklist .item-text {
            flex: 1;
        }

        .checklist .item-data {
            font-size: 0.9em;
            color: #888;
        }

        .checklist .item-imagens {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
            justify-content: flex-start;
        }

        .checklist .item-imagens img {
            max-width: 120px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .checklist .item-imagens img:hover {
            opacity: 0.8;
            transform: scale(1.05);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        footer {
            text-align: center;
            font-size: 0.9em;
            margin-top: 40px;
            color: #555;
        }


        @media (max-width: 768px) {


            .checklist li {
                flex-direction: column;
                align-items: flex-start;
            }

            .checklist .item-imagens img {
                max-width: 100%;
                margin-bottom: 10px;
            }
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

}
    </style>
</head>
<body>

<header>
    <h1>Detalhes do Contrato: <?php echo $nome_contrato; ?></h1>
</header>

<nav id="menu">
    <div class="menu-hamburguer" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <ul>
        <li><a href="admin.php">Contratos</a></li>
        <li><a href="clientes.php">Clientes</a></li>
        <li><a href="logout.php">Sair</a></li>
    </ul>
</nav>

<main>
    <div class="contrato-detalhes">
        <h2>Descrição do Contrato</h2>
        <p><strong>Status:</strong> <?php echo $status; ?></p>
        <p><strong>Data de Criação:</strong> <?php echo $row['data_formatada']; ?></p>
        <p><?php echo $descricao; ?></p>

        <?php if (!empty($checklist)): ?>
            <div class="checklist">
                <h3>Checklist do Contrato</h3>
                <ul>
                    <?php foreach ($checklist as $index => $item): ?>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div class="item-text">


                                <strong><?php echo $item; ?></strong>
                                <p class="item-data"><strong>Data de Conclusão:</strong> <?php echo date('d/m/Y', strtotime($imagens[$index]['data_conclusao'])); ?></p>





                            </div>
                            <div class="item-imagens">
                                <?php
                                    foreach (['imagem1', 'imagem2', 'imagem3'] as $image_key) {
                                        if (!empty($imagens[$index][$image_key])) {
                                            echo "<img src='" . $imagens[$index][$image_key] . "' alt='Imagem do item'>";
                                        }
                                    }
                                ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <p><?php echo $mensagem ?? "Contrato não encontrado."; ?></p>
        <?php endif; ?>
    </div>
</main>

<footer>
    &copy; 2024 RealAssetInvest
</footer>

<script>
    function toggleMenu() {
        document.getElementById('menu').classList.toggle('open');
    }
</script>

</body>
</html>


<?php
} else {
    header("Location: index.php");
    exit();
}
?>
