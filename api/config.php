<?php
    // api/config.php

    $host = "localhost";
    $db_name = "todo_db";
    $username = "root";     // ajuste conforme sua configuração
    $password = "";         // ajuste conforme sua configuração

    try {
        $pdo = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Sucesso!";
    } catch(PDOException $exception) {
        echo "Erro na conexão: " . $exception->getMessage();
        exit;
    }
?>