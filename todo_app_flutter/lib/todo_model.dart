class Todo {
  int id;
  String titulo;
  String descricao;
  bool completado;

  Todo({
    required this.id,
    required this.titulo,
    required this.descricao,
    required this.completado,
  });

  factory Todo.fromJson(Map<String, dynamic> json) {
    return Todo(
      id: json['id'],
      titulo: json['titulo'],
      descricao: json['descricao'] ?? '',
      completado: json['completado'] == 1,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'titulo': titulo,
      'descricao': descricao,
      'completado': completado ? 1 : 0,
    };
  }
}
