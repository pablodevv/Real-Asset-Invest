<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['usuario'])) {

    $host = "mysql.site2.taticaweb.com.br";
    $user = "boss_site2";
    $password = "mXpMqNzpj7GYC8H";
    $dbname = "boss_site2";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    $tipo_empreendimento = $_GET['tipo_empreendimento'] ?? ''; 

    $sql_tipos_projeto = "SELECT * FROM tipos_projeto";
    $result_tipos_projeto = $conn->query($sql_tipos_projeto);
    $tipos_projeto = [];
    if ($result_tipos_projeto->num_rows > 0) {
        while ($row = $result_tipos_projeto->fetch_assoc()) {
            $tipos_projeto[] = $row;
        }
    }

    $sql_contratos = "SELECT c.*, c.imagem_capa FROM contratos c
                  JOIN contratos_clientes cc ON c.id = cc.contrato_id
                  WHERE cc.cliente_id = {$_SESSION['id']}";


    if (!empty($tipo_empreendimento)) {
        $sql_contratos .= " AND LOWER(c.tipo_empreendimento) = LOWER('" . $conn->real_escape_string($tipo_empreendimento) . "')";
    }

    $sql_contratos .= " ORDER BY c.data_criacao DESC";

    $result_contratos = $conn->query($sql_contratos);

    $contratos = [];
    if ($result_contratos->num_rows > 0) {
    while ($row = $result_contratos->fetch_assoc()) {

        if (empty($row['imagem_capa'])) {
            $row['imagem_capa'] = 'bg1.jpg';
        }
        $contratos[] = $row;
    }
}
 else {
        $mensagem = "Nenhum contrato encontrado.";
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contratos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>


        a {
            text-decoration: none;
            color: inherit;
        }


        .contratos {
            list-style: none;
            padding: 0;
            margin: 0 auto;
        }

        .contratos li {
      box-shadow: 0 2px rgba(0, 0, 0, 0.1);
      width: 260px;
      height: 280px;
      border-radius: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
      padding: 15px;
      border: 1px solid #ddd;
      background-color: #fff;
      transition: transform 0.2s;
      display: inline-block;
      margin: 50px;
      position: relative; 
      overflow: hidden; 
  }

  .contratos li::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 40%; 
      background: linear-gradient(to bottom, rgb(2 60 86), transparent);
      z-index: 1; 
      border-radius: 15px; 
  }

  .contratos li * { 
      position: relative; 
      z-index: 2; 
  }



        .contratos li:hover {
            transform: translateY(-2px);
        }

        .nome-contrato {
          font-size: 24px;
            font-weight: 600;
              color: white;
        }

        .status {
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
            color: white;
        }

        .ativo {
            background-color: #2ecc71;
        }

        .finalizado {
            background-color: #e74c3c;
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



select {
  width: 250px;
  padding: 10px;
  font-size: 16px;
  border: 2px solid #ccc;
  border-radius: 5px;
  background-color: #f9f9f9;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  cursor: pointer;
}


select::-ms-expand {
  display: none;
}

select::after {
  content: '▼';
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}



select:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
  outline: none;
}


option {
  padding: 10px;
  font-size: 16px;
  background-color: white;
  color: #333;
}


option:hover {
  background-color: #f1f1f1;
}


select:disabled {
  background-color: #e9ecef;
  cursor: not-allowed;
}


.tipocontrato{

margin-top: 20px;


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
padding: 50px 80px;
min-height: 570px;
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
            <h2>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>

            <form method="GET" action="contratos.php">
                <label for="tipo_empreendimento"><b>Tipo de Projeto:</b></label>
                <select name="tipo_empreendimento" id="tipo_empreendimento" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach ($tipos_projeto as $tipo): ?>
                        <option value="<?php echo $tipo['nome']; ?>" <?php echo $tipo_empreendimento == $tipo['nome'] ? 'selected' : ''; ?>>
                            <?php echo $tipo['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if (!empty($contratos)): ?>
                <ul class="contratos">
                    <?php foreach ($contratos as $contrato): ?>
                        <li style="background-image: url('<?php echo $contrato['imagem_capa']; ?>'); background-size: cover; background-position: center;">
                            <a href="detalhes_contrato.php?id=<?php echo $contrato['id']; ?>">
                                <span class="nome-contrato"><?php echo $contrato['nome_contrato']; ?></span>
                                <span class="status <?php echo strtolower($contrato['status']); ?>">
                                    <?php echo $contrato['status']; ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?php echo $mensagem ?? "Nenhum contrato encontrado."; ?></p>
            <?php endif; ?>

    </div>
</div>
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


