<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lista de Tarefas</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { text-align: center; }
        form { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .delete-btn { color: red; cursor: pointer; }
        .update-btn { color: blue; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Lista de Tarefas</h2>

    <form id="task-form">
        <input type="text" id="task-title" placeholder="Título da Tarefa" required>
        <input type="text" id="task-desc" placeholder="Descrição da Tarefa">
        <button type="submit">Adicionar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Concluído</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="task-list">
            <!-- As tarefas serão carregadas aqui -->
        </tbody>
    </table>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            carregarTarefas();

            document.getElementById("task-form").addEventListener("submit", function(event) {
                event.preventDefault();
                adicionarTarefa();
            });
        });

        function carregarTarefas() {
            fetch("../api/api.php")
                .then(response => {
                    if (!response.ok) throw new Error("Erro na requisição");
                    return response.json();
                })
                .then(data => {
                    console.log("Dados recebidos:", data); // DEBUG
                    const taskList = document.getElementById("task-list");
                    taskList.innerHTML = "";
                    
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(task => {
                            const row = document.createElement("tr");
                            row.innerHTML = `
                                <td>${task.id}</td>
                                <td>${task.titulo}</td>
                                <td>${task.descricao}</td>
                                <td>${task.completado ? '✅' : '❌'}</td>
                                <td>
                                    <span class="update-btn" onclick="atualizarTarefa(${task.id}, '${task.titulo}', '${task.descricao}', ${task.completado})">Editar</span> |
                                    <span class="delete-btn" onclick="excluirTarefa(${task.id})">Excluir</span>
                                </td>
                            `;
                            taskList.appendChild(row);
                        });
                    } else {
                        taskList.innerHTML = "<tr><td colspan='5' style='text-align:center;'>Nenhuma tarefa encontrada.</td></tr>";
                    }
                })
                .catch(error => console.error("Erro ao carregar tarefas:", error));
        }

        function adicionarTarefa() {
            const titleInput = document.getElementById("task-title");
            const descInput = document.getElementById("task-desc");
            const titulo = titleInput.value.trim();
            const descricao = descInput.value.trim();

            if (!titulo) {
                alert("O título da tarefa é obrigatório!");
                return;
            }

            fetch("../api/api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ titulo, descricao })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.mensagem);
                if (data.id) {
                    titleInput.value = "";
                    descInput.value = "";
                    carregarTarefas();
                }
            })
            .catch(error => console.error("Erro ao adicionar tarefa:", error));
        }

        function excluirTarefa(id) {
            if (!confirm("Tem certeza que deseja excluir esta tarefa?")) return;

            fetch(`../api/api.php?id=${id}`, { method: "DELETE" })
                .then(response => response.json())
                .then(data => {
                    alert(data.mensagem);
                    carregarTarefas();
                })
                .catch(error => console.error("Erro ao excluir tarefa:", error));
        }

        function atualizarTarefa(id, titulo, descricao, completado) {
            const novoTitulo = prompt("Novo título:", titulo);
            if (!novoTitulo) return;

            const novaDescricao = prompt("Nova descrição:", descricao);
            const novoStatus = confirm("Marcar como concluído?") ? 1 : 0;

            fetch(`../api/api.php?id=${id}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ titulo: novoTitulo, descricao: novaDescricao, completado: novoStatus })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.mensagem);
                carregarTarefas();
            })
            .catch(error => console.error("Erro ao atualizar tarefa:", error));
        }
    </script>

</body>
</html>
