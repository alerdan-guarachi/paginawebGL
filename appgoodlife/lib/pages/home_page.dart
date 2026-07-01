import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_service.dart';
import 'login_page.dart';
import 'areas_page.dart';
import 'atencion_medicos_page.dart';
import '../widgets/good_life_loader.dart';
import 'documentos_page.dart';
import 'tramites_page.dart';
import 'notificaciones_page.dart';
import 'programaciones_page.dart';
import 'ausencias_page.dart';

class HomePage extends StatefulWidget {
  final String nombreUsuario;
  final String sucursalUsuario;
  final String usuarioId;

  HomePage({required this.nombreUsuario, required this.sucursalUsuario, required this.usuarioId});

  @override
  _HomePageState createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _unreadCount = 0;
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    _loadUnreadCount();
  }

  Future<void> _loadUnreadCount() async {
    try {
      final data = await ApiService.getNotificaciones(widget.usuarioId);
      if (mounted) {
        setState(() {
          _unreadCount = data['no_leidas']?.length ?? 0;
        });
      }
    } catch (e) {
      print('Error fetching notification count: $e');
    }
  }

  void cerrarSesion(BuildContext context) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();

    if (mounted) {
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => LoginPage()),
        (Route<dynamic> route) => false,
      );
    }
  }

  // Función para cumplir con Apple 5.1.1 (Eliminación de cuenta)
  void _confirmarEliminacionCuenta() {
    showDialog(
      context: context,
      builder: (BuildContext context) => AlertDialog(
        title: const Text('Eliminar Cuenta'),
        content: const Text(
          '¿Estás seguro de que deseas eliminar tu cuenta? Esta acción es irreversible y perderás acceso a tus informes y trámites de manera inmediata. Sus datos serán procesados de acuerdo a nuestra política de privacidad y normativas legales de salud.'
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancelar'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              try {
                // Llamada real a la API para inactivar la cuenta
                await ApiService.eliminarCuenta();

                if (context.mounted) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Cuenta inactivada con éxito.'))
                  );
                  cerrarSesion(context);
                }
              } catch (e) {
                if (context.mounted) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('Error al eliminar cuenta: $e'))
                  );
                }
              }
            },
            child: const Text('Eliminar definitivamente', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  void _navigateToNotificaciones() async {
    await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => NotificacionesPage(usuarioId: widget.usuarioId),
      ),
    );
    _loadUnreadCount();
  }

  Future<void> _abrirUrl(String urlString) async {
    final Uri url = Uri.parse(urlString);
    if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('No se pudo abrir el enlace')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: Drawer(
        child: Column(
          children: [
            Container(
              color: verde,
              width: double.infinity,
              padding: const EdgeInsets.only(top: 40, bottom: 20),
              child: Column(
                children: [
                  Container(
                    width: 70,
                    height: 70,
                    clipBehavior: Clip.antiAlias,
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      shape: BoxShape.circle,
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(8.0),
                      child: Image.asset('assets/iconogoodlife.png', fit: BoxFit.contain),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    widget.nombreUsuario,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 10),
            ListTile(
              dense: true,
              leading: Icon(Icons.medical_services, color: verde),
              title: const Text('Listado de Especialistas', style: TextStyle(fontSize: 15)),
              onTap: () {
                showDialog(
                  context: context,
                  builder: (_) => AlertDialog(
                    title: const Text('Listado de Especialistas'),
                    content: const Text('¿Deseas ver Estudios o Especialidades?'),
                    actions: [
                      TextButton(
                        onPressed: () {
                          Navigator.pop(context);
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => AreasPage(
                                tipoArea: 'ESTUDIO',
                                sucursalUsuario: widget.sucursalUsuario,
                              ),
                            ),
                          );
                        },
                        child: const Text('ESTUDIOS'),
                      ),
                      TextButton(
                        onPressed: () {
                          Navigator.pop(context);
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => AreasPage(
                                tipoArea: 'ESPECIALIDAD',
                                sucursalUsuario: widget.sucursalUsuario,
                              ),
                            ),
                          );
                        },
                        child: const Text('ESPECIALIDADES'),
                      ),
                    ],
                  ),
                );
              },
            ),

            ListTile(
              dense: true,
              leading: Icon(Icons.person_off_outlined, color: verde),
              title: const Text('Ausencias Médicos', style: TextStyle(fontSize: 15)),
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const AusenciasPage()),
                );
              },
            ),

            ListTile(
              dense: true,
              leading: Icon(Icons.calendar_month, color: verde),
              title: const Text('Programaciones Médicas', style: TextStyle(fontSize: 15)),
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => ProgramacionesPage(usuarioId: widget.usuarioId),
                  ),
                );
              },
            ),

            ListTile(
              dense: true,
              leading: Icon(Icons.description, color: verde),
              title: const Text('Informes Médicos', style: TextStyle(fontSize: 15)),
              onTap: () async {
                final prefs = await SharedPreferences.getInstance();
                final usuarioId = prefs.getString('usuarioId');

                if (usuarioId != null) {
                  if (mounted) {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => DocumentosPage(usuarioId: usuarioId),
                      ),
                    );
                  }
                } else {
                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Usuario no autenticado')),
                    );
                  }
                }
              },
            ),

            ListTile(
              dense: true,
              leading: Icon(Icons.assignment, color: verde),
              title: const Text('Trámites', style: TextStyle(fontSize: 15)),
              onTap: () async {
                final prefs = await SharedPreferences.getInstance();
                final usuarioId = prefs.getString('usuarioId');

                if (usuarioId != null) {
                  if (mounted) {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => TramitesPage(usuarioId: usuarioId),
                      ),
                    );
                  }
                }
              },
            ),

            ListTile(
              dense: true,
              leading: Icon(Icons.notifications, color: verde),
              title: const Text('Notificaciones', style: TextStyle(fontSize: 15)),
              trailing: _unreadCount > 0
                  ? Container(
                      padding: const EdgeInsets.all(6),
                      decoration: const BoxDecoration(
                        color: Colors.orange,
                        shape: BoxShape.circle,
                      ),
                      child: Text(
                        '$_unreadCount',
                        style: const TextStyle(color: Colors.white, fontSize: 10),
                      ),
                    )
                  : null,
              onTap: _navigateToNotificaciones,
            ),

            const Divider(),

            ListTile(
              dense: true,
              leading: Icon(Icons.gavel_outlined, color: verde),
              title: const Text('Términos de Servicio', style: TextStyle(fontSize: 15)),
              onTap: () => _abrirUrl('https://goodlife.com.bo/terminos-condiciones-servicio'),
            ),

            ListTile(
              dense: true,
              leading: Icon(Icons.privacy_tip_outlined, color: verde),
              title: const Text('Política de Privacidad', style: TextStyle(fontSize: 15)),
              onTap: () => _abrirUrl('https://goodlife.com.bo/politicas-privacidad'),
            ),

            const Divider(),

            ListTile(
              dense: true,
              leading: const Icon(Icons.no_accounts_outlined, color: Colors.grey),
              title: const Text('Eliminar mi cuenta', style: TextStyle(fontSize: 15, color: Colors.grey)),
              onTap: _confirmarEliminacionCuenta,
            ),

            ListTile(
              dense: true,
              leading: const Icon(Icons.logout, color: Colors.red),
              title: const Text('Cerrar sesión', style: TextStyle(fontSize: 15)),
              onTap: () {
                showDialog(
                  context: context,
                  builder: (_) => AlertDialog(
                    title: const Text('Confirmar'),
                    content: const Text('¿Deseas cerrar sesión?'),
                    actions: [
                      TextButton(
                        onPressed: () => Navigator.pop(context),
                        child: const Text('Cancelar'),
                      ),
                      ElevatedButton(
                        style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                        onPressed: () {
                          Navigator.pop(context);
                          cerrarSesion(context);
                        },
                        child: const Text('Sí, cerrar', style: TextStyle(color: Colors.white)),
                      ),
                    ],
                  ),
                );
              },
            ),
          ],
        ),
      ),
      appBar: AppBar(backgroundColor: verde, title: const Text("")),
      body: Container(
        width: double.infinity,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              verde.withOpacity(0.15),
              Colors.white,
            ],
          ),
        ),
        child: SingleChildScrollView(
          child: Column(
            children: [
              const SizedBox(height: 40),

              Center(
                child: Image.asset(
                  'assets/logo.png',
                  height: 120,
                ),
              ),

              const SizedBox(height: 30),

              // TITULO
              Text(
                "BIENVENIDO(A) A LA APP OFICIAL DE",
                style: TextStyle(
                  fontSize: 18,
                  color: Colors.grey.shade700,
                  letterSpacing: 1,
                ),
              ),

              const SizedBox(height: 1),

              Text(
                "GOOD LIFE S.R.L.",
                style: TextStyle(
                  fontSize: 26,
                  fontWeight: FontWeight.bold,
                  color: verde,
                ),
              ),

              const SizedBox(height: 20),

              // CARD DE TEXTO
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 20),
                padding: const EdgeInsets.all(25),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: const [
                    BoxShadow(
                      color: Colors.black12,
                      blurRadius: 15,
                      offset: Offset(0, 5),
                    ),
                  ],
                ),
                child: Text(
                  "En GOOD LIFE S.R.L. nos dedicamos a cuidar de tu bienestar y asegurar tu futuro financiero.\n\n"
                      "Descubre nuestros servicios médicos y asesoría en la ley de pensiones para una vida plena y segura.\n\n"
                      "Explora nuestra App y conoce más sobre nuestro equipo de profesionales apasionados y dedicados a tu atención.\n\n"
                      "Estamos aquí para escucharte y responder a tus necesidades de manera personalizada.\n\n"
                      "¡Tu bienestar es nuestra prioridad!",
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 15,
                    height: 1.4,
                    color: Colors.grey.shade800,
                  ),
                ),
              ),

              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }
}
