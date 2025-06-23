<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Se o nível for 'user', redireciona para 'disparo_mensagem.php'
if ($_SESSION['nivel'] == 'user') {
    header('Location: disparo_mensagem.php');
    exit();
}

include('conexao.php');

// Processa o formulário para adicionar comandos a um rastreador quando o botão de adicionar for pressionado
if (isset($_POST['add_comando'])) {
    $id_rastreador = $_POST['rastreador'];
    $id_comando = $_POST['comando_id'];
    
    // Verifica se o comando já foi associado ao rastreador
    $check_query = "SELECT * FROM mensagens_programadas WHERE id_rastreador = '$id_rastreador' AND id_comando = '$id_comando'";
    $check_result = $conn->query($check_query);
    
    // Só adiciona o comando se ele ainda não estiver associado
    if ($check_result->num_rows == 0) {
        // Inserir o comando selecionado na tabela mensagens_programadas
        $sql = "INSERT INTO mensagens_programadas (id_rastreador, id_comando) 
                VALUES ('$id_rastreador', '$id_comando')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Comando Adicionado!',
                        text: 'O comando foi adicionado com sucesso.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao adicionar comando',
                        text: 'Houve um erro ao adicionar o comando.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                  </script>";
        }
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'info',
                    title: 'Comando já associado',
                    text: 'Este comando já foi associado a este rastreador.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    }
}

// Processa a remoção de comandos de um rastreador via POST
if (isset($_POST['remove_comando']) && isset($_POST['remove_id']) && isset($_POST['rastreador_id'])) {
    $id_rastreador = $_POST['rastreador_id'];
    $id_comando = $_POST['remove_id'];
    
    // Remover o comando específico associado ao rastreador
    $sql = "DELETE FROM mensagens_programadas WHERE id_rastreador = '$id_rastreador' AND id_comando = '$id_comando'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Comando Removido!',
                    text: 'O comando foi removido com sucesso.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao remover comando',
                    text: 'Houve um erro ao remover o comando.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programar Rotina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .btn-custom {
            margin-left: 10px;
            font-size: 0.8rem;
        }
        .form-check-inline {
            display: flex;
            align-items: center;
        }
        .delete-button {
            font-size: 0.7rem;
            margin-top: 5px;
        }
        .add-button {
            font-size: 0.8rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Menu Fixo -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema Rastreadores</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cadastro_rastreador.php">Cadastrar Rastreadores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cadastro_comando.php">Cadastrar Comandos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="programar_rotina.php">Programar Rotina</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="disparo_mensagem.php">Disparar Mensagens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="configuracao.php">Configuração</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-4">
        <h2 class="text-center">Programar Rotina de Comandos</h2>

        <!-- Formulário para Programar a Rotina -->
        <div class="form-container">
            <form method="POST">
                <div class="mb-3">
                    <label for="rastreador" class="form-label">Selecione o Rastreado</label>
                    <select class="form-select" name="rastreador" required>
                        <?php
                        $result = $conn->query("SELECT * FROM rastreador");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='".$row['id']."'>".$row['marca']." - ".$row['modelo']."</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="comando" class="form-label">Selecione o Comando</label>
                    <select class="form-select" name="comando_id" required>
                        <?php
                        // Recupera todos os comandos cadastrados
                        $result_comandos = $conn->query("SELECT * FROM comandos");
                        while ($row_comando = $result_comandos->fetch_assoc()) {
                            echo "<option value='".$row_comando['id']."'>".$row_comando['comando']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="add_comando" class="btn btn-primary btn-sm add-button">Adicionar</button>
            </form>
        </div>

        <!-- Exibir os rastreadores e os comandos associados -->
        <div class="table-container">
            <h3>Rastreadores e seus Comandos Programados</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Rastreado</th>
                        <th>Comandos Associados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Exibe todos os rastreadores e seus comandos associados
                    $result_rastreadores = $conn->query("SELECT * FROM rastreador");
                    while ($row_rastreador = $result_rastreadores->fetch_assoc()) {
                        // Obtém os comandos associados a cada rastreador
                        $id_rastreador = $row_rastreador['id'];
                        $sql_associados = "SELECT c.id, c.comando FROM comandos c
                                           JOIN mensagens_programadas mp ON c.id = mp.id_comando
                                           WHERE mp.id_rastreador = '$id_rastreador'
                                           ORDER BY mp.id"; // Garantir que os comandos sejam listados na ordem de inserção
                        $result_associados = $conn->query($sql_associados);
                        $comandos = "";
                        while ($row_associado = $result_associados->fetch_assoc()) {
                            // Adiciona os comandos na lista com botão de excluir
                            $comandos .= $row_associado['comando'] . 
                            " <form method='POST' style='display:inline'>
                                    <input type='hidden' name='remove_id' value='".$row_associado['id']."'>
                                    <input type='hidden' name='rastreador_id' value='".$id_rastreador."'>
                                    <button type='submit' name='remove_comando' class='btn btn-danger btn-sm delete-button'>Excluir</button>
                                  </form><br>";
                        }
                        echo "<tr>
                                <td>".$row_rastreador['marca']." - ".$row_rastreador['modelo']."</td>
                                <td>".$comandos."</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
