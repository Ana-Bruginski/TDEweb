// Quando a página carregar, adiciona o evento no botão
window.onload = function() {
  document.getElementById('btnCadastrar').onclick = cadastrar;
};

function cadastrar() {
  var nome = document.getElementById('nome').value;
  var email = document.getElementById('email').value;
  var senha = document.getElementById('senha').value;
  var mensagem = document.getElementById('mensagem');

  if (nome === "" || email === "" || senha === "") {
    mensagem.style.color = "red";
    mensagem.innerHTML = "Preencha todos os campos!";
    return;
  }

  // Monta os dados para enviar
  var dados = "nome=" + encodeURIComponent(nome) +
              "&email=" + encodeURIComponent(email) +
              "&senha=" + encodeURIComponent(senha);

  // Faz a requisição para o PHP
  fetch("login.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: dados
  })
  .then(function(resposta) {
    return resposta.json();
  })
  .then(function(data) {
    if (data.status === "s") {
      mensagem.style.color = "green";
      mensagem.innerHTML = data.mensagem;
      document.getElementById('formCadastro').reset();
    } else {
      mensagem.style.color = "red";
      mensagem.innerHTML = data.mensagem;
    }
  })
  .catch(function(erro) {
    mensagem.style.color = "red";
    mensagem.innerHTML = "Erro ao conectar com o servidor.";
  });
}
