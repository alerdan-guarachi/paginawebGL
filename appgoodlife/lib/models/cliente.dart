class Cliente {
  final int id;
  final String nombrecompleto;
  final String genero;
  final String email;

  Cliente({
    required this.id,
    required this.nombrecompleto,
    required this.genero,
    required this.email,
  });

  factory Cliente.fromJson(Map<String, dynamic> json) {
    return Cliente(
      id: json['id'],
      nombrecompleto: json['nombrecompleto'],
      genero: json['genero'],
      email: json['email'],
    );
  }
}
