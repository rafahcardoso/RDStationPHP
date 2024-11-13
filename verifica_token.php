<?php

// Configurações de conexão com o banco de dados
$db_host = 'host';
$db_name = 'nomebanco';
$db_user = 'usuario';
$db_password = 'senha';

// URLs da API RD Station
$api_url = 'https://api.rd.services/platform/landing_pages';  // URL para verificar validade do token
$token_url = 'https://api.rd.services/auth/token';       // URL para renovação do token

// Conectando ao banco de dados
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Função para obter dados da tabela app_credentials
function obter_credenciais() {
    global $pdo;
    $stmt = $pdo->query("SELECT client_id, client_secret, refresh_token FROM app_credentials LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Função para obter o access_token da tabela token_rdsm
function obter_access_token() {
    global $pdo;
    $stmt = $pdo->query("SELECT token FROM token_rdsm LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['token'] : null;
}

// Função para atualizar o access_token na tabela token_rdsm
function atualizar_access_token($novo_access_token) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE token_rdsm SET token = :token WHERE id = 1"); // ajuste o WHERE conforme necessário
    $stmt->execute([':token' => $novo_access_token]);
}

// Função para atualizar o refresh_token na tabela app_credentials
function atualizar_refresh_token($novo_refresh_token) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE app_credentials SET refresh_token = :refresh_token WHERE id = 1"); // ajuste o WHERE conforme necessário
    $stmt->execute([':refresh_token' => $novo_refresh_token]);
}


function verificar_validade_token($access_token) {
    global $api_url;

    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api.rd.services/platform/landing_pages",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: Bearer $access_token"
      ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);



    
    if ($err) {
        echo "Token expirado. Renovando...\n";
        renovar_token();
    } else {
       
        echo "Token válido.\n";
       echo "<pre>";
        print_r($response);
        echo "</pre>";
    }
}

// Função para renovar o token de acesso
function renovar_token() {
    global $token_url;

    $credenciais = obter_credenciais();
    if (!$credenciais) {
        die("Erro: Credenciais não encontradas.");
    }


    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api.rd.services/auth/token",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode([
        'client_id' => $credenciais['client_id'],
        'client_secret' => $credenciais['client_secret'],
        'refresh_token' => $credenciais['refresh_token']
      ]),
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "content-type: application/json"
      ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
      echo "Erro ao renovar Token:" . $err;
    } else {
        $token_data = json_decode($response, true);
        $novo_access_token = $token_data['access_token'];
        $novo_refresh_token = $token_data['refresh_token']; // Atualiza o refresh token, se um novo for fornecido

        atualizar_access_token($novo_access_token);
        atualizar_refresh_token($novo_refresh_token);

        echo "Token renovado com sucesso.\n";
        echo "Novo access token: $novo_access_token\n";
    }

}

// // Exemplo de utilização
// //$access_token = obter_access_token();
// echo "Access token: $access_token\n";
// if ($access_token) {
//   //  verificar_validade_token($access_token);
// } else {
//  //   echo "Erro: Token de acesso não encontrado.";
// }

?>