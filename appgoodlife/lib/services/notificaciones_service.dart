import 'dart:math';

import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

// Definir el handler de background como una función global fuera de la clase
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  // FCM se encarga de la inicialización de Firebase para este proceso aislado.
  print("Handling a background message: ${message.messageId}");
}


class NotificacionesService {
  // Instancia de Firebase Messaging
  static final FirebaseMessaging _messaging = FirebaseMessaging.instance;

  // Instancia del plugin de notificaciones locales
  static final FlutterLocalNotificationsPlugin _notificacionesPlugin =
  FlutterLocalNotificationsPlugin();

  // Inicializar todo el servicio de notificaciones
  static Future<void> init() async {
    // 1. Inicializar notificaciones locales
    const AndroidInitializationSettings initializationSettingsAndroid =
    AndroidInitializationSettings('@mipmap/ic_launcher'); // icono de app

    const InitializationSettings initializationSettings =
    InitializationSettings(
      android: initializationSettingsAndroid,
    );
    await _notificacionesPlugin.initialize(initializationSettings);

    // 2. Solicitar permisos de notificación (importante para iOS)
    await _messaging.requestPermission();

    // 3. Obtener el Token del dispositivo (lo mantenemos para depuración)
    final token = await _messaging.getToken();
    print('====================== FCM TOKEN ======================');
    print(token);
    print('=====================================================');

    // 4. Configurar listeners para los mensajes
    _setupMessageListeners();
  }

  // Configura los listeners para los mensajes de FCM
  static void _setupMessageListeners() {
    // Mensajes recibidos mientras la app está ABIERTA (Foreground)
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print('¡Mensaje recibido en Foreground!');

      final notification = message.notification;

      if (notification != null) {
        mostrarNotificacion(
          titulo: notification.title ?? 'Nueva Notificación',
          mensaje: notification.body ?? '',
          id: Random().nextInt(100000),
        );
      }
    });

    // Handler para cuando se hace TAP en una notificación
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      print('Notificación presionada, abriendo la app: ${message.messageId}');
    });
  }


  // Muestra una notificación local
  static Future<void> mostrarNotificacion(
      {required String titulo, required String mensaje, int id = 0}) async {
    const AndroidNotificationDetails androidDetails =
    AndroidNotificationDetails(
      'canal_notificaciones',
      'Notificaciones',
      channelDescription: 'Canal para las notificaciones de la app',
      importance: Importance.max,
      priority: Priority.high,
      ticker: 'ticker',
    );

    const NotificationDetails platformDetails =
    NotificationDetails(android: androidDetails);

    await _notificacionesPlugin.show(id, titulo, mensaje, platformDetails);
  }
}
