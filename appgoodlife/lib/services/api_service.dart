import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'https://api.goodlife.com.bo';

  static Future<Map<String, dynamic>> login(String email, String password, {String? fcmToken}) async {
    final url = Uri.parse('$baseUrl/api/login');
    final Map<String, String> body = {
      'email': email,
      'password': password,
    };
    if (fcmToken != null) {
      body['fcm_token'] = fcmToken;
    }
    final response = await http.post(url, headers: {'Accept': 'application/json'}, body: body);
    if (response.statusCode == 200) return json.decode(response.body);
    print('Error en login: ${response.body}');
    throw Exception('Credenciales inválidas');
  }

  static Future<Map<String, dynamic>> getBaterias(String userId) async {
    final url = Uri.parse('$baseUrl/api/baterias/$userId');
    final response = await http.get(url);
    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Error al obtener las programaciones');
    }
  }

  static Future<Map<String, dynamic>> getProveedorDetalle(String accionId) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token == null) throw Exception('Usuario no autenticado');

    final url = Uri.parse('$baseUrl/api/proveedor-detalle/$accionId');
    final response = await http.get(url, headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Error al obtener los detalles del proveedor: ${response.body}');
    }
  }

  static Future<void> programarCita({
    required String bateriaId,
    required String fecha,
    required String horaDesde,
    required String horaHasta,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token == null) throw Exception('Usuario no autenticado');

    final url = Uri.parse('$baseUrl/api/programar-cita');
    final response = await http.post(
      url,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({
        'bateria_id': bateriaId,
        'fecha': fecha,
        'horadesde': horaDesde,
        'horahasta': horaHasta,
      }),
    );

    if (response.statusCode == 409) {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Este horario ya no está disponible.');
    }

    if (response.statusCode != 201) {
      throw Exception('Fallo al crear la programación: ${response.body}');
    }
  }

  static Future<List<dynamic>> getAusencias() async {
    final url = Uri.parse('$baseUrl/api/ausencias');
    final response = await http.get(url);
    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Error al obtener las ausencias');
    }
  }

  static Future<Map<String, dynamic>> register(String name, String email, String password) async {
    final url = Uri.parse('$baseUrl/api/register');
    final response = await http.post(url, body: {'name': name, 'email': email, 'password': password});
    if (response.statusCode == 200) return json.decode(response.body);
    throw Exception('Error al registrarse');
  }

  static Future<List<dynamic>> getAreas(String tipoArea, String sucursal) async {
    final url = Uri.parse('$baseUrl/api/areas?tipo=$tipoArea&sucursal=$sucursal');
    final response = await http.get(url);
    if (response.statusCode == 200) return json.decode(response.body);
    throw Exception('Error al obtener áreas');
  }

  static Future<List<dynamic>> getAcciones(String area, String sucursal) async {
    final url = Uri.parse('$baseUrl/api/acciones?area=$area&sucursal=$sucursal');
    final response = await http.get(url);
    if (response.statusCode == 200) return json.decode(response.body);
    throw Exception('Error al obtener acciones');
  }

  static Future<List<dynamic>> getProveedores(String area, {String? accion, required String sucursal}) async {
    final url = accion != null && accion.isNotEmpty
        ? Uri.parse('$baseUrl/api/proveedores?area=$area&accion=$accion&sucursal=$sucursal')
        : Uri.parse('$baseUrl/api/proveedores?area=$area&sucursal=$sucursal');
    final response = await http.get(url);
    if (response.statusCode == 200) return json.decode(response.body);
    throw Exception('Error al obtener proveedores');
  }

  static Future<Map<String, dynamic>> getClienteByUserId(String userId) async {
    final url = Uri.parse('$baseUrl/api/clientes/$userId');
    final response = await http.get(url);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Error al obtener datos del cliente');
    }
  }

  static Future<List<dynamic>> getDocumentosByUserId(String userId) async {
    final url = Uri.parse('$baseUrl/api/documentos/$userId');
    final response = await http.get(url);

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Error al obtener documentos: ${response.statusCode}');
    }
  }

  static Future<void> confirmarAsistencia({
    required String areaNombre,
    required String decision,
    required String usuarioId,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token == null) throw Exception('Usuario no autenticado');

    final url = Uri.parse('$baseUrl/api/confirmar-asistencia');
    final response = await http.post(
      url,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({
        'areanombre': areaNombre,
        'decision': decision,
        'usuario_id': usuarioId,
      }),
    );

    if (response.statusCode != 200) {
      print('Error en confirmarAsistencia: ${response.body}');
      throw Exception('Error al guardar confirmación');
    }
  }

  static Future<Map<String, dynamic>> getNotificaciones(String userId) async {
    final response = await http.get(Uri.parse('$baseUrl/api/notificaciones/$userId'));
    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return {
        'no_leidas': List<Map<String, dynamic>>.from(data['no_leidas']),
        'leidas': List<Map<String, dynamic>>.from(data['leidas']),
      };
    } else {
      throw Exception('Error al cargar notificaciones: ${response.statusCode}');
    }
  }

  static Future<void> marcarLeida(String id) async {
    await http.post(Uri.parse('$baseUrl/api/notificaciones/$id/leer'));
  }

  // ▼▼▼ FUNCIÓN PARA ELIMINAR CUENTA (CAMBIAR A INACTIVO EN DB) ▼▼▼
  // ▼▼▼ FUNCIÓN PARA ELIMINAR CUENTA ▼▼▼
  static Future<void> eliminarCuenta() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token == null) throw Exception('Usuario no autenticado');

    // ELIMINAMOS EL /$userId DEL FINAL PARA QUE COINCIDA CON LARAVEL
    final url = Uri.parse('$baseUrl/api/eliminar-cuenta');

    final response = await http.post(
      url,
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    // Esto imprimirá en la consola de Xcode pase lo que pase
    debugPrint('Respuesta Eliminar: ${response.statusCode} - ${response.body}');

    if (response.statusCode != 200) {
      throw Exception('Error servidor: ${response.body}');
    }
  }

}
