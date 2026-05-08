<?php
$path = __DIR__ . '/backend/config/config.ini';
echo "Caminho: " . $path . "<br>";
echo "Existe: " . (file_exists($path) ? "SIM" : "NÃO") . "<br>";
$cfg = parse_ini_file($path, true);
var_dump($cfg);