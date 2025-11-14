<?php
// Configurações para a chave RSA (recomendado 2048 ou 4096 bits)
$config = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 4096, // Alto nível de segurança
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

// 1. GERAÇÃO DO NOVO PAR DE CHAVES PRIVADA E PÚBLICA
$resource = openssl_pkey_new($config);

if (!$resource) {
    die("Erro ao gerar par de chaves. Verifique se o OpenSSL está configurado corretamente.");
}

// 2. EXPORTAÇÃO DA CHAVE PRIVADA (MANTIDA SECRETA NO SERVIDOR)
// O ideal é salvar esta chave em um local seguro, fora do acesso público (ex: fora da pasta 'www' ou 'public_html')
openssl_pkey_export($resource, $private_key_pem);
file_put_contents('../php/private_key.pem', $private_key_pem);

// 3. OBTENÇÃO DA CHAVE PÚBLICA EM FORMATO PEM (PARA USO NO JAVASCRIPT)
$details = openssl_pkey_get_details($resource);
$public_key_pem = $details['key'];

// 4. EXPORTAÇÃO DA CHAVE PÚBLICA (OPCIONAL, mas útil para referência)
file_put_contents('../php/public_key.pem', $public_key_pem);

// Exibe a chave pública para que você possa copiá-la para o JavaScript
echo "<h2>Chave Privada salva em: ../php/private_key.pem</h2>";
echo "<h2>Chave Pública (Copie o conteúdo abaixo):</h2>";
echo "<pre>" . htmlspecialchars($public_key_pem) . "</pre>";

// Libera o recurso
openssl_free_key($resource);

?>