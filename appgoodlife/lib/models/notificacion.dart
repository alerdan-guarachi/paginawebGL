class Notificacion {
  final String id;
  final String titulo;
  final String mensaje;
  final bool leida;
  final DateTime fecha;

  Notificacion({
    required this.id,
    required this.titulo,
    required this.mensaje,
    required this.leida,
    required this.fecha,
  });

  factory Notificacion.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>;

    return Notificacion(
      id: json['id'],
      titulo: data['titulo'] ?? 'Notificación',
      mensaje: data['mensaje'] ?? '',
      leida: json['read_at'] != null,
      fecha: DateTime.parse(json['created_at']),
    );
  }

}
