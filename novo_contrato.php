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

    function uploadImagem($input_name) {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
            $extensao = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
            $extensoes_validas = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($extensao), $extensoes_validas)) {
                return null;
            }
            $nome_arquivo = uniqid() . '.' . $extensao;
            $diretorio = 'uploads/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }
            move_uploaded_file($_FILES[$input_name]['tmp_name'], $diretorio . $nome_arquivo);
            return $diretorio . $nome_arquivo;
        }
        return null;
    }

    // Buscar tipos de projeto
    $sql_tipos_projeto = "SELECT id, nome FROM tipos_projeto ORDER BY nome ASC";
    $tipos_projeto_result = $conn->query($sql_tipos_projeto);
    $tipos_projeto = [];
    if ($tipos_projeto_result->num_rows > 0) {
        $tipos_projeto = $tipos_projeto_result->fetch_all(MYSQLI_ASSOC);
    }

    // Buscar clientes
    $sql_clientes = "SELECT id, nome FROM users WHERE role = 'user'";
    $clientes_result = $conn->query($sql_clientes);
    $clientes = [];
    if ($clientes_result->num_rows > 0) {
        $clientes = $clientes_result->fetch_all(MYSQLI_ASSOC);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome_empreendimento = $_POST['nome_empreendimento'];
        $descricao_empreendimento = $_POST['descricao_empreendimento'];
        $itens_checklist = $_POST['itens_checklist'];
        $cliente_ids = $_POST['cliente_id'];
        $datas_checklist = $_POST['datas_checklist'];


        $usuario_id = $_SESSION['id'];
        $tipo_empreendimento = $_POST['tipo_empreendimento'];
        $imagem_capa = uploadImagem('imagem_capa');


        $sql = "INSERT INTO contratos (nome_contrato, descricao, status, tipo_empreendimento, usuario_id, imagem_capa)
        VALUES ('$nome_empreendimento', '$descricao_empreendimento', 'ativo', '$tipo_empreendimento', '$usuario_id', '$imagem_capa')";


        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;

            foreach ($cliente_ids as $cliente_id) {
                $sql_vinculo = "INSERT INTO contratos_clientes (contrato_id, cliente_id)
                                VALUES ($last_id, $cliente_id)";
                $conn->query($sql_vinculo);
            }

            foreach ($itens_checklist as $index => $item) {
                $data = !empty($datas_checklist[$index]) ? $datas_checklist[$index] : null;
                $imagem1 = uploadImagem("imagem1_" . $index);
                $imagem2 = uploadImagem("imagem2_" . $index);
                $imagem3 = uploadImagem("imagem3_" . $index);

                $sql_checklist = "INSERT INTO checklist (contrato_id, item, data, imagem1, imagem2, imagem3)
                                  VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql_checklist);
                $stmt->bind_param("isssss", $last_id, $item, $data, $imagem1, $imagem2, $imagem3);
                $stmt->execute();
            }

            header("Location: admin.php");
            exit();
        } else {
            echo "Erro ao adicionar contrato: " . $conn->error;
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Projeto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>

        body {

            font-family: Inter, sans-serif;
            background-color: #f4f4f9;
            background: url('boss/img/background/bg-7.png') no-repeat center center fixed;
            background-size: cover; /* Faz com que a imagem cubra 100% da tela */
            padding: 20px;
            color: #333;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }


        ::placeholder {
  font-family: Inter, sans-serif;
}

::-ms-input-placeholder {
  font-family: Inter, sans-serif;
}


        h1 {
            color: #333;
            font-size: 2.5em;
            margin: 0 0 20px;
            text-align: center;
            padding-top: 20px;
        }

        .button-salvar {
            background-color: #023C56 !important;
            margin-top: -20px;
        }

        .form-container {
            width: 100%;
            max-width: 650px;
            padding: 25px 50px 50px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px 3px rgba(0, 0, 0, 0.3);
        }

        .form-container label {
            font-size: 1.1em;
            margin-bottom: 8px;
            display: block;
        }

        .form-container input, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 0px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        .form-container input[type="text"], .form-container textarea {
            background-color: #f9f9f9;
        }

        .form-container button {
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            width: 100%;
            border: none;
        }

        .form-container button2 {
            background-color: #0974A3;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            width: 100%;
            border: none;
        }

        .form-container button:hover {
            background-color: #27ae60;
        }


        .checklist_container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }



        .imagem-upload {

            gap: 10px;
            margin-top: 10px;
        }

        .imagem-upload input[type="file"] {

            color: #333;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .imagem-upload input[type="file"]:hover {
            background-color: #ecf0f1;
            border-color: #3498db;
        }


        .checklist-item {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
            margin-top: 20px;
        }

        .checklist-item img {
            width: 100px;
            height: auto;
            margin-right: 10px;
        }

        .checklist-item input[type="text"] {
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }

            .checklist-item input[type="text"], .checklist-item input[type="date"] {
                width: 100%;
            }

            .imagem-upload input[type="file"] {
                width: 100%;
            }
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


.container {
  position: relative;
  display: inline-block;
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


input[type="file"] {
    appearance: none; /* Remove o estilo padrão */
    background-color: #fff;
    color: white; /* Cor do texto */
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

  <div class="form-group">
  <div class="form-container">
    <h1>Cadastrar Novo Projeto</h1>
    <form action="novo_contrato.php" method="POST" enctype="multipart/form-data">
        <label for="nome_empreendimento">Nome do Projeto:</label>
        <input type="text" name="nome_empreendimento" id="nome_empreendimento" required>

        <br><br>

        <label for="tipo_empreendimento">Tipo de Projeto:</label>
        <select name="tipo_empreendimento" id="tipo_empreendimento" required>
            <option value="" disabled selected>Selecione o Tipo de Projeto</option>
            <?php foreach ($tipos_projeto as $tipo) : ?>
                <option value="<?php echo $tipo['id']; ?>"><?php echo $tipo['nome']; ?></option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label for="descricao_empreendimento">Descrição do Projeto:</label>
        <textarea name="descricao_empreendimento" id="descricao_empreendimento" rows="4" required></textarea>


        <br><br>


        <label for="imagem_capa">Imagem de Capa:</label>
        <input type="file" name="imagem_capa" id="imagem_capa" accept="image/*" required>

<br><br>



        <label for="cliente_id">Adicionar Clientes:</label>
        <select name="cliente_id[]" id="cliente_id" multiple required style="width: 100%;">
            <?php foreach ($clientes as $cliente) : ?>
                <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nome']; ?></option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <div class="checklist_container">
            <label for="itens_checklist">Itens do Checklist:</label>
            <div class="checklist-item" id="checklist-container">


                                <label>Nome do Item:</label>
                              <input type="text" name="itens_checklist[]" placeholder="Item do Checklist" required>

                              <label>Data de Conclusão:</label>
                              <input type="date" name="datas_checklist[]" style="font-family: Inter, sans-serif;">

                              <div class="form-group">
                                  <label>Imagem 1:</label>
                                  <input type="file" name="imagem1_0" accept="image/*">


                                  <label>Imagem 2:</label>
                                  <input type="file" name="imagem2_0" accept="image/*">

                                  <label>Imagem 3:</label>
                                  <input type="file" name="imagem3_0" accept="image/*">
                              </div>




            </div>
            <button type="button" id="add-checklist">Adicionar Item</button>
        </div>


        <br><br>

        <button type="submit" class="button-salvar">Salvar Novo Projeto</button>
        <a href="admin.php" class="btn-back">Voltar para a lista de projetos</a>
    </form>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
$(document).ready(function() {
   $('#cliente_id').select2({
       placeholder: 'Adicione um ou mais clientes',
       allowClear: true
   });

   const tipoEmpreendimentoSelect = document.getElementById('tipo_empreendimento');
   tipoEmpreendimentoSelect.addEventListener('change', function() {
       const tipoEmpreendimento = tipoEmpreendimentoSelect.value;
       updateChecklist(tipoEmpreendimento);
   });

   function updateChecklist(tipoEmpreendimento) {
     const checklistContainer = document.getElementById('checklist-container');
     console.log(checklistContainer);  // Verifique se o contêiner existe
     checklistContainer.innerHTML = ''; // Limpa o checklist atual

     $.ajax({
         url: 'get_checklist_preset.php',
         method: 'GET',
         data: { tipo_id: tipoEmpreendimento },
         success: function(response) {
             console.log(response);  // Verifique a resposta da requisição
             const checklistItems = JSON.parse(response);

             // Exibe os itens no DOM
             checklistItems.forEach((item, index) => {
                 const newChecklistItem = document.createElement('div');
                 newChecklistItem.classList.add('checklist-item');
                 newChecklistItem.innerHTML =
                     `

                      <label>Nome do Item:</label>
                    <input type="text" name="itens_checklist[]" value="${item}" placeholder="Item do Checklist" required>

                    <label>Data de Conclusão:</label>
                    <input type="date" name="datas_checklist[]" style="font-family: Inter, sans-serif;">

                    <div class="form-group">
                        <label>Imagem 1:</label>
                        <input type="file" name="imagem1_${index}" accept="image/*">


                        <label>Imagem 2:</label>
                        <input type="file" name="imagem2_${index}" accept="image/*">

                        <label>Imagem 3:</label>
                        <input type="file" name="imagem3_${index}" accept="image/*">
                    </div>

                      `;
                 checklistContainer.appendChild(newChecklistItem);

             });
         },
         error: function(error) {
             console.error("Erro na requisição: ", error);
         }
     });
 }




   $('#add-checklist').click(function() {
       const checklistItem = document.createElement('div');
       checklistItem.classList.add('checklist-item');
       checklistItem.innerHTML =
           `<label>Nome do Item:</label>
         <input type="text" name="itens_checklist[]" placeholder="Item do Checklist" required>

         <label>Data de Conclusão:</label>
         <input type="date" name="datas_checklist[]" style="font-family: Inter, sans-serif;">

         <div class="form-group">
             <label>Imagem 1:</label>
             <input type="file" name="imagem1_0" accept="image/*">


             <label>Imagem 2:</label>
             <input type="file" name="imagem2_0" accept="image/*">

             <label>Imagem 3:</label>
             <input type="file" name="imagem3_0" accept="image/*">
         </div>`;
       document.getElementById('checklist-container').appendChild(checklistItem);
   });
});
</script>

</body>
</html>


<?php
} else {
    header("Location: index.php");
    exit();
}
?>
