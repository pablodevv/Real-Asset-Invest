<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$host = "mysql.site2.taticaweb.com.br";
$user = "boss_site2";
$password = "mXpMqNzpj7GYC8H";
$dbname = "boss_site2";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id_contrato = intval($_GET['id']);

    $sql = "SELECT * FROM contratos WHERE id = ?";
    $stmt_contrato = $conn->prepare($sql);
    $stmt_contrato->bind_param("i", $id_contrato);
    $stmt_contrato->execute();
    $result = $stmt_contrato->get_result();
    if ($result->num_rows > 0) {
        $contrato = $result->fetch_assoc();
    } else {
        echo "Contrato não encontrado!";
        exit();
    }

    $sql_checklist = "SELECT * FROM checklist WHERE contrato_id = ?";
    $stmt_checklist = $conn->prepare($sql_checklist);
    $stmt_checklist->bind_param("i", $id_contrato);
    $stmt_checklist->execute();
    $result_checklist = $stmt_checklist->get_result();

    $checklist = [];
    while ($row = $result_checklist->fetch_assoc()) {
        $checklist[] = $row;
    }

    $clientes_sql = "SELECT c.id, c.nome FROM users c
                     INNER JOIN contratos_clientes cc ON c.id = cc.cliente_id
                     WHERE cc.contrato_id = ?";
    $stmt_clientes = $conn->prepare($clientes_sql);
    $stmt_clientes->bind_param("i", $id_contrato);
    $stmt_clientes->execute();
    $clientes_result = $stmt_clientes->get_result();

    $clientes_vinculados = [];
    while ($row = $clientes_result->fetch_assoc()) {
        $clientes_vinculados[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_contrato = $_POST['nome_contrato'];
    $descricao = $_POST['descricao'];
    $status = $_POST['status'];

    $tipo_projeto = $_POST['tipo_projeto'];

    $sql = "UPDATE contratos SET nome_contrato = ?, descricao = ?, status = ?, tipo_empreendimento = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql);
    $stmt_update->bind_param("ssssi", $nome_contrato, $descricao, $status, $tipo_projeto, $id_contrato);

    if ($stmt_update->execute()) {
        if (isset($_POST['checklist']) && is_array($_POST['checklist'])) {
            foreach ($_POST['checklist'] as $item_id => $item) {
                $item_text = $item['item'];
                $data_conclusao = $item['data_conclusao'];

                if (empty($data_conclusao) || $data_conclusao == '0000-00-00') {
                   $data_conclusao = $item['data'];  // Mantém a data anterior se estiver vazia ou inválida
               }

                $sql_update_item = "UPDATE checklist SET item = ?, data = ? WHERE id = ?";
                $stmt_update_item = $conn->prepare($sql_update_item);
                $stmt_update_item->bind_param("ssi", $item_text, $data_conclusao, $item_id);
                $stmt_update_item->execute();

                for ($i = 1; $i <= 3; $i++) {
                    if (!empty($_FILES['checklist']['name'][$item_id]["imagem$i"])) {
                        $image_name = basename($_FILES['checklist']['name'][$item_id]["imagem$i"]);
                        $image_tmp = $_FILES['checklist']['tmp_name'][$item_id]["imagem$i"];
                        $upload_dir = "uploads/";
                        $file_path = $upload_dir . uniqid() . "_" . $image_name;

                        if (move_uploaded_file($image_tmp, $file_path)) {
                            $sql_update_image = "UPDATE checklist SET imagem$i = ? WHERE id = ?";
                            $stmt_update_image = $conn->prepare($sql_update_image);
                            $stmt_update_image->bind_param("si", $file_path, $item_id);
                            $stmt_update_image->execute();
                        }
                    }
                }
            }
        }

        if (isset($_POST['novos_clientes']) && is_array($_POST['novos_clientes'])) {
            foreach ($_POST['novos_clientes'] as $cliente_id) {
                $sql_inserir_cliente = "INSERT INTO contratos_clientes (contrato_id, cliente_id) VALUES (?, ?)";
                $stmt_inserir_cliente = $conn->prepare($sql_inserir_cliente);
                $stmt_inserir_cliente->bind_param("ii", $id_contrato, $cliente_id);
                $stmt_inserir_cliente->execute();
            }
        }


        if (!empty($_FILES['imagem_capa']['name'])) {
    $image_name = basename($_FILES['imagem_capa']['name']);
    $image_tmp = $_FILES['imagem_capa']['tmp_name'];
    $upload_dir = "uploads/capas/";
    $file_path = $upload_dir . uniqid() . "_" . $image_name;

    // Cria o diretório se não existir
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($image_tmp, $file_path)) {
        // Atualiza o caminho da imagem no banco de dados
        $sql_update_capa = "UPDATE contratos SET imagem_capa = ? WHERE id = ?";
        $stmt_update_capa = $conn->prepare($sql_update_capa);
        $stmt_update_capa->bind_param("si", $file_path, $id_contrato);
        $stmt_update_capa->execute();
    }
}


        if (isset($_POST['novos_itens_checklist']) && is_array($_POST['novos_itens_checklist'])) {
            foreach ($_POST['novos_itens_checklist'] as $index => $novo_item) {
                $data_conclusao = $_POST['novas_datas_checklist'][$index];

                $sql_insert_item = "INSERT INTO checklist (contrato_id, item, data) VALUES (?, ?, ?)";
                $stmt_insert_item = $conn->prepare($sql_insert_item);
                $stmt_insert_item->bind_param("iss", $id_contrato, $novo_item, $data_conclusao);
                $stmt_insert_item->execute();

                $novo_item_id = $stmt_insert_item->insert_id;

                if (isset($_FILES['novas_imagens_checklist']['name'][$index])) {
                    for ($i = 0; $i < 3; $i++) {
                        if (!empty($_FILES['novas_imagens_checklist']['name'][$index][$i])) {
                            $image_name = basename($_FILES['novas_imagens_checklist']['name'][$index][$i]);
                            $image_tmp = $_FILES['novas_imagens_checklist']['tmp_name'][$index][$i];
                            $upload_dir = "uploads/";
                            $file_path = $upload_dir . uniqid() . "_" . $image_name;

                            if (move_uploaded_file($image_tmp, $file_path)) {
                                $sql_insert_image = "UPDATE checklist SET imagem" . ($i + 1) . " = ? WHERE id = ?";
                                $stmt_insert_image = $conn->prepare($sql_insert_image);
                                $stmt_insert_image->bind_param("si", $file_path, $novo_item_id);
                                $stmt_insert_image->execute();
                            }
                        }
                    }
                }
            }
        }

        header("Location: admin.php");
        exit();
    } else {
        echo "Erro ao atualizar o contrato.";
    }
}
?>






