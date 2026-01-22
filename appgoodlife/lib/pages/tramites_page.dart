import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'detalle_tramite_page.dart'; // Asegúrate de importar tu nueva vista
import '../widgets/good_life_loader.dart';

class TramitesPage extends StatefulWidget {
  final String usuarioId;

  TramitesPage({required this.usuarioId});

  @override
  _TramitesPageState createState() => _TramitesPageState();
}

class _TramitesPageState extends State<TramitesPage> {
  List tramites = [];
  List procedimientos = [];
  bool cargando = true;
  final Color verde = Color(0xFF94C93B);

  Future<void> cargarTramites() async {
    final url = Uri.parse("http://192.168.0.20:8000/api/tramites/${widget.usuarioId}");
    final resp = await http.get(url);

    if (resp.statusCode == 200) {
      final data = jsonDecode(resp.body);

      setState(() {
        tramites = data["tramites"];
        procedimientos = data["procedimientos"];
        cargando = false;
      });
    } else {
      setState(() => cargando = false);
    }
  }

  @override
  void initState() {
    super.initState();
    cargarTramites();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Trámites"), backgroundColor: verde),
      body: cargando
          ? Center(child: GoodLifeLoader())
          : ListView.builder(
        itemCount: tramites.length,
        itemBuilder: (context, i) {
          final tramite = tramites[i];

          final detalle = procedimientos.firstWhere(
                (p) => p["idtramite"] == tramite["id"],
            orElse: () => null,
          );

          final String estadoTexto = tramite['estado'] == 'PENDIENTE'
              ? 'EN CURSO'
              : tramite['estado'] ?? 'SIN ESTADO';
          final Color estadoColor = tramite['estado'] == 'PENDIENTE'
              ? Colors.orange.shade700
              : Colors.blueGrey;

          return Card(
            elevation: 3,
            margin: EdgeInsets.symmetric(vertical: 6, horizontal: 8),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(10),
            ),
            clipBehavior: Clip.antiAlias,
            child: Stack(
              children: [
                ListTile(
                  contentPadding: EdgeInsets.fromLTRB(16, 12, 16, 12),
                  title: Text(
                    tramite["tramite"] ?? "Sin nombre",
                    style: TextStyle(
                      fontWeight: FontWeight.w600,
                      fontSize: 14,
                    ),
                  ),
                  subtitle: detalle == null
                      ? Text(
                    "APODERADO: ${tramite["apoderadoasignado"] ?? "No asignado"}",
                    style: TextStyle(fontSize: 10),
                  )
                      : Text(
                    "Tipo: ${detalle["tipo"]}\n"
                        "Nivel: ${detalle["nivelprocedimiento"]}\n"
                        "Subprocedimiento: ${detalle["subprocedimiento"]}\n"
                        "Documento: ${detalle["documento"]}",
                    style: TextStyle(fontSize: 12),
                  ),
                  trailing: Icon(Icons.arrow_forward_ios, size: 18),
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => DetalleTramitePage(
                          tramiteId: tramite["id"].toString(),
                          nombreTramite: tramite["tramite"] ?? "Trámite",
                        ),
                      ),
                    );
                  },
                ),
                // ▼▼▼ WIDGET PARA LA ETIQUETA DE ESTADO (MODIFICADO) ▼▼▼
                Positioned(
                  bottom: 0, // Anclado a la parte inferior
                  right: 0,  // Anclado a la derecha
                  child: Container(
                    padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: estadoColor,
                      borderRadius: BorderRadius.only(
                        topLeft: Radius.circular(10), // Esquina redondeada ahora es la superior izquierda
                      ),
                    ),
                    child: Text(
                      estadoTexto,
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
