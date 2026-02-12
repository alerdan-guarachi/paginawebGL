import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';

class DocumentosPage extends StatefulWidget {
  final String usuarioId;

  DocumentosPage({required this.usuarioId});

  @override
  _DocumentosPageState createState() => _DocumentosPageState();
}

class _DocumentosPageState extends State<DocumentosPage> {
  bool isLoading = true;
  List<dynamic> documentos = [];
  Map<String, List<dynamic>> documentosPorFecha = {};
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    fetchDocumentos();
  }

  // ▼▼▼ FUNCIÓN MODIFICADA PARA AGRUPAR POR FECHA O HISTORIA CLÍNICA ▼▼▼
  void fetchDocumentos() async {
    try {
      final response = await ApiService.getDocumentosByUserId(widget.usuarioId);
      List<dynamic> data = List.from(response);

      // Agrupar por fecha o "HISTORIA CLINICA"
      Map<String, List<dynamic>> grouped = {};
      final now = DateTime.now();
      // Calculamos la fecha de hace 6 meses
      final sixMonthsAgo = DateTime(now.year, now.month - 6, now.day);

      for (var doc in data) {
        String groupKey;
        final createdAtString = doc['created_at'];

        // Si el documento tiene fecha de creación, la evaluamos
        if (createdAtString != null) {
          final createdAtDate = DateTime.tryParse(createdAtString);
          if (createdAtDate != null && createdAtDate.isBefore(sixMonthsAgo)) {
            groupKey = 'HISTORIA CLINICA';
          } else {
            // Si es reciente, se agrupa por fechabateria
            groupKey = doc['fechabateria'] ?? 'Sin fecha';
          }
        } else {
          // Si no tiene created_at, se usa la lógica antigua
          groupKey = doc['fechabateria'] ?? 'Sin fecha';
        }

        if (!grouped.containsKey(groupKey)) {
          grouped[groupKey] = [];
        }
        grouped[groupKey]!.add(doc);
      }

      setState(() {
        documentos = data; // Aún mantenemos la lista original si se necesita
        documentosPorFecha = grouped;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error al cargar documentos: $e')),
        );
      }
    }
  }

  // MÉTODO CORRECTO
  Widget _buildArchivoRow(Map<String, dynamic> doc, String label, String? value) {
    if (value == null || value.isEmpty) return SizedBox();

    final clienteId = doc['clienteitaid'];
    final url =
        "http://192.168.88.224:8000/documentacionclientesita/$clienteId/$value";

    IconData icon;
    if (label.contains('Imagen')) {
      icon = Icons.image;
    } else if (label.contains('firmado')) {
      icon = Icons.edit_document;
    } else {
      icon = Icons.picture_as_pdf;
    }

    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 3),
      child: Row(
        children: [
          Icon(icon, size: 18, color: Colors.black87),
          SizedBox(width: 8),
          Expanded(
            child: Text(
              label,
              style: TextStyle(fontSize: 13, fontWeight: FontWeight.w500),
            ),
          ),
          SizedBox(
            height: 28,
            child: ElevatedButton(
              style: ButtonStyle(
                backgroundColor: MaterialStateProperty.resolveWith<Color>(
                      (Set<MaterialState> states) {
                    if (states.contains(MaterialState.pressed)) {
                      return Colors.orange; // color cuando se presiona
                    }
                    return verde; // color normal
                  },
                ),
                foregroundColor: MaterialStateProperty.all<Color>(Colors.white),
                padding: MaterialStateProperty.all<EdgeInsets>(
                    EdgeInsets.symmetric(horizontal: 10)),
                minimumSize: MaterialStateProperty.all<Size>(Size(10, 28)),
                shape: MaterialStateProperty.all<RoundedRectangleBorder>(
                  RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
              ),
              onPressed: () async {
                final uri = Uri.parse(url);
                if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
                  if(mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('No se puede abrir el archivo')),
                    );
                  }
                }
              },
              child: Row(
                children: [
                  Icon(Icons.open_in_new, size: 14, color: Colors.white),
                  SizedBox(width: 4),
                  Text(
                    "Abrir",
                    style: TextStyle(fontSize: 12, color: Colors.white),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }


  @override
  Widget build(BuildContext context) {
    // ▼▼▼ WIDGET BUILD MODIFICADO PARA ORDENAR LOS GRUPOS ▼▼▼

    // Ordenar las claves: Primero las fechas (más recientes primero), y al final "HISTORIA CLINICA"
    final sortedEntries = documentosPorFecha.entries.toList()
      ..sort((a, b) {
        if (a.key == 'HISTORIA CLINICA') return 1; // Mueve 'HISTORIA CLINICA' al final
        if (b.key == 'HISTORIA CLINICA') return -1;
        // Ordena las fechas de más reciente a más antigua
        try {
          // Asume que las claves son fechas en formato YYYY-MM-DD
          return b.key.compareTo(a.key);
        } catch (e) {
          return 0; // No hacer nada si no se pueden comparar
        }
      });

    return Scaffold(
      appBar: AppBar(
        title: Text('Informes Médicos'),
        backgroundColor: verde,
      ),
      body: isLoading
          ? Center(child: GoodLifeLoader())
          : sortedEntries.isEmpty
          ? Center(child: Text('No hay documentos'))
          : ListView(
        children: sortedEntries.map((entry) {
          final fecha = entry.key;
          final docs = entry.value;

          // Se ajusta el título dinámicamente
          final titleText = fecha == 'HISTORIA CLINICA' ? fecha : 'INFORMES: $fecha';

          return Card(
            margin: EdgeInsets.all(8),
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
                side: BorderSide(color: Colors.grey.shade300)),
            elevation: 3,
            color: Colors.white,
            child: ExpansionTile(
              tilePadding: EdgeInsets.symmetric(horizontal: 16),
              collapsedBackgroundColor: Colors.white,
              backgroundColor: Colors.white,
              title: Text(titleText,
                  style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Colors.black)),
              children: docs.map((doc) {
                return Padding(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 16, vertical: 3),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('${doc['accion'] ?? 'N/A'}',
                          style:
                          TextStyle(fontWeight: FontWeight.bold)),
                      SizedBox(height: 4),
                      // ▼▼▼ LÍNEA AÑADIDA PARA MOSTRAR CREATED_AT ▼▼▼
                      if (doc['created_at'] != null)
                        Builder(builder: (context) {
                          try {
                            final date = DateTime.parse(doc['created_at']);
                            final formattedDate = '${date.day.toString().padLeft(2, '0')}/${date.month.toString().padLeft(2, '0')}/${date.year.toString().substring(2)} - ${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 1.0),
                              child: Text(
                                'Registrado el: $formattedDate',
                                style: TextStyle(
                                    fontSize: 11,
                                    fontStyle: FontStyle.italic,
                                    color: Colors.grey.shade700),
                              ),
                            );
                          } catch (e) {
                            return SizedBox.shrink(); // No mostrar nada si la fecha no es válida
                          }
                        }),
                      // _buildArchivoRow(doc, 'Informe', doc['document']),
                      _buildArchivoRow(doc, 'Informe', doc['documentfirmado']),
                      _buildArchivoRow(doc, 'Imagen 1', doc['image']),
                      _buildArchivoRow(doc, 'Imagen 2', doc['image2']),
                      Divider(),
                    ],
                  ),
                );
              }).toList(),
            ),
          );
        }).toList(),
      ),
    );
  }
}
