<?php

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT); 

    $con = mysqli_connect("localhost:3306", "root", "Ag0306/48", "tdeweb");
    $stmt = mysqli_stmt_init($con);
    $query = "INSERT INTO usuarios(nome, email, senha) VALUES (?,?,?)"; 
    
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $senha_hash);
    $resultado = mysqli_stmt_execute($stmt);

?>