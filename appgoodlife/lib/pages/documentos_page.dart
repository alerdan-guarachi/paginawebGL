import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';
import 'pdf_viewer_page.dart';
import 'image_viewer_page.dart';

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

  void fetchDocumentos() async {
    try {
      final response = await ApiService.getDocumentosByUserId(widget.usuarioId);
      List<dynamic> data = List.from(response);

      Map<String, List<dynamic>> grouped = {};
      final now = DateTime.now();
      final sixMonthsAgo = DateTime(now.year, now.month - 6, now.day);

      for (var doc in data) {
        String groupKey;
        final createdAtString = doc['created_at'];

        if (createdAtString != null) {
          final createdAtDate = DateTime.tryParse(createdAtString);
          if (createdAtDate != null && createdAtDate.isBefore(sixMonthsAgo)) {
            groupKey = 'HISTORIA CLINICA';
          } else {
            groupKey = doc['fechabateria'] ?? 'Sin fecha';
          }
        } else {
          groupKey = doc['fechabateria'] ?? 'Sin fecha';
        }

        if (!grouped.containsKey(groupKey)) {
          grouped[groupKey] = [];
        }
        grouped[groupKey]!.add(doc);
      }

      if (mounted) {
        setState(() {
          documentos = data;
          documentosPorFecha = grouped;
          isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error al cargar documentos: $e')),
        );
      }
    }
  }

  Widget _buildArchivoRow(Map<String, dynamic> doc, String label, String? value) {
    if (value == null || value.toString().isEmpty || value.toString() == "null") return SizedBox();

    final clienteId = doc['clienteitaid'];
    final url =
        "https://api.goodlife.com.bo/documentacionclientesita/$clienteId/$value";

    IconData icon;
    bool isImage = false;
    bool isPdf = value.toLowerCase().endsWith('.pdf');

    if (label.contains('Imagen') || value.toLowerCase().endsWith('.jpg') || value.toLowerCase().endsWith('.png') || value.toLowerCase().endsWith('.jpeg')) {
      icon = Icons.image;
      isImage = true;
    } else if (label.contains('Informe') || label.contains('firmado') || isPdf) {
      icon = Icons.picture_as_pdf;
    } else {
      icon = Icons.insert_drive_file;
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
                      return Colors.orange;
                    }
                    return verde;
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
                if (isPdf) {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => PdfViewerPage(url: url, title: label),
                    ),
                  );
                } else if (isImage) {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => ImageViewerPage(url: url, title: label),
                    ),
                  );
                } else {
                  final uri = Uri.parse(url);
                  if (!await launchUrl(uri, mode: LaunchMode.inAppWebView)) {
                    if(mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text('No se puede abrir el archivo')),
                      );
                    }
                  }
                }
              },
              child: Row(
                children: [
                  Icon(Icons.remove_red_eye, size: 14, color: Colors.white),
                  SizedBox(width: 4),
                  Text(
                    "Ver",
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
    final sortedEntries = documentosPorFecha.entries.toList()
      ..sort((a, b) {
        if (a.key == 'HISTORIA CLINICA') return 1;
        if (b.key == 'HISTORIA CLINICA') return -1;
        try {
          return b.key.compareTo(a.key);
        } catch (e) {
          return 0;
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
                // Lógica de prioridad: documentfirmado > document
                String? archivoInforme;
                if (doc['documentfirmado'] != null && doc['documentfirmado'].toString().isNotEmpty && doc['documentfirmado'].toString() != "null") {
                  archivoInforme = doc['documentfirmado'];
                } else if (doc['document'] != null && doc['document'].toString().isNotEmpty && doc['document'].toString() != "null") {
                  archivoInforme = doc['document'];
                }

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
                      if (doc['created_at'] != null)
                        Builder(builder: (context) {
                          try {
                            final date = DateTime.parse(doc['created_at']);
                            final formattedDate = '${date.day.toString().padLeft(2, '0')}/${date.month.toString().padLeft(2, '0')}/${date.year.toString().substring(2)} - ${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 8.0),
                              child: Text(
                                'Registrado: $formattedDate',
                                style: TextStyle(
                                    fontSize: 11,
                                    fontStyle: FontStyle.italic,
                                    color: Colors.grey.shade700),
                              ),
                            );
                          } catch (e) {
                            return SizedBox.shrink();
                          }
                        }),
                      // Solo muestra la fila si archivoInforme tiene algo
                      if (archivoInforme != null)
                        _buildArchivoRow(doc, 'Informe', archivoInforme),

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
