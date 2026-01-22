import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'proveedores_page.dart';
import '../widgets/good_life_loader.dart';


class AccionesPage extends StatefulWidget {
  final String area;
  final String sucursalUsuario;

  AccionesPage({required this.area, required this.sucursalUsuario});

  @override
  _AccionesPageState createState() => _AccionesPageState();
}

class _AccionesPageState extends State<AccionesPage> {
  bool isLoading = true;
  List<dynamic> acciones = [];
  List<dynamic> filteredAcciones = []; // Lista filtrada
  final Color verde = Color(0xFF94C93B);
  final TextEditingController searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    fetchAcciones();
    searchController.addListener(_filterAcciones);
  }

  @override
  void dispose() {
    searchController.removeListener(_filterAcciones);
    searchController.dispose();
    super.dispose();
  }

  void fetchAcciones() async {
    try {
      final data = await ApiService.getAcciones(widget.area, widget.sucursalUsuario);
      data.sort((a, b) => a['accion'].toString().toLowerCase().compareTo(
        b['accion'].toString().toLowerCase(),
      ));
      setState(() {
        acciones = data;
        filteredAcciones = data; // Inicialmente iguales
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text('No hay estudios disponibles')));
    }
  }

  void _filterAcciones() {
    final query = searchController.text.toLowerCase();
    setState(() {
      filteredAcciones = acciones
          .where((a) => a['accion']?.toString().toLowerCase().contains(query) ?? false)
          .toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.area), // <-- Muestra el nombre del área
        backgroundColor: verde,
      ),
      body: isLoading
          ? GoodLifeLoader()
          : Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: TextField(
              controller: searchController,
              decoration: InputDecoration(
                hintText: "Buscar estudio...",
                prefixIcon: Icon(Icons.search),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
          ),
          Expanded(
            child: filteredAcciones.isEmpty
                ? Center(
              child: Text(
                "No hay estudios disponibles",
                style: TextStyle(fontSize: 16, color: Colors.grey),
              ),
            )
                : ListView.builder(
              itemCount: filteredAcciones.length,
              itemBuilder: (_, index) {
                final accion = filteredAcciones[index];
                final nombreAccion =
                    accion['accion']?.toString() ?? 'SIN NOMBRE';
                return Card(
                  elevation: 3,
                  margin:
                  EdgeInsets.symmetric(vertical: 6, horizontal: 4),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12)),
                  child: ListTile(
                    leading: CircleAvatar(
                      radius: 16, // tamaño más pequeño del círculo
                      backgroundColor: verde,
                      child: Text(
                        nombreAccion[0],
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 14, // tamaño de la letra dentro del círculo
                          fontWeight: FontWeight.w600, // menos grueso que bold
                        ),
                      ),
                    ),

                    title: Text(nombreAccion,
                        style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600)),
                    trailing:
                    Icon(Icons.arrow_forward_ios, size: 18),
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => ProveedoresPage(
                            area: widget.area,
                            accion: nombreAccion,
                            sucursalUsuario:
                            widget.sucursalUsuario,
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
