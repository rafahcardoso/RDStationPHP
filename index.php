<?php

include 'verifica_token.php';

$access_token = obter_access_token();

echo    'Access Token na pagina integração: ' . $access_token;

if ($access_token) {
    verificar_validade_token($access_token);
}
