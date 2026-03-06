import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';
import 'proveedor_detalle_page.dart';
import 'package:intl/intl.dart';

class ProgramacionesPage extends StatefulWidget {
  final String usuarioId;

  const ProgramacionesPage({Key? key, required this.usuarioId}) : super(key: key);

  @override
  _ProgramacionesPageState createState() => _ProgramacionesPageState();
}

class _ProgramacionesPageState extends State<ProgramacionesPage> {
  late Future<Map<String, dynamic>> _dataFuture;
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    _dataFuture = ApiService.getBaterias(widget.usuarioId);
  }

  void _confirmar(String areaNombre, String decision) async {
    try {
      await ApiService.confirmarAsistencia(
        areaNombre: areaNombre,
        decision: decision,
        usuarioId: widget.usuarioId,
      );
      setState(() {
        _dataFuture = ApiService.getBaterias(widget.usuarioId);
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Respuesta enviada: $decision'), backgroundColor: verde),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error al enviar respuesta'), backgroundColor: Colors.red),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Programaciones Médicas'),
        backgroundColor: verde,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _dataFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: GoodLifeLoader());
          } else if (snapshot.hasError) {
            return Center(child: Text('Error al cargar las programaciones.'));
          } else if (!snapshot.hasData) {
            return Center(child: Text('No se pudo obtener la información.'));
          } else {
            final List<dynamic> baterias = snapshot.data!['baterias'] ?? [];
            final bool isBlocked = snapshot.data!['is_blocked'] ?? false;

            if (baterias.isEmpty) {
              return Center(child: Text('No tiene programaciones médicas pendientes.'));
            }

            return Column(
              children: [
                if (isBlocked)
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(12),
                    margin: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: Colors.red.shade100,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: Colors.red.shade400),
                    ),
                    child: Row(
                      children: [
                        Icon(Icons.report_problem, color: Colors.red.shade800),
                        SizedBox(width: 12),
                        Expanded(
                          child: Text(
                            'HAS SIDO BLOQUEADO POR REPROGRAMACIONES ESCESIVAS',
                            style: TextStyle(
                              color: Colors.red.shade900,
                              fontWeight: FontWeight.bold,
                              fontSize: 13,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                Expanded(
                  child: ListView.builder(
                    padding: const EdgeInsets.all(8.0),
                    itemCount: baterias.length,
                    itemBuilder: (context, index) {
                      final bateria = baterias[index];
                      final areaNombre = bateria['areanombre'] ?? 'Área sin nombre';
                      final accionId = bateria['accionid']?.toString();
                      final bateriaId = bateria['id']?.toString();
                      final accionesDetalle = bateria['acciones_detalle']?.toString();
                      final precioTotal = bateria['precio_total']?.toString();
                      final confAsistencia = bateria['confasistencia']; // Campo de la DB

                      final bool isProgramado = bateria['fechaasignada'] != null;

                      // ▼▼ LÓGICA DE TIEMPO PARA CONFIRMACIÓN ▼▼
                      bool mostrarBotones = false;
                      if (isProgramado && confAsistencia == null) {
                        try {
                          final String fechaStr = bateria['fechaasignada'];
                          final String horaStr = bateria['horadesde'];
                          final DateTime citaDateTime = DateTime.parse('$fechaStr $horaStr');
                          final DateTime now = DateTime.now();
                          final difference = citaDateTime.difference(now).inMinutes;

                          // Mostrar botones si falta 1 hora (60 min) o menos, pero la cita aún no ha pasado
                          if (difference <= 60 && difference > 0) {
                            mostrarBotones = true;
                          }
                        } catch (e) {}
                      }

                      return Card(
                        elevation: 2,
                        margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 8),
                        color: isProgramado ? Colors.green.shade50 : Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Column(
                          children: [
                            ListTile(
                              title: Text(
                                areaNombre,
                                style: TextStyle(
                                  fontWeight: isProgramado ? FontWeight.bold : FontWeight.w600,
                                  fontSize: 15,
                                  color: isBlocked && !isProgramado ? Colors.black54 : Colors.black,
                                ),
                              ),
                              subtitle: isProgramado
                                  ? Builder(
                                builder: (context) {
                                  String fechaFormateada = bateria['fechaasignada'] ?? '';
                                  String hora = (bateria['horadesde']?.length ?? 0) >= 5
                                      ? bateria['horadesde'].substring(0, 5)
                                      : '--:--';

                                  try {
                                    final fechaDate = DateTime.parse(bateria['fechaasignada']);
                                    fechaFormateada = DateFormat("d 'de' MMMM 'del' y", 'es_ES').format(fechaDate);
                                  } catch (e) {}

                                  return Padding(
                                    padding: const EdgeInsets.only(top: 4.0),
                                    child: Text(
                                      'Programado para el: $fechaFormateada a las $hora',
                                      style: TextStyle(
                                        color: Colors.green.shade800,
                                        fontWeight: FontWeight.bold,
                                        fontSize: 13,
                                      ),
                                    ),
                                  );
                                },
                              )
                                  : Text(
                                'Pendiente de programación',
                                style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                              ),
                              leading: Icon(
                                isProgramado ? Icons.event_available : Icons.calendar_today,
                                color: isProgramado ? Colors.green.shade700 : (isBlocked ? Colors.grey : verde),
                              ),
                              trailing: isBlocked && !isProgramado
                                  ? Icon(Icons.lock, size: 18, color: Colors.grey)
                                  : Icon(Icons.arrow_forward_ios, size: 16),
                              onTap: (accionId == null || bateriaId == null || isBlocked)
                                  ? (isBlocked ? () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text('Acceso restringido por reprogramaciones excesivas.'),
                                    backgroundColor: Colors.red.shade700,
                                  ),
                                );
                              } : null)
                                  : () async {
                                final result = await Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => ProveedorDetallePage(
                                      accionId: accionId,
                                      bateriaId: bateriaId,
                                      accionNombre: areaNombre,
                                      accionesDetalle: accionesDetalle,
                                      precioTotal: precioTotal,
                                      fechaProgramada: bateria['fechaasignada'],
                                      horaDesdeProgramada: bateria['horadesde'],
                                      horaHastaProgramada: bateria['horahasta'],
                                    ),
                                  ),
                                );

                                if (result == true) {
                                  setState(() {
                                    _dataFuture = ApiService.getBaterias(widget.usuarioId);
                                  });
                                }
                              },
                            ),

                            // ▼▼▼ BOTONES DE CONFIRMACIÓN ▼▼▼
                            if (mostrarBotones)
                              Padding(
                                padding: const EdgeInsets.only(bottom: 12.0, left: 16, right: 16),
                                child: Column(
                                  children: [
                                    Divider(),
                                    Text(
                                      '¿Asistirá a su cita programada?',
                                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.black),
                                    ),
                                    SizedBox(height: 10),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                                      children: [
                                        ElevatedButton(
                                          onPressed: () => _confirmar(areaNombre, 'SI'),
                                          style: ElevatedButton.styleFrom(backgroundColor: Color(0xFF94C93B), foregroundColor: Colors.white),
                                          child: Text('SÍ, ASISTIRÉ'),
                                        ),
                                        ElevatedButton(
                                          onPressed: () => _confirmar(areaNombre, 'NO'),
                                          style: ElevatedButton.styleFrom(backgroundColor: Colors.orange, foregroundColor: Colors.white),
                                          child: Text('NO PODRÉ IR'),
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                              ),

                            // Mostrar estado si ya respondió
                            if (confAsistencia != null)
                              Padding(
                                padding: const EdgeInsets.only(bottom: 8.0),
                                child: Text(
                                  'Confirmación: $confAsistencia',
                                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey),
                                ),
                              ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
              ],
            );
          }
        },
      ),
    );
  }
}
