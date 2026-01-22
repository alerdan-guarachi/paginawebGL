import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';

class GoodBitsPage extends StatefulWidget {
  final String usuarioId;

  GoodBitsPage({required this.usuarioId});

  @override
  _GoodBitsPageState createState() => _GoodBitsPageState();
}

class _GoodBitsPageState extends State<GoodBitsPage> {
  bool isLoading = true;
  double saldo = 0.0;

  final Color verde = Color(0xFF94C93B);
  final Color naranja = Color(0xFFF7941D);

  @override
  void initState() {
    super.initState();
    fetchSaldo();
  }

  void fetchSaldo() async {
    try {
      final data = await ApiService.getClienteByUserId(widget.usuarioId);

      setState(() {
        saldo = double.tryParse(data['billeteramovil'].toString()) ?? 0.0;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error al cargar saldo')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("Billetera Móvil"),
        backgroundColor: verde,
      ),
      body: isLoading
          ? GoodLifeLoader()
          : SingleChildScrollView(
        padding: EdgeInsets.all(20),
        child: Column(
          children: [

            // ===============================
            // PANEL INFORMATIVO GOODBITS
            // ===============================
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black12,
                    blurRadius: 8,
                    offset: Offset(0, 4),
                  )
                ],
              ),
              child: ExpansionTile(
                tilePadding:
                EdgeInsets.symmetric(horizontal: 15, vertical: 5),
                childrenPadding:
                EdgeInsets.symmetric(horizontal: 15, vertical: 10),

                leading: Icon(Icons.help_outline, color: naranja),
                collapsedIconColor: verde,
                iconColor: naranja,

                title: Text(
                  "¿Qué son los GoodBits?",
                  style: TextStyle(
                    color: Colors.black87,
                    fontWeight: FontWeight.w600,
                    fontSize: 16,
                  ),
                ),

                children: [
                  Text(
                    "Los GoodBits son un beneficio que GOOD LIFE otorga a sus clientes por recomendar nuestros servicios.\n\n"
                        "Si una persona se registra como cliente en GOOD LIFE y te elige como referenciador, tú ganas 20 GoodBits automáticamente.\n\n"
                        "Los GoodBits funcionan como saldo disponible que puedes utilizarlos como descuento en servicios ofrecidos por GOOD LIFE.\n\n"
                        "Mientras más recomiendes, más GoodBits acumulas. ¡Es nuestra forma de agradecer tu confianza!",
                    style: TextStyle(
                      fontSize: 14,
                      height: 1.5,
                      color: Colors.black87,
                    ),
                  ),
                ],
              ),
            ),

            SizedBox(height: 100),

            // ===============================
            // TARJETA DEL SALDO
            // ===============================
            Container(
              padding: EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    Color(0xFFF0F8E8),
                    Color(0xFFF7FAF0),
                  ],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black12,
                    blurRadius: 12,
                    offset: Offset(0, 6),
                  )
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 86, // un poco más grande para el borde
                    height: 86,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: naranja,
                        width: 3,
                      ),
                    ),
                    child: Center(
                      child: SizedBox(
                        width: 78,
                        height: 78,
                        child: ClipOval(
                          child: Image.asset(
                            'assets/goodbits.png',
                            fit: BoxFit.cover,
                          ),
                        ),
                      ),
                    ),
                  ),



                  SizedBox(height: 10),

                  Text(
                    "Saldo Disponible:",
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                      color: Colors.black87,
                    ),
                  ),

                  SizedBox(height: 1),

                  Column(
                    children: [
                      Text(
                        "${saldo.toStringAsFixed(2)}",
                        style: TextStyle(
                          fontSize: 40,
                          fontWeight: FontWeight.bold,
                          color: Colors.black87,
                        ),
                      ),

                      SizedBox(height: 1),

                      Text(
                        "GoodBits",
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w400,
                          color: Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
