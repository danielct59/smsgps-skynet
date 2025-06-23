<?php
include('conexao.php');

// Processa o formulário para adicionar um novo rastreador
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marca'])) {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $sql = "INSERT INTO rastreador (marca, modelo) VALUES ('$marca', '$modelo')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Rastreado Cadastrado com Sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao cadastrar rastreador',
                    text: 'Houve um erro ao cadastrar o rastreador.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    }
}

// Processa a exclusão de um rastreador via POST
if (isset($_POST['remove_id'])) {
    $id_rastreador = $_POST['remove_id'];
    $sql = "DELETE FROM rastreador WHERE id = '$id_rastreador'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Rastreado Removido!',
                    text: 'O rastreador foi removido com sucesso.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao remover rastreador',
                    text: 'Houve um erro ao remover o rastreador.',
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
    <title>Cadastrar Rastreadores</title>
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
                        <a class="nav-link active" href="cadastro_rastreador.php">Cadastrar Rastreadores</a>
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
                    <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-4">
        <h2 class="text-center">Cadastrar Rastreadores</h2>

        <!-- Formulário para Cadastrar Rastreadores -->
        <div class="form-container">
            <form method="POST">
                <div class="mb-3">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" class="form-control" id="marca" name="marca" required>
                </div>
                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" required>
                </div>
                <button type="submit" class="btn btn-success">Cadastrar</button>
            </form>
        </div>

        <!-- Exibir os rastreadores cadastrados -->
        <div class="table-container">
            <h3>Rastreadores Cadastrados</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Exibe todos os rastreadores cadastrados
                    $result_rastreadores = $conn->query("SELECT * FROM rastreador");
                    while ($row_rastreador = $result_rastreadores->fetch_assoc()) {
                        echo "<tr>
                                <td>".$row_rastreador['marca']."</td>
                                <td>".$row_rastreador['modelo']."</td>
                                <td>
                                    <form method='POST' style='display:inline'>
                                        <input type='hidden' name='remove_id' value='".$row_rastreador['id']."'>
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
