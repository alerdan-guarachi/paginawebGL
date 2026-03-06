import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_localizations/flutter_localizations.dart'; // 1. IMPORTAR
import 'firebase_options.dart';

import 'pages/login_page.dart';
import 'pages/home_page.dart';
import 'services/notificaciones_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );

  FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);

  await NotificacionesService.init();

  final prefs = await SharedPreferences.getInstance();
  final nombreUsuario = prefs.getString('nombreUsuario');
  final sucursalUsuario = prefs.getString('sucursalUsuario');
  final usuarioId = prefs.getString('usuarioId');

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

      // ▼▼▼ CONFIGURACIÓN DE IDIOMA AÑADIDA ▼▼▼
      localizationsDelegates: [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      supportedLocales: [
        const Locale('en', ''), // Inglés
        const Locale('es', ''), // Español
      ],
      locale: const Locale('es', 'ES'), // Forzar el idioma español
      // ▲▲▲ FIN DE LA CONFIGURACIÓN ▲▲▲

      home: (nombreUsuario != null && sucursalUsuario != null && usuarioId != null)
          ? HomePage(
        nombreUsuario: nombreUsuario!,
        sucursalUsuario: sucursalUsuario!,
        usuarioId: usuarioId!,
      )
          : LoginPage(),
      routes: {
        '/login': (context) => LoginPage(),
      },
    );
  }
}
