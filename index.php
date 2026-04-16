<?php
// Iniciamos la sesión para comprobar el estado del usuario
session_start();

// Si el usuario tiene una sesión activa, lo redirigimos al área segura (dashboard)
if (isset($_SESSION['usuario_id'])) {
    header("Location: views/dashboard.php");
    exit();
} else {
    // Si no está autenticado, lo enviamos al login
    header("Location: views/login.php");
    exit();
}