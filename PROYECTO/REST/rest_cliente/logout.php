<?php
/**
 * =====================
 *  logout.php
 * =====================
 * 
 * Cierra la sesión del usuario actual.
 * Elimina todas las variables de sesión, destruye la sesión y
 * redirige al usuario de vuelta al formulario de login.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 */

session_start(); // Inicia la sesión si no estaba activa

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión actual
session_destroy();

// Redirige al formulario de login
header("Location: login.php");
exit;
