<?php
include('conexao.php');

// Recupera as mensagens atualizadas do banco
$query = "SELECT r.marca, r.modelo, dm.numero, c.comando, dm.mensagem, dm.status, dm.data_programacao, dm.id 
          FROM disparo_mensagens dm
          JOIN rastreador r ON dm.id_rastreador = r.id
          JOIN comandos c ON dm.id_comando = c.id
          ORDER BY dm.data_programacao DESC";
$result = $conn->query($query);

// Gerar o conteúdo da tabela de mensagens
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

// Retorna o conteúdo atualizado da tabela
echo $output;
?>
