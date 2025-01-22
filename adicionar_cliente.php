<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    exit('Acesso não autorizado');
}

if (isset($_POST['cliente_id']) && isset($_POST['contrato_id'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $contrato_id = intval($_POST['contrato_id']);

    // Verifique se o cliente já está vinculado ao contrato
    $check_sql = "SELECT * FROM contratos_clientes WHERE contrato_id = ? AND cliente_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $contrato_id, $cliente_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        // Se não estiver vinculado, insira
        $sql_inserir_cliente = "INSERT INTO contratos_clientes (contrato_id, cliente_id) VALUES (?, ?)";
        $stmt_inserir_cliente = $conn->prepare($sql_inserir_cliente);
        $stmt_inserir_cliente->bind_param("ii", $contrato_id, $cliente_id);
        $stmt_inserir_cliente->execute();
        echo 'success';  // Retorne sucesso
    } else {
        echo 'already_exists';  // Cliente já está vinculado
    }
} else {
    echo 'invalid_request';
}
