<?php
session_start();

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
        $data_formatada = (new DateTime($data_criacao))->format('d/m/Y H:i:s');
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


$total_itens = count($checklist);
$itens_concluidos = 0;

foreach ($imagens as $imagem) {
    if (!empty($imagem['data_conclusao'])) {
        $itens_concluidos++;
    }
}

$percentual_conclusao = ($total_itens > 0) ? round((100 / $total_itens) * $itens_concluidos, 2) : 0;



?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Contrato</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>

    ul{

      margin-bottom: 0 !important;

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



.fas.fa-check-circle {
    font-size: 1.5em;
    margin-right: 8px;
    vertical-align: middle;
}

.fas.fa-check-circle.gray {
    color: gray;
}

.fas.fa-check-circle.green {
    color: green;
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



.progress{

margin: 35px !important;
margin-top: 10px !important;
margin-left: 0px !important;
height: 45px !important;
width: 100% !important;
border-radius: 6px !important;
margin-top: -20px;


}


.progress-bar {
    background-color: #148d79;
    height: 100%;
    color: white;
    text-align: center;
    line-height: 45px;
    font-weight: bold;
    transition: width 0.4s ease-in-out;
}


.progresso{


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



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

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
text-decoration: none;
}

.container {
padding: 50px 80px;
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
    gap: 10px;
    margin-left: -20px;
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
      <h1>Área do Cliente</h1>
  </header>

  <nav>
      <ul>
          <li><a href="contratos.php"><i class="bi bi-house-door"></i> Projetos</a></li>
          <li><a href="home.php"><i class="bi bi-bar-chart"></i> Documentos</a></li>
  <li><a href="logout.php"><i class="bi bi-gear"></i> Sair</a></li>
      </ul>
  </nav>

  <div class="container">
      <div class="box">
    <div class="contrato-detalhes">

      <div class="progresso">
        <h2>Progresso Até Agora:</h2>
    </div>
    <div class="progress">
    <div class="progress-bar" role="progressbar" style="width: <?= $percentual_conclusao; ?>%;" aria-valuenow="<?= $percentual_conclusao; ?>" aria-valuemin="0" aria-valuemax="100">
        <?= $percentual_conclusao; ?>%
    </div>
</div>




        <h2>Descrição do Projeto</h2>
        <p><strong>Status:</strong> <?php echo $status; ?></p>
        <p><strong>Data de Criação:</strong> <?php echo $data_formatada; ?></p>
        <p><?php echo $descricao; ?></p>

        <?php if (!empty($checklist)): ?>
      <div class="checklist">
          <h3>Checklist do Projeto</h3>
          <ul>
              <?php foreach ($checklist as $index => $item) : ?>
                  <li>
                      <i class="fas fa-check-circle <?php echo empty($imagens[$index]['data_conclusao']) ? 'gray' : 'green'; ?>"></i>
                      <span class="item-text"><?php echo $item; ?></span>
                      <div class="item-data">
                          <?php if (empty($imagens[$index]['data_conclusao'])) : ?>
                              <span>Não concluído</span>
                          <?php else : ?>
                              <span>Concluído em: <?php echo date('d/m/Y', strtotime($imagens[$index]['data_conclusao'])); ?></span>
                          <?php endif; ?>
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

</div>
  </div>
  <footer>
      <div class="copyright">&copy; 2024 Tática</div>
      <div class="footer-image"></div>
      </footer>


<script>
    function toggleMenu() {
        document.getElementById('menu').classList.toggle('open');
    }
</script>

</body>
</html>

