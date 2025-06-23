<?php
include('conexao.php');

// Aqui estamos usando o long polling: a requisição vai aguardar por uma atualização
// Podemos fazer isso verificando a última atualização no banco (timestamp) ou um campo de controle

// O que vai identificar se houve alteração no status (exemplo: data da última alteração)
$query_last_update = "SELECT MAX(data_programacao) AS last_update FROM disparo_mensagens";
$result_last_update = $conn->query($query_last_update);
$row_last_update = $result_last_update->fetch_assoc();
$last_update_time = $row_last_update['last_update'];

// A lógica para verificar se houve mudança
while (true) {
    $query_check_update = "SELECT MAX(data_programacao) AS last_update FROM disparo_mensagens";
    $result_check_update = $conn->query($query_check_update);
    $row_check_update = $result_check_update->fetch_assoc();
    $current_update_time = $row_check_update['last_update'];

    // Se a data de atualização mudou, envia os dados atualizados
    if ($current_update_time != $last_update_time) {
        // Atualiza o último tempo de modificação
        $last_update_time = $current_update_time;

        // Recupera as mensagens atualizadas
        $query = "SELECT r.marca, r.modelo, dm.numero, c.comando, dm.mensagem, dm.status, dm.data_programacao, dm.id 
                  FROM disparo_mensagens dm
                  JOIN rastreador r ON dm.id_rastreador = r.id
                  JOIN comandos c ON dm.id_comando = c.id
                  ORDER BY dm.data_programacao DESC";
        $result = $conn->query($query);

        // Exibe as mensagens disparadas (e retorna para o cliente)
        $output = '';
        while ($row = $result->fetch_assoc()) {
            $status_class = ($row['status'] == 'Programado') ? 'status-programado' : 'status-enviado';
            $output .= "<tr>
                            <td>".$row['marca']." - ".$row['modelo']."</td>
                            <td>".$row['numero']."</td>
                            <td>".$row['comando']."</td>
                            <td class='".$status_class."'>".$row['status']."</td>
                            <td>".$row['data_programacao']."</td>
                            <td>
                                <form method='POST'>
                                    <input type='hidden' name='id_mensagem' value='".$row['id']."'>
                                    <button type='submit' name='delete_mensagem' class='btn btn-danger btn-sm'>Excluir</button>
                                </form>
                            </td>
                          </tr>";
        }

        // Retorna as atualizações
        echo $output;
        exit;
    }

    // Aguarda 2 segundos antes de fazer nova checagem
    sleep(2);
}
?>
