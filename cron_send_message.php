<?php
$servername = "localhost";
$username = "root";
$password = "vertrigo";
$dbname = "gps"; // Nome do banco de dados

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificação da conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Recupera a próxima mensagem com status 'Programado', ordenada de forma ascendente
$sql = "SELECT dm.id, dm.numero, dm.mensagem FROM disparo_mensagens dm
        WHERE dm.status = 'Programado'
        ORDER BY dm.data_programacao ASC
        LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Pega a mensagem programada
    $row = $result->fetch_assoc();
    $id_mensagem = $row['id'];
    $numero_rastreador = $row['numero'];
    $mensagem = $row['mensagem'];

    // Código nativo para envio de SMS
    $url = 'http://100.64.0.58/default/en_US/sms_info.html?type=sms';
    $data = [
        'line2' => 1,
        'smskey' => '67f4384c',
        'action' => 'SMS',
        'telnum' => $numero_rastreador,
        'smscontent' => $mensagem,
        'send' => 'Send'
    ];

    // Cabeçalhos HTTP
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        'Authorization: Basic YWRtaW46YWRtaW4=',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'Content-Type: application/x-www-form-urlencoded',
        'Origin: http://100.64.0.58',
        'Referer: http://100.64.0.58/default/en_US/sms.html?u=admin&p=admin&l=2&n=' . urlencode($numero_rastreador) . '&msg=' . urlencode($mensagem),
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36'
    ];

    // Iniciar cURL
    $ch = curl_init();

    // Configurar opções do cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignorar verificação SSL (equivalente ao --insecure no curl)

    // Executar o cURL
    $response = curl_exec($ch);

    // Verificar se ocorreu algum erro
    if (curl_errno($ch)) {
        echo 'Erro cURL: ' . curl_error($ch);
    } else {
        echo "SMS enviado com sucesso!";
    }

    // Fechar a conexão cURL
    curl_close($ch);

    // Atualizar o status da mensagem para 'Enviado'
    $update_sql = "UPDATE disparo_mensagens SET status = 'Enviado' WHERE id = '$id_mensagem'";
    if ($conn->query($update_sql) === TRUE) {
        echo "Mensagem enviada e status atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar o status da mensagem: " . $conn->error;
    }
} else {
    echo "Nenhuma mensagem programada encontrada.";
}

$conn->close();
?>
