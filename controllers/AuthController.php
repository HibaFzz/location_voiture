<?php
include '../../models/User.php';

class AuthController
{
    public static function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Start session only if it's not already started
        }
    }

    public static function checkAccess($requiredRole)
    {
        self::startSession(); // Ensure session is started

        // Check if the user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: login.php');
            exit();
        }

        // Check if the user's role matches the required role
        if ($_SESSION['user']['role'] !== $requiredRole) {
            // If the role doesn't match, redirect to "access denied"
            header('Location: ../access_denied.php');
            exit();
        }
    }

    public static function checkMultipleRoles($roles)
    {
        self::startSession(); // Ensure session is started

        // Check if the user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: ../frontOffice/login.php');
            exit();
        }

        // Check if the user's role is in the allowed roles array
        if (!in_array($_SESSION['user']['role'], $roles)) {
            // If the role is not allowed, redirect to "access denied"
            header('Location: ../frontOffice/access_denied.php');
            exit();
        }
    }

    public static function logout() {
        // Start the session only if it hasn't been started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear session data
        session_unset(); 
        session_destroy(); 

        // Redirect to login page
        header('Location: ../frontOffice/login.php');
        exit(); // Ensure that no further code is executed
    }
    public static function getCurrentUser() {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user']; 
        }

        return null; 
    }

    
}
?>
