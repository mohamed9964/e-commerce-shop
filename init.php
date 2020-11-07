<?php
// Error Reporting
ini_set('display_errors', 'On'); // set option to ini.php
error_reporting(E_ALL);
include 'admin/connect.php';

$sessionUser = '';
if(isset($_SESSION['user'])) {
    $sessionUser = $_SESSION['user'];
}
// Routes

$tpl='includes/templates/'; // Template Directory
$lang= 'includes/languages/'; // Language Directory
$func= 'includes/functions/'; // Functions Directory
$css='layout/css/'; // CSS Directory
$js='layout/js/'; // JS Directiory

// Include The Important Files

include $func . 'functions.php';
include $lang . 'english.php';
include $tpl . 'header.php';
