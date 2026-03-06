import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';

class AusenciasPage extends StatefulWidget {
  const AusenciasPage({Key? key}) : super(key: key);

  @override
  _AusenciasPageState createState() => _AusenciasPageState();
}

class _AusenciasPageState extends State<AusenciasPage> {
  late Future<List<dynamic>> _ausenciasFuture;
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    _ausenciasFuture = ApiService.getAusencias();
  }

  // ▼▼▼ FUNCIÓN MODIFICADA ▼▼▼
  Map<String, dynamic> _groupAusencias(List<dynamic> ausencias) {
    final Map<String, dynamic> grouped = {};

    for (var ausencia in ausencias) {
      final key = '${ausencia['proveedornombre']}_${ausencia['motivo']}';
      if (!grouped.containsKey(key)) {
        grouped[key] = {
          'proveedornombre': ausencia['proveedornombre'],
          'motivo': ausencia['motivo'],
          'fechas': <DateTime>[], // Se especifica el tipo de la lista
          'horainicio': ausencia['horainicio'],
          'horafin': ausencia['horafin'],
        };
      }
      grouped[key]['fechas'].add(DateTime.parse(ausencia['fecha']));
    }

    // Ordenar las fechas dentro de cada grupo
    grouped.forEach((key, value) {
      (value['fechas'] as List<DateTime>).sort();
    });

    return grouped;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Ausencias Médicos'),
        backgroundColor: verde,
      ),
      body: FutureBuilder<List<dynamic>>(
        future: _ausenciasFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: GoodLifeLoader());
          } else if (snapshot.hasError) {
            return Center(child: Text('Error al cargar las ausencias.'));
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text('No hay ausencias programadas.'));
          } else {
            final groupedAusencias = _groupAusencias(snapshot.data!);
            final ausenciasList = groupedAusencias.values.toList();

            return ListView.builder(
              padding: const EdgeInsets.all(8.0),
              itemCount: ausenciasList.length,
              itemBuilder: (context, index) {
                final ausencia = ausenciasList[index];
                final List<DateTime> fechas = ausencia['fechas'];
                // ▼▼▼ LÍNEAS MODIFICADAS ▼▼▼
                final String fechaInicio = DateFormat('dd/MM/yy', 'es_ES').format(fechas.first);
                final String fechaFin = DateFormat('dd/MM/yy', 'es_ES').format(fechas.last);
                final String rangoFechas = fechas.length == 1 ? fechaFin : 'Desde el $fechaInicio al $fechaFin';

                String? horaInicio, horaFin;
                if(ausencia['horainicio'] != null && (ausencia['horainicio'] as String).length >= 5) {
                  horaInicio = (ausencia['horainicio'] as String).substring(0, 5);
                }
                if(ausencia['horafin'] != null && (ausencia['horafin'] as String).length >= 5) {
                  horaFin = (ausencia['horafin'] as String).substring(0, 5);
                }

                return Card(
                  elevation: 2,
                  margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 8),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(12.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          ausencia['proveedornombre'] ?? 'Médico no especificado',
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                        ),
                        SizedBox(height: 2),
                        Text(
                          ausencia['motivo'] ?? 'Motivo no especificado',
                          style: TextStyle(fontStyle: FontStyle.italic, color: Colors.grey.shade700),
                        ),
                        Divider(height: 10),
                        Row(
                          children: [
                            Icon(Icons.calendar_today, size: 16, color: Colors.blue.shade700),
                            SizedBox(width: 8),
                            Expanded(
                              child: Text(rangoFechas, style: TextStyle(fontWeight: FontWeight.w500)),
                            ),
                          ],
                        ),
                        if (horaInicio != null && horaFin != null)
                          Padding(
                            padding: const EdgeInsets.only(top: 8.0),
                            child: Row(
                              children: [
                                Icon(Icons.timer_outlined, size: 16, color: Colors.orange.shade700),
                                SizedBox(width: 8),
                                Text('$horaInicio - $horaFin', style: TextStyle(fontWeight: FontWeight.w500)),
                              ],
                            ),
                          ),
                      ],
                    ),
                  ),
                );
              },
            );
          }
        },
      ),
    );
  }
}
