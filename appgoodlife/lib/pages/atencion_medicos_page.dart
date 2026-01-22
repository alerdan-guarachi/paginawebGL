import 'package:flutter/material.dart';
import 'areas_page.dart';
import '../widgets/good_life_loader.dart';

class AtencionMedicosPage extends StatelessWidget {
  final String sucursalUsuario;
  final Color verde = Color(0xFF94C93B);

  AtencionMedicosPage({required this.sucursalUsuario});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Atención Médicos"), backgroundColor: verde),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          children: [
            ElevatedButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => AreasPage(
                      tipoArea: "ESTUDIO",
                      sucursalUsuario: sucursalUsuario,
                    ),
                  ),
                );
              },
              style: ElevatedButton.styleFrom(minimumSize: Size(double.infinity, 50), backgroundColor: verde),
              child: Text("Estudios"),
            ),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => AreasPage(
                      tipoArea: "ESPECIALIDAD",
                      sucursalUsuario: sucursalUsuario,
                    ),
                  ),
                );
              },
              style: ElevatedButton.styleFrom(minimumSize: Size(double.infinity, 50), backgroundColor: verde),
              child: Text("Especialidades"),
            ),
          ],
        ),
      ),
    );
  }
}