<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contrato</title>
    <style>
    img {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px;
        max-width: 100%;
        height: auto;
    }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;

            color: #333;
            padding: 0;
            background: url('boss/img/background/bg-7.png') no-repeat center center fixed;
            background-size: cover;
            margin-top: 0;
            margin-bottom: 0;
        }

        nav {
            background-color: #34495e;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 20px;
        }

        nav ul li a {
            color: white;
            font-size: 1.2em;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: #1abc9c;
        }


        .container {
            display: flex;
            justify-content: center;
            padding: 40px;
        }



        .form-container {
    width: 100%;
    max-width: 650px;
    padding: 25px 50px 50px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px 3px rgba(0, 0, 0, 0.3);
}




        h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            text-align: center;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 1.1em;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #34495e;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            color: #333;
            background-color: #f9f9f9;
            margin-top: 5px;
            font-family: 'Arial', sans-serif;
        }













        textarea {
            resize: vertical;
            height: 120px;
        }

        button[type="submit"] {
            background-color: #1abc9c;
            color: white;
            border: none;
            padding: 15px 20px;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #16a085;
            transform: translateY(-2px);
        }

        .checklist-item {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
        }

        .checklist-item img {
            width: 100px;
            height: auto;
            margin-right: 10px;
        }

        .checklist-item input[type="text"] {
            margin-bottom: 10px;
        }

        a.btn-back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #34495e;
            font-size: 1.1em;
            font-weight: bold;
            padding: 12px;
            background-color: #ecf0f1;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        a.btn-back:hover {
            background-color: #bdc3c7;
        }

        #adicionar-item {
            background-color: #2980b9;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px;
            display: inline-block;
        }

        #adicionar-item:hover {
            background-color: #3498db;
        }





        .remover-item {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 10px 20px;
  font-size: 1em;
  font-weight: bold;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.3s ease;
  margin-top: 10px;
  display: inline-block;
}

