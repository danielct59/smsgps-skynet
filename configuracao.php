<?php
include('conexao.php');

// Processa o formulário para salvar as configurações
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['configuracao_form'])) {
    $provedor = $_POST['provedor'];
    $dominio_api = $_POST['dominio_api'];
    $chave_api = $_POST['chave_api'];

    // Verifica se já existe uma configuração
    $check_query = "SELECT * FROM configuracoes WHERE provedor = 'mobizon'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // Se já houver uma configuração para o provedor "mobizon", atualiza as informações
        $sql = "UPDATE configuracoes SET dominio_api = '$dominio_api', chave_api = '$chave_api' WHERE provedor = 'mobizon'";
    } else {
        // Se não houver, insere uma nova configuração
        $sql = "INSERT INTO configuracoes (provedor, dominio_api, chave_api) 
                VALUES ('$provedor', '$dominio_api', '$chave_api')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Configuração Salva com Sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao salvar configuração',
                    text: 'Houve um erro ao salvar as configurações.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    }
}

// Processa a alteração de senha de um usuário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['alterar_senha'])) {
    $usuario_id = $_POST['usuario_id'];
    $nova_senha = $_POST['nova_senha'];
    $hashed_senha = password_hash($nova_senha, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET senha = '$hashed_senha' WHERE id = '$usuario_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Senha alterada com sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao alterar senha',
                    text: 'Houve um erro ao alterar a senha.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    }
}

// Processa o cadastro de novos usuários
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar_usuario'])) {
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $nivel = $_POST['nivel'];

    $hashed_senha = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (username, senha, nivel) VALUES ('$username', '$hashed_senha', '$nivel')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Usuário cadastrado com sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao cadastrar usuário',
                    text: 'Houve um erro ao cadastrar o usuário.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    }
}

// Processa a exclusão de um usuário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_usuario'])) {
    $usuario_id = $_POST['usuario_id'];

    $sql = "DELETE FROM usuarios WHERE id = '$usuario_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Usuário excluído com sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao excluir usuário',
                    text: 'Houve um erro ao excluir o usuário.',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
    }
}

// Recupera as configurações atuais
$config_query = "SELECT * FROM configuracoes WHERE provedor = 'mobizon'";
$config_result = $conn->query($config_query);
$config = $config_result->fetch_assoc();

// Recupera a lista de usuários cadastrados
$usuarios_query = "SELECT * FROM usuarios";
$usuarios_result = $conn->query($usuarios_query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-container {
            margin-top: 30px;
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
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
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
                        <a class="nav-link active" href="configuracao.php">Configuração</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-4">
        <h2 class="text-center">Configuração</h2>

        <!-- Formulário de Configuração -->
        <div class="form-container">
            <form method="POST">
                <div class="mb-3">
                    <label for="provedor" class="form-label">Provedor</label>
                    <select class="form-select" name="provedor" required>
                        <option value="mobizon" <?php if(isset($config) && $config['provedor'] == 'mobizon') echo 'selected'; ?>>mobizon</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="dominio_api" class="form-label">Domínio da API</label>
                    <input type="text" class="form-control" id="dominio_api" name="dominio_api" value="<?php echo isset($config) ? $config['dominio_api'] : 'api.mobizon.com.br'; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="chave_api" class="form-label">Chave API</label>
                    <input type="text" class="form-control" id="chave_api" name="chave_api" value="<?php echo isset($config) ? $config['chave_api'] : 'br1a683d455413ffbb3524365fc96ba2635f2570ac10e44543de95807a8e59272e1491'; ?>" required>
                </div>

                <button type="submit" name="configuracao_form" class="btn btn-success">Salvar Configuração</button>
            </form>
        </div>

        <h3 class="mt-5">Usuários Cadastrados</h3>
        <!-- Botão para adicionar novo usuário -->
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createUserModal">Criar Novo Usuário</button>


        <!-- Tabela de usuários -->
        <table class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome de Usuário</th>
                    <th>Nível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $usuarios_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo $usuario['username']; ?></td>
                        <td><?php echo $usuario['nivel']; ?></td>
                        <td>
                            <form method="POST" action="configuracao.php" style="display:inline;">
                                <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                <button type="submit" name="excluir_usuario" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal" onclick="document.getElementById('userId').value='<?php echo $usuario['id']; ?>'">Trocar Senha</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        </div>

    <!-- Modal para criar novo usuário -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Criar Novo Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome de Usuário</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                        <div class="mb-3">
                            <label for="nivel" class="form-label">Nível de Acesso</label>
                            <select class="form-select" name="nivel" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit" name="cadastrar_usuario" class="btn btn-primary">Cadastrar Usuário</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para alterar a senha -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Alterar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="usuario_id" id="userId">
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
                        </div>
                        <button type="submit" name="alterar_senha" class="btn btn-primary">Alterar Senha</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
