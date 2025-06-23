<?php
session_start();

include('conexao.php');

// Processa o disparo da mensagem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rastreador'])) {
    $id_rastreador = $_POST['rastreador'];
    $numero_rastreador = $_POST['numero_rastreador'];

    // Verifica se o número de rastreador foi inserido corretamente
    if (empty($numero_rastreador)) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Número do rastreador não pode ser vazio!',
                    showConfirmButton: true
                });
              </script>";
        exit;
    }

    // Recupera os comandos associados ao rastreador selecionado
    $comandos_query = "SELECT c.id, c.comando FROM comandos c 
                       JOIN mensagens_programadas mp ON c.id = mp.id_comando
                       WHERE mp.id_rastreador = '$id_rastreador' 
                       ORDER BY mp.id";
    $comandos_result = $conn->query($comandos_query);

    if ($comandos_result->num_rows > 0) {
        while ($row_comando = $comandos_result->fetch_assoc()) {
            // Insere os comandos programados com o número do rastreador e o comando (mensagem)
            $sql = "INSERT INTO disparo_mensagens (id_rastreador, id_comando, numero, mensagem, status) 
                    VALUES ('$id_rastreador', '" . $row_comando['id'] . "', '$numero_rastreador', '" . $row_comando['comando'] . "', 'Programado')";

            if (!$conn->query($sql)) {
                // Se ocorrer algum erro ao inserir no banco, exibe o erro
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao salvar mensagem',
                            text: 'Erro no banco de dados: " . $conn->error . "',
                            showConfirmButton: true
                        });
                      </script>";
                exit;
            }
        }

        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Mensagens Programadas com Sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Nenhum comando encontrado para este rastreador!',
                    showConfirmButton: true
                });
              </script>";
    }
}

// Processa a exclusão de uma mensagem disparada
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_mensagem'])) {
    $id_mensagem = $_POST['id_mensagem'];

    // Excluir a mensagem disparada da tabela
    $sql_delete = "DELETE FROM disparo_mensagens WHERE id = '$id_mensagem'";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Mensagem Excluída com Sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao excluir mensagem',
                    text: 'Erro no banco de dados: " . $conn->error . "',
                    showConfirmButton: true
                });
              </script>";
    }
}

// Recupera os rastreadores para o selectbox
$rastreadores_query = "SELECT * FROM rastreador";
$rastreadores_result = $conn->query($rastreadores_query);

// Exibe a lista de mensagens disparadas com a data de programação
$query = "SELECT r.marca, r.modelo, dm.numero, c.comando, dm.mensagem, dm.status, dm.data_programacao, dm.id 
          FROM disparo_mensagens dm
          JOIN rastreador r ON dm.id_rastreador = r.id
          JOIN comandos c ON dm.id_comando = c.id
          ORDER BY dm.data_programacao DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disparo de Mensagem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .form-container {
            margin-top: 30px;
        }

        .table-container {
            margin-top: 30px;
        }

        .navbar {
            margin-bottom: 30px;
        }

        .status-programado {
            color: black;
            /* Alterado para preto */
        }

        .status-enviado {
            color: black;
            /* Alterado para preto */
        }

        .input-phone {
            width: 100%;
            padding: 0.5rem;
            font-size: 1rem;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <!-- Menu Fixo -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema Rastreadores</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>

                    <?php if ($_SESSION['nivel'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cadastro_rastreador.php">Cadastrar Rastreadores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cadastro_comando.php">Cadastrar Comandos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="programar_rotina.php">Programar Rotina</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="disparo_mensagem.php">Disparar Mensagens</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="configuracao.php">Configuração</a>
                        </li>
                    <?php elseif ($_SESSION['nivel'] == 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="disparo_mensagem.php">Disparar Mensagens</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5 pt-4">
        <h2 class="text-center">Disparo de Mensagem</h2>

        <!-- Formulário de Disparo de Mensagem -->
        <div class="form-container">
            <form method="POST">
                <div class="mb-3">
                    <label for="rastreador" class="form-label">Selecione o Rastreado</label>
                    <select class="form-select" name="rastreador" required>
                        <?php
                        while ($row_rastreador = $rastreadores_result->fetch_assoc()) {
                            echo "<option value='" . $row_rastreador['id'] . "'>" . $row_rastreador['marca'] . " - " . $row_rastreador['modelo'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="numero_rastreador" class="form-label">Número do Rastreador</label>
                    <input type="tel" class="input-phone" id="numero_rastreador" name="numero_rastreador"
                        placeholder="Ex: +5588999584256" pattern="^\+55\d{11}$" required>
                    <small class="form-text text-muted">Digite o número no formato: +5588999584256</small>
                </div>

                <button type="submit" class="btn btn-success">Programar Mensagens</button>
            </form>
        </div>

        <div class="table-container">
            <h3>Mensagens Programadas</h3>
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-danger" id="limparMensagens">Limpar Mensagens Programadas</button>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Rastreado</th>
                        <th>Número</th> <!-- Adicionado o campo número -->
                        <th>Comando</th>
                        <th>Status</th>
                        <th>Data de Programação</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Exibe as mensagens disparadas
                    while ($row = $result->fetch_assoc()) {
                        $status_class = ($row['status'] == 'Programado') ? 'status-programado' : 'status-enviado';
                        echo "<tr>
                                <td>" . $row['marca'] . " - " . $row['modelo'] . "</td>
                                <td>" . $row['numero'] . "</td> <!-- Número do Rastreador -->
                                <td>" . $row['comando'] . "</td>
                                <td class='" . $status_class . "'>" . $row['status'] . "</td>
                                <td>" . $row['data_programacao'] . "</td>
                                <td>
                                    <form method='POST'>
                                        <input type='hidden' name='id_mensagem' value='" . $row['id'] . "'>
                                        <button type='submit' name='delete_mensagem' class='btn btn-danger btn-sm'>Excluir</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
    <script>
        // Função para fazer a requisição AJAX e atualizar a tabela com os dados mais recentes
        function atualizarTabela() {
            $.ajax({
                url: 'atualizar_status.php', // O arquivo PHP que processa a atualização
                method: 'GET',
                success: function (data) {
                    // Atualiza a tabela com os dados retornados
                    $('tbody').html(data);
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Não foi possível obter os dados.',
                        showConfirmButton: true
                    });
                }
            });
        }

        // Inicia o long polling chamando a função a cada 3 segundos
        $(document).ready(function () {
            setInterval(atualizarTabela, 3000); // Atualiza a tabela a cada 3 segundos
        });
    </script>
    <script>
        $(document).ready(function () {
            // Função para limpar as mensagens programadas
            $('#limparMensagens').on('click', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Você tem certeza?',
                    text: 'Isso irá excluir todas as mensagens programadas!',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Chama a função para excluir todas as mensagens programadas
                        limparMensagensProgramadas();
                    }
                });
            });
        });

        // Função para excluir as mensagens programadas com AJAX
        function limparMensagensProgramadas() {
            $.ajax({
                url: 'limpar_mensagens.php', // O arquivo PHP que processa a exclusão
                method: 'POST',
                data: {
                    delete_all: true // Envia a informação para excluir todas as mensagens programadas
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mensagens Excluídas',
                        text: 'Todas as mensagens programadas foram excluídas com sucesso!',
                        showConfirmButton: true
                    });

                    // Atualiza a tabela após a exclusão
                    atualizarTabela();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Não foi possível excluir as mensagens.',
                        showConfirmButton: true
                    });
                }
            });
        }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>