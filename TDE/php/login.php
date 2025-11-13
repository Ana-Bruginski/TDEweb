<?php

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status'=>'n','mensagem'=>'Método não permitido. Use POST.']);
        exit;
    }

    $nome  = isset($_POST['nome'])  ? trim($_POST['nome'])  : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha']       : '';

    if ($nome === '' || $email === '' || $senha === '') {
        echo json_encode(['status'=>'n','mensagem'=>'Todos os campos são obrigatórios.']);
        exit;
    }

    $key_path = _DIR_ . '/key.php';
    if (!file_exists($key_path)) {
        echo json_encode(['status'=>'n','mensagem'=>'Arquivo de chave não encontrado.']);
        exit;
    }
    $key = require $key_path;
    if (!is_string($key) || strlen($key) !== 32) {
        echo json_encode(['status'=>'n','mensagem'=>'Chave inválida. Deve ter 32 bytes.']);
        exit;
    }

    function encrypt_aes256cbc(string $plaintext, string $key): string {
        $method = 'AES-256-CBC';
        $ivlen = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $cipher_raw = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $cipher_raw);
    }

    $nome_cripto = encrypt_aes256cbc($nome, $key);
    $email_cripto = encrypt_aes256cbc($email, $key);
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $con = mysqli_connect("localhost:3306", "root", "Ag0306/48", "tdeweb");
    if (!$con) {
        echo json_encode(['status'=>'n','mensagem'=>'Erro conexão BD: ' . mysqli_connect_error()]);
        exit;
    }
    mysqli_set_charset($con, 'utf8mb4');

    $q = "INSERT INTO usuario(nome, email, senha) VALUES (?,?,?)";
    $stmt = mysqli_stmt_init($con);
    if (!mysqli_stmt_prepare($stmt, $q)) {
        echo json_encode(['status'=>'n','mensagem'=>'Erro no prepare: ' . mysqli_error($con)]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 'sss', $nome_cripto, $email_cripto,  $senha_hash);
    $resultado = mysqli_stmt_execute($stmt);

    if ($resultado) {
        echo json_encode(['status'=>'s','mensagem'=>'Usuário cadastrado com sucesso']);
    } else {
        echo json_encode(['status'=>'n','mensagem'=>'Erro ao cadastrar: ' . mysqli_stmt_error($stmt)]);
    }


?>