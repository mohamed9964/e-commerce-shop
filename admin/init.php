<?php

include 'connect.php';

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

// Includr Navbar On All Pages Expect The One With $noNavbar Vairable

if (!isset($noNavbar)) { include $tpl . 'navbar.php'; }
