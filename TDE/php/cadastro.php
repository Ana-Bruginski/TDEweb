<?php

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $con = mysqli_connect("localhost:3306", "root", "Ag0306/48", "tdeweb");

    $stmt = mysqli_stmt_init($con);
    $query = "INSERT INTO usuarios(nome, email, senha) VALUES (?,?,?)";
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $senha);
    $resultado = mysqli_stmt_execute($stmt);

    if($resultado == true) {
        $retorno["status"] = "s";
        $retorno["mensagem"] = "Cadastrado com sucesso!";
    } else {
        $retorno["status"] = "n";
        $retorno["mensagem"] = "Erro ao cadastrar o usuário!";
    };

    $json = json_encode($retorno);
    echo $json;


?>