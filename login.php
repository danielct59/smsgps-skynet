<?php
session_start();
include('conexao.php');

// Verifica se o usuário já está logado
if (isset($_SESSION['username'])) {
    if ($_SESSION['nivel'] == 'admin') {
        header('Location: index.php');  // Redireciona para a página de admin
    } else {
        header('Location: index.php');  // Redireciona para a página de user
    }
    exit();
}

// Processa o login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $senha = $_POST['senha'];

    // Consulta o usuário no banco de dados
    $sql = "SELECT * FROM usuarios WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Se o usuário existe, verifica a senha
        $row = $result->fetch_assoc();
        if (password_verify($senha, $row['senha'])) {
            // Se a senha estiver correta, inicia a sessão e redireciona conforme o nível
            $_SESSION['username'] = $row['username'];
            $_SESSION['nivel'] = $row['nivel'];  // Salva o nível de acesso

            if ($_SESSION['nivel'] == 'admin') {
                header('Location: index.php');  // Redireciona para a página de admin
            } else {
                header('Location: index.php');  // Redireciona para a página de user
            }
            exit();
        } else {
            $error_message = "Usuário ou senha inválidos.";
        }
    } else {
        $error_message = "Usuário ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Login</h2>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de Login -->
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nome de usuário</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
