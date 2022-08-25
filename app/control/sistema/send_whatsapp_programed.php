<?php



include("SistemaSendMessageProgramed.class.php");


$nome = "verificar";

$ref = $_GET['ref'];

for ($i=1; $i < $argc; $i++) {parse_str($argv[$i]);}


$sendText = new SistemaSendMessageProgramed();

$sendText->testeClass();


?>