.remover-item:hover {
  background-color: #c0392b;
  transform: translateY(-2px);
}

.remover-item:focus {
  outline: none;
}

input[type="file"] {
    appearance: none; /* Remove o estilo padrão */
    color: Black; /* Cor do texto */
    border: none; /* Remove bordas */
    border-radius: 5px; /* Bordas arredondadas */
    padding: 10px 20px; /* Espaçamento interno */
    cursor: pointer; /* Cursor de "mãozinha" */
    font-size: 14px; /* Tamanho da fonte */
}

input[type="file"]::file-selector-button {
    background-color: #0974a3; /* Cor de fundo do botão */
    color: white; /* Cor do texto do botão */
    border: none; /* Remove bordas do botão */
    border-radius: 5px; /* Bordas arredondadas */
    padding: 10px 20px; /* Espaçamento interno */
    cursor: pointer; /* Cursor de "mãozinha" */
    font-size: 14px; /* Tamanho da fonte */
}

input[type="file"]::file-selector-button:hover {
    background-color: #0056b3; /* Azul mais escuro ao passar o mouse */
}

    </style>
</head>
<body>


    <div class="container">
        <div class="form-container">
            <h2>Editar Projeto</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome_contrato">Nome do Projeto:</label>
                    <input type="text" name="nome_contrato" id="nome_contrato" value="<?php echo $contrato['nome_contrato']; ?>" required>
                </div>




                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea name="descricao" id="descricao" required><?php echo $contrato['descricao']; ?></textarea>
                </div>


                <div class="form-group">
    <label for="imagem_capa">Imagem de Capa:</label>
    <input type="file" name="imagem_capa" id="imagem_capa">
    <?php if (!empty($contrato['imagem_capa'])): ?>
        <img src="<?php echo $contrato['imagem_capa']; ?>" alt="Imagem de Capa" width="150">
    <?php endif; ?>
