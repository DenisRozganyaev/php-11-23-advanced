<?php
$isInfo = $_GET['info'] ?? false;

if ($isInfo) {
    echo "<h1>PHP INFO:</h1>";
    phpinfo();
    die();
}
