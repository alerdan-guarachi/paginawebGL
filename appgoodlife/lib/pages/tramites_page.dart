import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'detalle_tramite_page.dart';
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
    // ▼▼▼ IP CORREGIDA ▼▼▼
    final url = Uri.parse("http://192.168.88.224:8000/api/tramites/${widget.usuarioId}");
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

          final String estado = (tramite['estado'] ?? 'SIN ESTADO').toUpperCase();
          String estadoTexto;
          Color estadoColor;
          bool isClickable;

          switch (estado) {
            case 'PENDIENTE':
              estadoTexto = 'EN CURSO';
              estadoColor = Colors.orange.shade700;
              isClickable = true;
              break;
            case 'FINALIZADO':
              estadoTexto = 'FINALIZADO';
              estadoColor = Colors.green.shade700;
              isClickable = false;
              break;
            case 'INTERRUMPIDO':
              estadoTexto = 'INTERRUMPIDO';
              estadoColor = Colors.red.shade700;
              isClickable = false;
              break;
            default:
              estadoTexto = estado;
              estadoColor = Colors.blueGrey;
              isClickable = true;
              break;
          }

          return Card(
            elevation: isClickable ? 3 : 1,
            color: isClickable ? Colors.white : Colors.grey.shade100,
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
                      color: isClickable ? Colors.black87 : Colors.grey.shade600,
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
                  trailing: isClickable
                      ? Icon(Icons.arrow_forward_ios, size: 18)
                      : Icon(Icons.lock_outline, size: 18, color: Colors.grey),
                  onTap: isClickable
                      ? () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => DetalleTramitePage(
                                tramiteId: tramite["id"].toString(),
                                nombreTramite: tramite["tramite"] ?? "Trámite",
                              ),
                            ),
                          );
                        }
                      : null,
                ),
                Positioned(
                  bottom: 0,
                  right: 0,
                  child: Container(
                    padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: estadoColor,
                      borderRadius: BorderRadius.only(
                        topLeft: Radius.circular(10),
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
