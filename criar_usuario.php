<?php
include('conexao.php');

// Dados para criar um usuário (Admin e User)
$admin_username = "admin";
$admin_password = "admin";  // Lembre-se de usar uma senha forte e de hashear a senha
$admin_nivel = "admin"; // Nível do usuário

$user_username = "user";
$user_password = "user";
$user_nivel = "user";  // Nível do usuário

// Hasheando as senhas
$admin_hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
$user_hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

// Inserindo o usuário Admin
$admin_sql = "INSERT INTO usuarios (username, senha, nivel) VALUES ('$admin_username', '$admin_hashed_password', '$admin_nivel')";
$conn->query($admin_sql);

// Inserindo o usuário User
$user_sql = "INSERT INTO usuarios (username, senha, nivel) VALUES ('$user_username', '$user_hashed_password', '$user_nivel')";
$conn->query($user_sql);

echo "Usuários criados com sucesso!";
?>