</div>



                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status" required>
                        <option value="ativo" <?php echo ($contrato['status'] == 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                        <option value="finalizado" <?php echo ($contrato['status'] == 'finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                        <option value="inativo" <?php echo ($contrato['status'] == 'inativo') ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>

                <!-- Adicionar Novos Clientes -->
  <div class="form-group">
      <h3>Adicionar Novos Clientes</h3>
      <div id="clientes-container">
          <div class="cliente-adicionar">
              <select name="novos_clientes[]" class="cliente-select">
                  <option value="" disabled selected>Selecione um cliente</option>
                  <?php
                  $clientes_sql = "SELECT id, nome FROM users";
                  $clientes_result = $conn->query($clientes_sql);
                  while ($cliente = $clientes_result->fetch_assoc()) {
                      echo '<option value="' . $cliente['id'] . '">' . $cliente['nome'] . '</option>';
                  }
                  ?>
              </select>
          </div>
      </div>

      <button type="button" id="adicionar-cliente" style="margin-top: 10px; background-color: #2980b9; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
          Adicionar Cliente
      </button>
  </div>

  <!-- Lista de Clientes Vinculados -->
  <div class="form-group">
      <label for="clientes_vinculados">Clientes Vinculados:</label>
      <ul id="clientes-vinculados-list">
          <?php foreach ($clientes_vinculados as $cliente): ?>
              <li data-cliente-id="<?php echo $cliente['id']; ?>"><?php echo $cliente['nome']; ?></li>
          <?php endforeach; ?>
      </ul>
  </div>


<script>
document.getElementById('adicionar-cliente').addEventListener('click', function () {
// Cria uma mensagem de sucesso
let successMessage = document.createElement('div');
successMessage.textContent = 'Cliente adicionado com sucesso!';
successMessage.style.cssText = `
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    border-radius: 5px;
    position: fixed;
    top: 20px;
    right: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    z-index: 1000;
`;

// Adiciona a mensagem ao corpo da página
document.body.appendChild(successMessage);

// Remove a mensagem após 3 segundos
setTimeout(function () {
    successMessage.remove();
}, 3000);
});




</script>



                <div class="form-group">
                    <h2>Itens do Checklist:</h2>
                    <div id="checklist-container">
                        <?php foreach ($checklist as $item): ?>
                            <div class="checklist-item">
                                <label>Item:</label>
                                <input type="text" name="checklist[<?php echo $item['id']; ?>][item]" value="<?php echo $item['item']; ?>" required>

                                <label>Data de Conclusão:</label>
                                <input type="date" name="checklist[<?php echo $item['id']; ?>][data_conclusao]" value="<?php echo $item['data']; ?>" style="font-family: 'Arial', sans-serif;">


                                <div class="form-group">
                                    <label>Imagem 1:</label>
                                    <input type="file" name="checklist[<?php echo $item['id']; ?>][imagem1]">
                                    <?php if ($item['imagem1']): ?>
                                        <img src="<?php echo $item['imagem1']; ?>" alt="Imagem 1" width="100">
                                    <?php endif; ?>

                                    <label>Imagem 2:</label>
                                    <input type="file" name="checklist[<?php echo $item['id']; ?>][imagem2]">
                                    <?php if ($item['imagem2']): ?>
                                        <img src="<?php echo $item['imagem2']; ?>" alt="Imagem 2" width="100">
                                    <?php endif; ?>

                                    <label>Imagem 3:</label>
                                    <input type="file" name="checklist[<?php echo $item['id']; ?>][imagem3]">
                                    <?php if ($item['imagem3']): ?>
                                        <img src="<?php echo $item['imagem3']; ?>" alt="Imagem 3" width="100">
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="remover-item" data-id="<?php echo $item['id']; ?>">Remover</button>

                            </div>
                        <?php endforeach; ?>
                    </div>


                    <script>

                    document.querySelectorAll('.remover-item').forEach(button => {
  button.addEventListener('click', function() {
      const itemId = this.getAttribute('data-id');

      // Remover o item da interface
      this.closest('.checklist-item').remove();

      // Remover o item do banco de dados (envio via AJAX ou formulário)
      const formData = new FormData();
      formData.append('remover_item', itemId);

      fetch('remover_item.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.text())
      .then(data => {
          console.log(data); // Mensagem de sucesso ou erro
      });
  });
});



                    </script>


                    <div id="novo-item-checklist"></div>


                    <button type="button" id="adicionar-item">Adicionar Item</button>
                </div>






                <button type="submit" class="btn-submit">Salvar Alterações</button>
            </form>
            <a href="admin.php" class="btn-back">Voltar para a lista de projetos</a>
        </div>
    </div>

    <script>
    document.getElementById('adicionar-item').addEventListener('click', function() {
    let novoIndex = document.querySelectorAll('#novo-item-checklist .checklist-item').length;

    let novoItemHtml = `
    <div class="checklist-item">
        <label>Novo Item:</label>
        <input type="text" name="novos_itens_checklist[]">

        <label>Data de Conclusão:</label>
        <input type="date" name="novas_datas_checklist[]">

        <div class="form-group">
            <label>Imagem 1:</label>
            <input type="file" name="novas_imagens_checklist[${novoIndex}][0]">

            <label>Imagem 2:</label>
            <input type="file" name="novas_imagens_checklist[${novoIndex}][1]">

            <label>Imagem 3:</label>
            <input type="file" name="novas_imagens_checklist[${novoIndex}][2]">
        </div>
    </div>`;


    document.getElementById('novo-item-checklist').insertAdjacentHTML('beforeend', novoItemHtml);
});




    </script>
</body>
</html>
