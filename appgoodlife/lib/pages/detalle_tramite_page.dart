import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';
import '../widgets/good_life_loader.dart';
import 'pdf_viewer_page.dart';
import 'image_viewer_page.dart';

class DetalleTramitePage extends StatefulWidget {
  final String tramiteId;
  final String nombreTramite;

  DetalleTramitePage({
    required this.tramiteId,
    required this.nombreTramite,
  });

  @override
  _DetalleTramitePageState createState() => _DetalleTramitePageState();
}

class _DetalleTramitePageState extends State<DetalleTramitePage> {
  bool cargando = true;
  List procedimientos = [];
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    cargarProcedimientos();
  }

  Future<void> cargarProcedimientos() async {
    final url = Uri.parse(
        "https://api.goodlife.com.bo/api/tramite/${widget.tramiteId}");
    final resp = await http.get(url);

    if (resp.statusCode == 200) {
      final data = jsonDecode(resp.body);
      setState(() {
        procedimientos = data["procedimientos"];
        cargando = false;
      });
    } else {
      setState(() {
        cargando = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("No se pudo cargar los procesos")),
      );
    }
  }

  String _nivelSub(Map p) {
    final nivel = p["nivelprocedimiento"] ?? "";
    final sub = p["subprocedimiento"] ?? "";
    if (nivel.isNotEmpty && sub.isNotEmpty) {
      return (nivel == sub) ? nivel : "$nivel - $sub";
    } else if (nivel.isNotEmpty) {
      return nivel;
    } else {
      return sub;
    }
  }

  void _abrirDocumento(Map p) async {
    final clienteId = p["clienteid"] ?? "";
    final tramite = p["tramite"] ?? "";
    final sub = p["nivelprocedimiento"] ?? "";
    final documento = p["document"] ?? "";

    final urlString = "https://api.goodlife.com.bo/tramitesclientesita/$clienteId/$tramite/$sub/$documento";

    final String label = "Documento";
    final bool isPdf = documento.toLowerCase().endsWith('.pdf');
    final bool isImage = documento.toLowerCase().endsWith('.jpg') ||
                       documento.toLowerCase().endsWith('.png') ||
                       documento.toLowerCase().endsWith('.jpeg');

    if (isPdf) {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => PdfViewerPage(url: urlString, title: label),
        ),
      );
    } else if (isImage) {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => ImageViewerPage(url: urlString, title: label),
        ),
      );
    } else {
      final url = Uri.parse(urlString);
      if (!await launchUrl(url, mode: LaunchMode.inAppWebView)) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('No se pudo abrir el documento')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: verde,
        title: Text(widget.nombreTramite),
      ),
      body: cargando
          ? Center(child: GoodLifeLoader())
          : procedimientos.isEmpty
          ? Center(child: Text("No hay procesos"))
          : ListView.builder(
        itemCount: procedimientos.length,
        itemBuilder: (context, i) {
          final p = procedimientos[i];
          final fecha = p["fechasubida"] ?? "Sin fecha";
          final documento = p["document"];

          return Card(
            elevation: 3,
            margin: EdgeInsets.symmetric(vertical: 6, horizontal: 8),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(10),
            ),
            child: Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _nivelSub(p),
                    style: TextStyle(fontSize: 12),
                  ),
                  Padding(
                    padding: const EdgeInsets.only(top: 4),
                    child: Text(
                      "Fecha: $fecha",
                      style: TextStyle(fontSize: 12),
                    ),
                  ),
                  if (documento != null && documento.toString().isNotEmpty)
                    Align(
                      alignment: Alignment.centerRight,
                      child: SizedBox(
                        height: 28,
                        child: ElevatedButton.icon(
                          style: ButtonStyle(
                            backgroundColor:
                            MaterialStateProperty.all<Color>(verde),
                            foregroundColor:
                            MaterialStateProperty.all<Color>(Colors.white),
                            shape: MaterialStateProperty.all<
                                RoundedRectangleBorder>(
                              RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(16),
                              ),
                            ),
                          ),
                          onPressed: () => _abrirDocumento(p),
                          icon: Icon(Icons.remove_red_eye, size: 14, color: Colors.white),
                          label: Text(
                            "Ver",
                            style: TextStyle(fontSize: 12, color: Colors.white),
                          ),
                        ),
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
