<?php
/**
 * login.php
 *
 * Pagina de inicio de sesión preparada para tratar errores de contraseña, errores de DNI,
 * envio vacio de formulario y control de vista de contraseña por botón
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @link       http://localhost/GestionGuardias/PROYECTO/REST/rest_cliente/login.php
 */

session_start();
$error = $_SESSION["error_login"] ?? null;
unset($_SESSION["error_login"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Log In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="src/images/favicon.png" type="image/x-icon">      
    <link rel="stylesheet" href="src/styles.css">
</head>
<body>
    <div class="fecha-actual text-center mt-3"></div>
    <div class="text-center">
        <img id="logo" src="src/images/logoenUno2.png" alt="Logo de AsistGuard" style="margin-top:50px;">
    </div>

    <div class="login-box">
        <h2 class="text-center mb-4">Iniciar sesión</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="inicioSesion.php">
            <div class="mb-3">
                <label for="document" class="form-label">Número de documento</label>
                <input type="text" class="form-control" id="document" name="document" placeholder="12345678Z" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <span id="eyeIcon">👁️</span>
                    </button>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" name="validar" class="btn btn-gradient">Entrar</button>
            </div>
        </form>
    </div>

    <!-- JavaScript -->
     <script src="src/app.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
