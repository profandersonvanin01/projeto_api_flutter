<?php
    // api/api.php

    header("Content-Type: application/json");
    require 'config.php';

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method) {
        case 'GET':
            // Se for informado um ID via query string, retorna um todo; caso contrário, retorna todos
            if(isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $pdo->prepare("SELECT * FROM todos WHERE id = ?");
                $stmt->execute([$id]);
                $todo = $stmt->fetch(PDO::FETCH_ASSOC);
                if($todo) {
                    echo json_encode($todo);
                } else {
                    http_response_code(404);
                    echo json_encode(["mensagem" => "Tarefa não encontrada."]);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM todos ORDER BY created_at DESC");
                $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($todos);
            }
            break;

        case 'POST':
            // Cria uma nova tarefa. Espera receber JSON com pelo menos o campo "titulo"
            $data = json_decode(file_get_contents("php://input"), true);
            if(isset($data['titulo'])) {
                $titulo = $data['titulo'];
                $descricao = isset($data['descricao']) ? $data['descricao'] : '';
                $stmt = $pdo->prepare("INSERT INTO todos (titulo, descricao) VALUES (?, ?)");
                if($stmt->execute([$titulo, $descricao])) {
                    http_response_code(201);
                    echo json_encode(["mensagem" => "Tarefa criada com sucesso.", "id" => $pdo->lastInsertId()]);
                } else {
                    http_response_code(500);
                    echo json_encode(["mensagem" => "Falha ao criar tarefa."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensagem" => "O campo 'título' é obrigatório."]);
            }
            break;

        case 'PUT':
            // Atualiza uma tarefa existente. O ID deve ser enviado via query string e os dados via body JSON
            if(isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $data = json_decode(file_get_contents("php://input"), true);
                if(isset($data['titulo'])) {
                    $titulo = $data['titulo'];
                    $descricao = isset($data['descricao']) ? $data['descricao'] : '';
                    $completado = isset($data['completado']) ? intval($data['completado']) : 0;
                    $stmt = $pdo->prepare("UPDATE todos SET titulo = ?, descricao = ?, completado = ? WHERE id = ?");
                    if($stmt->execute([$titulo, $descricao, $completado, $id])) {
                        echo json_encode(["mensagem" => "Tarefa atualizada com sucesso."]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["mensagem" => "Falha ao atualizar tarefa."]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["mensagem" => "O campo 'titulo' é obrigatório."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensagem" => "O parâmetro 'id' é obrigatório para atualização."]);
            }
            break;

        case 'DELETE':
            // Exclui uma tarefa. O ID deve ser enviado via query string
            if(isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ?");
                if($stmt->execute([$id])) {
                    echo json_encode(["mensagem" => "Tarefa excluída com sucesso."]);
                } else {
                    http_response_code(500);
                    echo json_encode(["mensagem" => "Falha ao excluir tarefa."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensagem" => "O parâmetro 'id' é obrigatório para exclusão."]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["mensagem" => "Método não permitido."]);
            break;
    }
?>
