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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Rastreadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            margin-top: 30px;
        }
        .form-container {
            margin-bottom: 30px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .navbar {
            margin-bottom: 30px;
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
                    <a class="nav-link active" href="index.php">Início</a>
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
                        <a class="nav-link" href="disparo_mensagem.php">Disparar Mensagens</a>
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
        <h2 class="text-center">Dashboard Rastreadores</h2>
        <div class="mt-4">
            <a href="cadastro_rastreador.php" class="btn btn-success">Adicionar Novo Rastreado</a>
            <a href="cadastro_comando.php" class="btn btn-warning">Adicionar Novo Comando</a>
        </div>
        <div class="table-container mt-4">
            <h3>Rastreadores Cadastrados</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include('conexao.php');
                    $result_rastreadores = $conn->query("SELECT * FROM rastreador");
                    while ($row_rastreador = $result_rastreadores->fetch_assoc()) {
                        echo "<tr>
                                <td>".$row_rastreador['marca']."</td>
                                <td>".$row_rastreador['modelo']."</td>
                                <td><a href='programar_rotina.php?rastreador_id=".$row_rastreador['id']."' class='btn btn-info btn-sm'>Programar</a></td>
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
