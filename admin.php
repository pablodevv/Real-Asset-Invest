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

    // Inicializando a variável de pesquisa
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Ajustando a consulta SQL com base na pesquisa
    $sql = "
    SELECT contratos.*,
           (SELECT COUNT(*) FROM checklist WHERE contrato_id = contratos.id AND data IS NOT NULL) AS concluido,
           (SELECT COUNT(*) FROM checklist WHERE contrato_id = contratos.id) AS total_itens
    FROM contratos
    WHERE nome_contrato LIKE ?
    ORDER BY
        CASE
            WHEN status = 'ativo' THEN 1
            WHEN status = 'finalizado' THEN 2
            WHEN status = 'inativo' THEN 3
            ELSE 4
        END";



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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard Executivo Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

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
padding: 4px 30px;
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
padding-top: 10px;
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
font-size: 0.9em;
}

.status.ativo {
background-color: #00c076;
color: white;
}

.status.inativo {
background-color: #b1b1b1;
color: white;
padding: 8px 10px;
}

.status.finalizado {
background-color: #dc3545;
color: white;
padding: 8px 10px;
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
    padding: 20px;
}

header {
    padding: 20px;
    flex-direction: column;
    align-items: center;
}


.acao-btn2{

margin-top: 3px;

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
            <h2>Gerenciar Projetos</h2>


            <div class="search-container">
              <form method="get" action="">
                <input type="text" placeholder="Pesquisar projetos..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Buscar</button>
              </form>
                <a href="novo_contrato.php" class="novo-btn">+ Novo</a>

            </div>




            <table>
                <thead>
                    <tr>
                        <th>Nome do Projeto</th>
                        <th>Status</th>
                        <th>Progresso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
    <?php if (count($contratos) > 0) : ?>
        <?php foreach ($contratos as $contrato) : ?>
            <?php
                $progresso = ($contrato['total_itens'] > 0) ? round(($contrato['concluido'] / $contrato['total_itens']) * 100) : 0;
            ?>
            <tr>
                <td><?php echo $contrato['nome_contrato']; ?></td>
                <td><span class="status <?php echo $contrato['status']; ?>"><?php echo ucfirst($contrato['status']); ?></span></td>
                <td><div class="progress" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $progresso; ?>%;" aria-valuenow="<?php echo $progresso; ?>" aria-valuemin="0" aria-valuemax="100">
                        <?php echo $progresso . '%'; ?>
                    </div>
                </div></td>
                <td>
                    <button class="acao-btn" onclick="window.location.href='editar_contrato.php?id=<?php echo $contrato['id']; ?>'" style="background-color: #0974a3;">Editar</button>
                    <button class="acao-btn2" onclick="window.location.href='excluir_contrato.php?id=<?php echo $contrato['id']; ?>'">Excluir</button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="4">Nenhum contrato encontrado.</td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>
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
