import 'package:flutter/material.dart';
import 'api_service.dart';
import 'todo_model.dart';
import 'add_edit_task_screen.dart';

class TodoListScreen extends StatefulWidget {
  @override
  _TodoListScreenState createState() => _TodoListScreenState();
}

class _TodoListScreenState extends State<TodoListScreen> {
  final ApiService apiService = ApiService();
  late Future<List<Todo>> futureTodos;

  @override
  void initState() {
    super.initState();
    futureTodos = apiService.fetchTodos();
  }

  void refreshTodos() {
    setState(() {
      futureTodos = apiService.fetchTodos();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Lista de Tarefas")),
      body: FutureBuilder<List<Todo>>(
        future: futureTodos,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return Center(
              child: Text(
                "Erro ao carregar: ${snapshot.error}",
                style: TextStyle(color: Colors.red, fontSize: 16),
                textAlign: TextAlign.center,
              ),
            );
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text("Nenhuma tarefa encontrada"));
          }

          List<Todo> todos = snapshot.data!;
          return ListView.builder(
            itemCount: todos.length,
            itemBuilder: (context, index) {
              final todo = todos[index];
              return ListTile(
                title: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(todo.titulo),
                    Text(
                      todo.completado ? "Concluída" : "Não Concluída",
                      style: TextStyle(
                        color: todo.completado ? Colors.green : Colors.red,
                      ),
                    ),
                  ],
                ),
                leading: Icon(
                  todo.completado
                      ? Icons.check_circle
                      : Icons.radio_button_unchecked,
                  color: todo.completado ? Colors.green : Colors.red,
                ),
                subtitle: Text(todo.descricao),
                trailing: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    IconButton(
                      icon: Icon(Icons.edit),
                      onPressed: () async {
                        await Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => AddEditTaskScreen(todo: todo),
                          ),
                        );
                        refreshTodos();
                      },
                    ),
                    IconButton(
                      icon: Icon(Icons.delete),
                      onPressed: () async {
                        bool? confirmDelete = await showDialog(
                          context: context,
                          builder:
                              (context) => AlertDialog(
                                title: Text("Excluir Tarefa"),
                                content: Text(
                                  "Você tem certeza que deseja excluir esta tarefa?",
                                ),
                                actions: [
                                  TextButton(
                                    onPressed: () {
                                      Navigator.of(context).pop(false);
                                    },
                                    child: Text("Cancelar"),
                                  ),
                                  TextButton(
                                    onPressed: () {
                                      Navigator.of(context).pop(true);
                                    },
                                    child: Text("Excluir"),
                                  ),
                                ],
                              ),
                        );

                        if (confirmDelete != null && confirmDelete) {
                          await apiService.deleteTodo(todo.id);
                          refreshTodos();
                        }
                      },
                    ),
                  ],
                ),
              );
            },
          );
        },
      ),
      floatingActionButton: FloatingActionButton(
        child: Icon(Icons.add),
        onPressed: () async {
          await Navigator.push(
            context,
            MaterialPageRoute(builder: (context) => AddEditTaskScreen()),
          );
          refreshTodos();
        },
      ),
    );
  }
}
