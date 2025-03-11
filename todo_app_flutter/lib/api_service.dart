import 'dart:convert';
import 'package:http/http.dart' as http;
import 'todo_model.dart';

class ApiService {
  // static const String apiUrl = "http://10.0.2.2/projeto_api_flutter/api/api.php";
  static const String apiUrl =
      "http://192.168.0.29/projeto_api_flutter/api/api.php";

  Future<List<Todo>> fetchTodos() async {
    try {
      final response = await http.get(Uri.parse(apiUrl));

      if (response.statusCode == 200) {
        List<dynamic> data = json.decode(response.body);
        //print("Sem erros na API");
        return data.map((json) => Todo.fromJson(json)).toList();
      } else {
        //print("Erro na API: ${response.statusCode}");
        throw Exception("Erro ao carregar tarefas");
      }
    } catch (e) {
      print("Erro de conexão: $e");
      throw Exception("Erro ao conectar com a API");
    }
  }

  Future<void> addTodo(Todo todo) async {
    final response = await http.post(
      Uri.parse(apiUrl),
      headers: {"Content-Type": "application/json"},
      body: json.encode(todo.toJson()),
    );
    if (response.statusCode != 201) {
      throw Exception("Erro ao adicionar tarefa");
    }
  }

  Future<void> updateTodo(int id, Todo todo) async {
    final response = await http.put(
      Uri.parse("$apiUrl?id=$id"),
      headers: {"Content-Type": "application/json"},
      body: json.encode(todo.toJson()),
    );
    if (response.statusCode != 200) {
      throw Exception("Erro ao atualizar tarefa");
    }
  }

  Future<void> deleteTodo(int id) async {
    final response = await http.delete(Uri.parse("$apiUrl?id=$id"));
    if (response.statusCode != 200) {
      throw Exception("Erro ao excluir tarefa");
    }
  }
}
