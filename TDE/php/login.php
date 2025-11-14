<?php

    $email = $_POST["email"];
    $senha = $_POST["senha"];
    
    $con = mysqli_connect("localhost:3306", "root", "Ag0306/48", "tdeweb");

    $stmt = mysqli_stmt_init($con);
    $query = "SELECT nome, email, senha FROM usuarios WHERE email = ?";
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if($usuario = mysqli_fetch_assoc($resultado)) {
        $senha_hash_bd = $usuario['senha'];

        if (password_verify($senha, $senha_hash_bd)) {
            $retorno["status"] = "s";
            $retorno["mensagem"] = "Login realizado com sucesso!";
        } else {
            $retorno["status"] = "n";
            $retorno["mensagem"] = "E-mail ou senha incorretos.";
        }

    } else {
        $retorno["status"] = "n";
        $retorno["mensagem"] = "E-mail ou senha incorretos.";
    }

    $json = json_encode($retorno);
    echo $json;
?>