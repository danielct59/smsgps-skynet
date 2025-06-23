<?php
include('conexao.php');

// Verifica se a requisição é para excluir as mensagens
if (isset($_POST['delete_all'])) {
    // Exclui todas as mensagens programadas
    $sql_delete_all = "DELETE FROM disparo_mensagens";
    
    if ($conn->query($sql_delete_all) === TRUE) {
        echo "Mensagens excluídas com sucesso.";
    } else {
        echo "Erro ao excluir mensagens: " . $conn->error;
    }
}
?>
