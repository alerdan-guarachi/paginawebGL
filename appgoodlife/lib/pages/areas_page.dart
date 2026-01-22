import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'acciones_page.dart';
import 'proveedores_page.dart';
import '../widgets/good_life_loader.dart';


class AreasPage extends StatefulWidget {
  final String tipoArea; // 'ESTUDIO' o 'ESPECIALIDAD'
  final String sucursalUsuario;

  AreasPage({required this.tipoArea, required this.sucursalUsuario});

  @override
  _AreasPageState createState() => _AreasPageState();
}

class _AreasPageState extends State<AreasPage> {
  bool isLoading = true;
  List<dynamic> areas = [];
  List<dynamic> filteredAreas = []; // Lista filtrada para búsqueda
  final Color verde = Color(0xFF94C93B);
  final TextEditingController searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    fetchAreas();
    searchController.addListener(_filterAreas);
  }

  @override
  void dispose() {
    searchController.removeListener(_filterAreas);
    searchController.dispose();
    super.dispose();
  }

  void fetchAreas() async {
    try {
      final data = await ApiService.getAreas(widget.tipoArea, widget.sucursalUsuario);

      // Orden alfabético
      data.sort((a, b) => a['area']
          .toString()
          .toLowerCase()
          .compareTo(b['area'].toString().toLowerCase()));

      setState(() {
        areas = data;
        filteredAreas = data;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text('Error al cargar áreas')));
    }
  }

  void _filterAreas() {
    final query = searchController.text.toLowerCase();
    setState(() {
      filteredAreas = areas
          .where((a) => a['area']?.toString().toLowerCase().contains(query) ?? false)
          .toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.tipoArea == 'ESTUDIO' ? 'ESTUDIOS' : 'ESPECIALIDADES'),
        backgroundColor: verde,
      ),
      body: isLoading
          ? GoodLifeLoader()
          : Column(
        children: [
          // Buscador
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: TextField(
              controller: searchController,
              decoration: InputDecoration(
                hintText: "Buscar área...",
                prefixIcon: Icon(Icons.search),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
          ),
          // Lista de áreas
          Expanded(
            child: filteredAreas.isEmpty
                ? Center(
              child: Text(
                "No hay áreas disponibles",
                style: TextStyle(fontSize: 16, color: Colors.grey),
              ),
            )
                : ListView.builder(
              itemCount: filteredAreas.length,
              itemBuilder: (_, index) {
                final area = filteredAreas[index];
                final nombreArea =
                    area['area']?.toString() ?? 'SIN NOMBRE';

                return Card(
                  elevation: 3,
                  margin: EdgeInsets.symmetric(
                      vertical: 6, horizontal: 8),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10)),
                  child: ListTile(
                    leading: CircleAvatar(
                      radius: 14, // <--- tamaño más pequeño
                      backgroundColor: verde,
                      child: Text(
                        nombreArea[0],
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 12, // ajustar el tamaño de la letra dentro del círculo
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),

                    title: Text(
                      nombreArea,
                      style: TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 14, // tamaño reducido
                      ),
                    ),
                    trailing:
                    Icon(Icons.arrow_forward_ios, size: 18),
                    onTap: () {
                      if (widget.tipoArea == 'ESTUDIO') {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => AccionesPage(
                              area: nombreArea,
                              sucursalUsuario:
                              widget.sucursalUsuario,
                            ),
                          ),
                        );
                      } else {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => ProveedoresPage(
                              area: nombreArea,
                              accion: null,
                              sucursalUsuario:
                              widget.sucursalUsuario,
                            ),
                          ),
                        );
                      }
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
