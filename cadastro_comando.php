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

// Processa o formulário para adicionar um novo comando
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comando'])) {
    $comando = $_POST['comando'];
    $sql = "INSERT INTO comandos (comando) VALUES ('$comando')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Comando Cadastrado com Sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao cadastrar comando',
                    text: 'Houve um erro ao cadastrar o comando.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    }
}

// Processa a exclusão de um comando via POST
if (isset($_POST['remove_id'])) {
    $id_comando = $_POST['remove_id'];

    // Verifica se o comando está associado a algum rastreador
    $check_query = "SELECT * FROM mensagens_programadas WHERE id_comando = '$id_comando'";
    $check_result = $conn->query($check_query);
    
    // Se o comando estiver associado a um rastreador, exibe erro
    if ($check_result->num_rows > 0) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao excluir comando',
                    text: 'Este comando está associado a um rastreador. Não é possível excluir.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        // Se não estiver associado, podemos excluir o comando
        $sql = "DELETE FROM comandos WHERE id = '$id_comando'";
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
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Comandos</title>
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
        .delete-button {
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
                        <a class="nav-link active" href="cadastro_comando.php">Cadastrar Comandos</a>
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
                    <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-4">
        <h2 class="text-center">Cadastrar Comandos</h2>

        <!-- Formulário para Cadastrar Comandos -->
        <div class="form-container">
            <form method="POST">
                <div class="mb-3">
                    <label for="comando" class="form-label">Comando</label>
                    <input type="text" class="form-control" id="comando" name="comando" required>
                </div>
                <button type="submit" class="btn btn-success">Cadastrar</button>
            </form>
        </div>

        <!-- Exibir os comandos cadastrados -->
        <div class="table-container">
            <h3>Comandos Cadastrados</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Comando</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Exibe todos os comandos cadastrados
                    $result_comandos = $conn->query("SELECT * FROM comandos");
                    while ($row_comando = $result_comandos->fetch_assoc()) {
                        echo "<tr>
                                <td>".$row_comando['comando']."</td>
                                <td>
                                    <form method='POST' style='display:inline'>
                                        <input type='hidden' name='remove_id' value='".$row_comando['id']."'>
                                        <button type='submit' class='btn btn-danger btn-sm delete-button'>Excluir</button>
                                    </form>
                                </td>
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
