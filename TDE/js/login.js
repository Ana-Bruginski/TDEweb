// ATENÇÃO: A Chave Pública do servidor ainda precisa ser carregada!
const SERVER_PUBLIC_KEY_PEM = "SUA_CHAVE_PUBLICA_RSA_AQUI"; 
const URL_AUTENTICACAO = "../php/autenticar.php";

function logar() {
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const mensagem = document.getElementById('mensagem');
    
    // 1. DADOS DE LOGIN
    const dadosLogin = JSON.stringify({ email: email, senha: senha });

    // --- CRIPTOGRAFIA HÍBRIDA ---

    // A. Criptografia Simétrica (AES) - Usando CryptoJS
    // Geração de chave AES e IV aleatórios
    const chaveAes = CryptoJS.lib.WordArray.random(256/8); // 32 bytes (AES-256)
    const iv = CryptoJS.libWordArray.random(128/8);        // 16 bytes

    // Criptografa os dados de login com AES
    const dadosCriptografadosAes = CryptoJS.AES.encrypt(dadosLogin, chaveAes, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    }).toString();

    // B. Criptografia Assimétrica (RSA) - Usando JSEncrypt
    const encryptor = new JSEncrypt();
    encryptor.setPublicKey(SERVER_PUBLIC_KEY_PEM);

    // Criptografa a chave AES (simétrica) usando a chave pública RSA (assimétrica)
    // O JSEncrypt limita o tamanho da mensagem, por isso criptografamos apenas a chave.
    const chaveAesCriptografadaRsa = encryptor.encrypt(chaveAes.toString());

    // C. Envio
    if (!chaveAesCriptografadaRsa) {
         mensagem.style.color = "red";
         mensagem.innerHTML = "Erro ao criptografar chave AES com RSA. A chave pública está correta?";
         return;
    }

    const dados = new FormData();
    dados.append("data", dadosCriptografadosAes);
    dados.append("key", chaveAesCriptografadaRsa);
    dados.append("iv", iv.toString()); // Enviar o IV em texto puro é seguro (não é um segredo)

    fetch(URL_AUTENTICACAO, {
        method: "POST",
        body: dados
    })
    .then(resposta => resposta.json())
    .then(data => {
        // ... Lógica de tratamento de resposta do servidor
    })
    .catch(erro => {
        // ...
    });
}