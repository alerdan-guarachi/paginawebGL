import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';

class ProgramacionesPage extends StatefulWidget {
  final String usuarioId;

  const ProgramacionesPage({Key? key, required this.usuarioId}) : super(key: key);

  @override
  _ProgramacionesPageState createState() => _ProgramacionesPageState();
}

class _ProgramacionesPageState extends State<ProgramacionesPage> {
  late Future<List<dynamic>> _bateriasFuture;
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    _bateriasFuture = ApiService.getBaterias(widget.usuarioId);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Programaciones Médicas'),
        backgroundColor: verde,
      ),
      body: FutureBuilder<List<dynamic>>(
        future: _bateriasFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: GoodLifeLoader());
          } else if (snapshot.hasError) {
            return Center(child: Text('Error al cargar las programaciones.'));
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text('No tiene programaciones médicas pendientes.'));
          } else {
            final baterias = snapshot.data!;
            return ListView.builder(
              padding: const EdgeInsets.all(8.0),
              itemCount: baterias.length,
              itemBuilder: (context, index) {
                final bateria = baterias[index];
                final accionNombre = bateria['accionnombre'] ?? 'Programación sin nombre';

                return Card(
                  elevation: 2,
                  margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 8),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: ListTile(
                    title: Text(
                      accionNombre,
                      style: TextStyle(fontWeight: FontWeight.w500),
                    ),
                    leading: Icon(Icons.calendar_today, color: verde),
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
