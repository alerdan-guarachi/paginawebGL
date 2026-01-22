import 'package:flutter/material.dart';

class RegisterPage extends StatelessWidget {

  final Color verde = Color(0xFF94C93B);

  @override
  Widget build(BuildContext context) {

    return Scaffold(
      appBar: AppBar(
        backgroundColor: verde,
        title: Text('Registro'),
      ),

      body: Center(
        child: Padding(
          padding: EdgeInsets.all(20),
          child: Column(
            children: [

              Text(
                "REGÍSTRATE EN GOOD LIFE",
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: verde,
                ),
              ),

              SizedBox(height: 30),

              TextField(decoration: InputDecoration(labelText: "Nombre")),
              SizedBox(height: 12),

              TextField(decoration: InputDecoration(labelText: "Correo")),
              SizedBox(height: 12),

              TextField(
                decoration: InputDecoration(labelText: "Contraseña"),
                obscureText: true,
              ),

              SizedBox(height: 30),

              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: verde,
                  ),
                  child: Text("CREAR CUENTA"),
                  onPressed: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text("Registro pendiente de conectar a Laravel")),
                    );
                  },
                ),
              )

            ],
          ),
        ),
      ),
    );
  }
}
