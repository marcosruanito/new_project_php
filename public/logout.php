<?php
session_start();

// Remove todas as variáveis da sessão
$_SESSION = [];

// Se existir cookie de sessão, força expiração
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destrói a sessão completamente
session_destroy();

// Mensagem de feedback (flash)
session_start();
$_SESSION['flash_success'] = "Você saiu com sucesso.";

// Redireciona pro login
header('Location: login.php');
exit;
?>
