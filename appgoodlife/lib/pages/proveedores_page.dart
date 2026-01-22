import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';

class ProveedoresPage extends StatefulWidget {
  final String area;
  final String? accion;
  final String sucursalUsuario;

  ProveedoresPage({required this.area, this.accion, required this.sucursalUsuario});

  @override
  _ProveedoresPageState createState() => _ProveedoresPageState();
}

class _ProveedoresPageState extends State<ProveedoresPage> {
  bool isLoading = true;
  List<dynamic> proveedores = [];
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    fetchProveedores();
  }

  void fetchProveedores() async {
    try {
      final data = await ApiService.getProveedores(widget.area, accion: widget.accion, sucursal: widget.sucursalUsuario);
      data.sort((a, b) => a['proveedor'].toString().toLowerCase().compareTo(b['proveedor'].toString().toLowerCase()));
      setState(() {
        proveedores = data;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('No hay proveedores disponibles')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("MÉDICOS"), backgroundColor: verde),
      body: isLoading
          ? GoodLifeLoader()
          : proveedores.isEmpty
          ? Center(child: Text("No hay proveedores disponibles", style: TextStyle(fontSize: 16, color: Colors.grey)))
          : ListView.builder(
        padding: EdgeInsets.all(8),
        itemCount: proveedores.length,
        itemBuilder: (_, index) {
          final p = proveedores[index];
          final nombre = p['proveedor']?.toString() ?? 'SIN NOMBRE';
          final horarioIni = p['horarioinicial'] ?? 'No definido';
          final horarioFin = p['horariofinal'] ?? 'No definido';

          return Card(
            elevation: 3,
            margin: EdgeInsets.symmetric(vertical: 6, horizontal: 4),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            child: Padding(
              padding: const EdgeInsets.all(8.0),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Círculo con inicial
                  CircleAvatar(
                    radius: 16,
                    backgroundColor: verde,
                    child: Text(
                      nombre[0],
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  SizedBox(width: 10),

                  // Texto principal y horario
                  Expanded(
                    child: Stack(
                      children: [
                        // Column con nombre, horario y tiempo
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              nombre,
                              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                            ),
                            SizedBox(height: 2),
                            Text(
                              "Horario: $horarioIni - $horarioFin",
                              style: TextStyle(fontSize: 12, color: Colors.grey[800]),
                            ),
                            SizedBox(height: 2),
                            Text(
                              "Tiempo de atención: ${p['tiempoatencion'] ?? 'No definido'}",
                              style: TextStyle(fontSize: 12, color: Colors.grey[800]),
                            ),
                            SizedBox(height: 20), // espacio para que el precio no se sobreponga
                          ],
                        ),

                        // Precio en la esquina inferior derecha
                        Positioned(
                          bottom: 0,
                          right: 0,
                          child: Container(
                            padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: Colors.orange[400],
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              "Bs. ${p['precio'] ?? '0.00'}",
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Colors.black,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          );

        },
      ),
    );
  }
}
