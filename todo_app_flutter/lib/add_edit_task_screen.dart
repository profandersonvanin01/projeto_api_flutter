import 'package:flutter/material.dart';
import 'api_service.dart';
import 'todo_model.dart';

class AddEditTaskScreen extends StatefulWidget {
  final Todo? todo;

  const AddEditTaskScreen({super.key, this.todo});

  @override
  _AddEditTaskScreenState createState() => _AddEditTaskScreenState();
}

class _AddEditTaskScreenState extends State<AddEditTaskScreen> {
  // final _formKey = GlobalKey<FormState>();
  final ApiService apiService = ApiService();

  late TextEditingController _titleController;
  late TextEditingController _descController;
  bool _completed = false;

  @override
  void initState() {
    super.initState();
    _titleController = TextEditingController(text: widget.todo?.titulo ?? '');
    _descController = TextEditingController(text: widget.todo?.descricao ?? '');
    _completed = widget.todo?.completado ?? false;
  }

  void saveTask() async {
    Todo newTodo = Todo(
      id: widget.todo?.id ?? 0,
      titulo: _titleController.text,
      descricao: _descController.text,
      completado: _completed,
    );

    if (widget.todo == null) {
      await apiService.addTodo(newTodo);
    } else {
      await apiService.updateTodo(widget.todo!.id, newTodo);
    }

    // ignore: use_build_context_synchronously
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Nova Tarefa")),
      body: Column(
        children: [
          TextField(
            controller: _titleController,
            decoration: InputDecoration(labelText: "Título"),
          ),
          TextField(
            controller: _descController,
            decoration: InputDecoration(labelText: "Descrição"),
          ),
          ElevatedButton(onPressed: saveTask, child: Text("Salvar")),
        ],
      ),
    );
  }
}
