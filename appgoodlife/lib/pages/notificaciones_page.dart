import 'package:flutter/material.dart';
import '../models/notificacion.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';

class NotificacionesPage extends StatefulWidget {
  final String usuarioId;

  const NotificacionesPage({Key? key, required this.usuarioId}) : super(key: key);

  @override
  State<NotificacionesPage> createState() => _NotificacionesPageState();
}

class _NotificacionesPageState extends State<NotificacionesPage> {
  bool isLoading = true;
  List<Notificacion> _notificaciones = [];
  String? _error;
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    _cargarNotificaciones();
  }

  Future<void> _cargarNotificaciones() async {
    if (!mounted) return;
    try {
      final data = await ApiService.getNotificaciones(widget.usuarioId);
      List<Notificacion> lista = [];
      for (var n in data['no_leidas']) {
        lista.add(Notificacion.fromJson(n));
      }
      for (var n in data['leidas']) {
        lista.add(Notificacion.fromJson(n));
      }

      if (mounted) {
        setState(() {
          _notificaciones = lista;
          isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = e.toString();
          isLoading = false;
        });
      }
    }
  }

  // Función corregida que no muta el estado, sino que lo reemplaza.
  Future<void> _marcarComoLeida(Notificacion notificacion) async {
    if (notificacion.leida) return;

    final int index = _notificaciones.indexOf(notificacion);
    if (index == -1) return;

    final notificacionActualizada = Notificacion(
      id: notificacion.id,
      titulo: notificacion.titulo,
      mensaje: notificacion.mensaje,
      fecha: notificacion.fecha,
      leida: true,
    );

    setState(() {
      _notificaciones[index] = notificacionActualizada;
    });

    try {
      await ApiService.marcarLeida(notificacion.id);
    } catch (e) {
      setState(() {
        _notificaciones[index] = notificacion;
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error al marcar la notificación.')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Notificaciones'), backgroundColor: verde),
      body: isLoading
          ? GoodLifeLoader()
          : _buildBody(),
    );
  }

  // ▼▼▼ DISEÑO RESTAURADO AL DE PANEL EXPANDIBLE (ExpansionTile) ▼▼▼
  Widget _buildBody() {
    if (_error != null) {
      return Center(child: Text('Error: $_error'));
    }

    if (_notificaciones.isEmpty) {
      return const Center(child: Text('No hay notificaciones'));
    }

    return ListView.builder(
      padding: const EdgeInsets.symmetric(vertical: 8.0, horizontal: 12.0),
      itemCount: _notificaciones.length,
      itemBuilder: (context, index) {
        final n = _notificaciones[index];

        return Card(
          elevation: 2,
          margin: const EdgeInsets.symmetric(vertical: 6),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          clipBehavior: Clip.antiAlias, // Evita que el color de fondo se salga
          child: ExpansionTile(
            key: PageStorageKey(n.id), // Mantiene el estado de expansión
            backgroundColor: Colors.white,
            collapsedBackgroundColor: n.leida ? Colors.white : Colors.green.shade50,
            title: Text(
              n.titulo,
              style: TextStyle(
                fontWeight: n.leida ? FontWeight.normal : FontWeight.bold,
                color: Colors.black87,
              ),
            ),
            leading: CircleAvatar(
              radius: 20,
              backgroundColor: n.leida ? Colors.grey.shade300 : verde,
              child: Icon(
                n.leida ? Icons.drafts_outlined : Icons.notifications_active,
                color: n.leida ? Colors.grey.shade700 : Colors.white,
              ),
            ),
            onExpansionChanged: (isExpanding) {
              if (isExpanding) {
                _marcarComoLeida(n);
              }
            },
            children: [
              Padding(
                padding: const EdgeInsets.only(left: 16, right: 16, bottom: 16, top: 8),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      n.mensaje,
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey.shade800,
                        height: 1.4,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        Icon(Icons.access_time, size: 14, color: Colors.grey),
                        const SizedBox(width: 4),
                        // ▼▼▼ LÍNEA MODIFICADA PARA USAR UN GUIÓN ▼▼▼
                        Text(
                          '${n.fecha.day.toString().padLeft(2, '0')}/${n.fecha.month.toString().padLeft(2, '0')}/${n.fecha.year.toString().substring(2)} - ${n.fecha.hour.toString().padLeft(2, '0')}:${n.fecha.minute.toString().padLeft(2, '0')}',
                          style: const TextStyle(fontSize: 12, color: Colors.grey),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
