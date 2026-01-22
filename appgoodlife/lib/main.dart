import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'firebase_options.dart';

import 'pages/login_page.dart';
import 'pages/home_page.dart';
import 'services/notificaciones_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // 🔥 Inicializar Firebase
  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );

  // 🎧 Registrar el handler para mensajes en background
  FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);

  // 🔔 Inicializar el servicio de notificaciones (locales y push)
  await NotificacionesService.init();

  // 🔑 Cargar datos guardados del usuario
  final prefs = await SharedPreferences.getInstance();
  final nombreUsuario = prefs.getString('nombreUsuario');
  final sucursalUsuario = prefs.getString('sucursalUsuario');
  final usuarioId = prefs.getString('usuarioId');

  // 🔹 Ejecutar app
  runApp(MyApp(
    nombreUsuario: nombreUsuario,
    sucursalUsuario: sucursalUsuario,
    usuarioId: usuarioId,
  ));
}


class MyApp extends StatelessWidget {
  final String? nombreUsuario;
  final String? sucursalUsuario;
  final String? usuarioId;

  MyApp({this.nombreUsuario, this.sucursalUsuario, this.usuarioId});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Good Life',
      home: (nombreUsuario != null && sucursalUsuario != null && usuarioId != null)
          ? HomePage(
        nombreUsuario: nombreUsuario!,
        sucursalUsuario: sucursalUsuario!,
        usuarioId: usuarioId!, // <-- AHORA SÍ EXISTE
      )
          : LoginPage(),
      routes: {
        '/login': (context) => LoginPage(),
      },
    );
  }
}
