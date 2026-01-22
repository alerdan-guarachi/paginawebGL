import 'package:flutter/material.dart';
import 'package:appgoodlife/services/api_service.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'home_page.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../widgets/good_life_loader.dart';

class LoginPage extends StatefulWidget {
  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  bool isLoading = false;
  bool _isPasswordObscured = true;

  final Color verde = Color(0xFF94C93B);
  final Color amarillo = Color(0xFFFAA625);

  @override
  void initState() {
    super.initState();
    checkLogin();
  }

  void checkLogin() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    if (token != null) {
      final nombreUsuario = prefs.getString('nombreUsuario');
      final sucursalUsuario = prefs.getString('sucursalUsuario');
      final usuarioId = prefs.getString('usuarioId');

      if (nombreUsuario != null && sucursalUsuario != null && usuarioId != null) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) => HomePage(
              nombreUsuario: nombreUsuario,
              sucursalUsuario: sucursalUsuario,
              usuarioId: usuarioId,
            ),
          ),
        );
      }
    }
  }

  void login() async {
    setState(() => isLoading = true);

    try {
      final fcmToken = await FirebaseMessaging.instance.getToken();

      final data = await ApiService.login(
        emailController.text.trim(),
        passwordController.text.trim(),
        fcmToken: fcmToken,
      );

      // ▼▼▼ ¡MODIFICACIÓN CLAVE AQUÍ! ▼▼▼
      // Leemos la estructura plana que devuelve tu API
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('nombreUsuario', data['name']);
      await prefs.setString('sucursalUsuario', data['sucursal'] ?? 'N/A');
      await prefs.setString('usuarioId', data['id'].toString());
      await prefs.setString('token', data['token']); // Guardamos el token de Sanctum

      setState(() => isLoading = false);

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => HomePage(
            nombreUsuario: data['name'],
            sucursalUsuario: data['sucursal'] ?? 'N/A',
            usuarioId: data['id'].toString(),
          ),
        ),
      );

    } catch (e) {
      setState(() => isLoading = false);
      print('Error en login: $e');
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Usuario o contraseña incorrectos'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [verde, amarillo],
          ),
        ),
        child: Center(
          child: SingleChildScrollView(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [

                Container(
                  width: 140,
                  height: 140,
                  decoration: BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                  ),
                  padding: EdgeInsets.all(1),
                  child: Image.asset(
                    'assets/logo.png',
                    fit: BoxFit.contain,
                  ),
                ),

                SizedBox(height: 10),

                Container(
                  margin: EdgeInsets.symmetric(horizontal: 24),
                  padding: EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black26,
                        blurRadius: 10,
                        offset: Offset(0, 5),
                      )
                    ],
                  ),
                  child: Column(
                    children: [

                      Text(
                        "Iniciar Sesión",
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                          color: verde,
                        ),
                      ),

                      SizedBox(height: 20),

                      TextField(
                        controller: emailController,
                        decoration: InputDecoration(
                          labelText: "Correo",
                          prefixIcon: Icon(Icons.email, color: verde),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(15),
                          ),
                        ),
                      ),

                      SizedBox(height: 16),

                      TextField(
                        controller: passwordController,
                        obscureText: _isPasswordObscured,
                        decoration: InputDecoration(
                          labelText: "Contraseña",
                          prefixIcon: Icon(Icons.lock, color: verde),
                          suffixIcon: IconButton(
                            icon: Icon(
                              _isPasswordObscured ? Icons.visibility_off : Icons.visibility,
                              color: verde,
                            ),
                            onPressed: () {
                              setState(() {
                                _isPasswordObscured = !_isPasswordObscured;
                              });
                            },
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(15),
                          ),
                        ),
                      ),

                      SizedBox(height: 25),

                      SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: ElevatedButton(
                          onPressed: isLoading ? null : login,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: verde,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(15),
                            ),
                          ),
                          child: isLoading
                              ? CircularProgressIndicator(color: Colors.white)
                              : Text(
                            "INICIAR SESIÓN",
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                )
              ],
            ),
          ),
        ),
      ),
    );
  }
}
