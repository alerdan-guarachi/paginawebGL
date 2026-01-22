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

  void fetchDocumentos() async {
    try {
      final response = await ApiService.getDocumentosByUserId(widget.usuarioId);
      List<dynamic> data = List.from(response);

      // Agrupar por fechabateria
      Map<String, List<dynamic>> grouped = {};
      for (var doc in data) {
        final fecha = doc['fechabateria'] ?? 'Sin fecha';
        if (!grouped.containsKey(fecha)) grouped[fecha] = [];
        grouped[fecha]!.add(doc);
      }

      setState(() {
        documentos = data;
        documentosPorFecha = grouped;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error al cargar documentos: $e')),
      );
    }
  }

  // MÉTODO CORRECTO
  Widget _buildArchivoRow(Map<String, dynamic> doc, String label, String? value) {
    if (value == null || value.isEmpty) return SizedBox();

    final clienteId = doc['clienteitaid'];
    final url =
        "http://192.168.0.20:8000/documentacionclientesita/$clienteId/$value";

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
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('No se puede abrir el archivo')),
                  );
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
    return Scaffold(
      appBar: AppBar(
        title: Text('Informes Médicos'),
        backgroundColor: verde,
      ),
      body: isLoading
          ? Center(child: GoodLifeLoader())
          : documentosPorFecha.isEmpty
          ? Center(child: Text('No hay documentos'))
          : ListView(
        children: documentosPorFecha.entries.map((entry) {
          final fecha = entry.key;
          final docs = entry.value;

          return Card(
            margin: EdgeInsets.all(8),
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
                side: BorderSide(color: Colors.grey.shade300)),
            elevation: 3,
            color: Colors.white, // fondo blanco neutro
            child: ExpansionTile(
              tilePadding: EdgeInsets.symmetric(horizontal: 16),
              collapsedBackgroundColor: Colors.white,
              backgroundColor: Colors.white,
              title: Text('Fecha Batería: $fecha',
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
                      _buildArchivoRow(doc, 'Informe', doc['document']),
                      _buildArchivoRow(doc, 'Informe firmado', doc['documentfirmado']),
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
