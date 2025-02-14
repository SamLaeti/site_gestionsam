<?php
session_start();

function redirectIfNotLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isCSS() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'css';
}

function isTechnicien() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'technicien';
}

function isVisiteur() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'visiteur';
}
?>