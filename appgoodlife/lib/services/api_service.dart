import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl = 'http://192.168.0.20:8000';

  // =============================================================
  // ▼ SE HA MODIFICADO ESTA FUNCIÓN ▼
  // =============================================================
  static Future<Map<String, dynamic>> login(String email, String password, {String? fcmToken}) async {
    final url = Uri.parse('$baseUrl/api/login');

    // Construimos el cuerpo de la petición
    final Map<String, String> body = {
      'email': email,
      'password': password,
    };

    // Añadimos el fcm_token si está presente
    if (fcmToken != null) {
      body['fcm_token'] = fcmToken;
    }

    final response = await http.post(
        url,
        headers: {'Accept': 'application/json'}, // Es buena práctica añadir esto
        body: body
    );

    if (response.statusCode == 200) return json.decode(response.body);

    print('Error en login: ${response.body}'); // Imprimir error para depurar
    throw Exception('Credenciales inválidas');
  }
  // =============================================================
  // ▲ FIN DE LA FUNCIÓN MODIFICADA ▲
  // =============================================================

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

  // Dentro de ApiService
  static Future<Map<String, dynamic>> getClienteByUserId(String userId) async {
    final url = Uri.parse('$baseUrl/api/clientes/$userId'); // Ajusta según tu endpoint
    final response = await http.get(url);

    if (response.statusCode == 200) {
      return json.decode(response.body); // Debe retornar un Map con los datos del cliente
    } else {
      throw Exception('Error al obtener datos del cliente');
    }
  }

  // Dentro de ApiService
  static Future<List<dynamic>> getDocumentosByUserId(String userId) async {
    final url = Uri.parse('$baseUrl/api/documentos/$userId');
    final response = await http.get(url);

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Error al obtener documentos: ${response.statusCode}');
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

}
