<?php

session_start();
require_once "../models/Usuario.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_input = trim($_POST['usuario']);
    $password_input = $_POST['password_hash'];

    if (empty($usuario_input) || empty($password_input)) {
        $_SESSION['error'] = "Por favor, complete todos los campos.";
        header("Location: ../views/login.php");
        exit();
    }

    $usuarioModel = new Usuario();
    $user = $usuarioModel->getUsuarioPorNombre($usuario_input);

    if ($user && password_verify($password_input, $user['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['usuario'];

        header("Location: ../views/dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header("Location: ../views/login.php");
        exit();
    }
} else {
    header("Location: ../views/login.php");
    exit();
}