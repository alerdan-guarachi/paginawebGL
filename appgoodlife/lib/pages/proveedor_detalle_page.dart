import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';

class ProveedorDetallePage extends StatefulWidget {
  final String accionId;
  final String accionNombre;

  const ProveedorDetallePage({Key? key, required this.accionId, required this.accionNombre}) : super(key: key);

  @override
  _ProveedorDetallePageState createState() => _ProveedorDetallePageState();
}

class _ProveedorDetallePageState extends State<ProveedorDetallePage> {
  late Future<Map<String, dynamic>> _proveedorFuture;
  final Color verde = Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    _proveedorFuture = ApiService.getProveedorDetalle(widget.accionId);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.accionNombre),
        backgroundColor: verde,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _proveedorFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: GoodLifeLoader());
          } else if (snapshot.hasError) {
            return Center(child: Text('Error al cargar los detalles del proveedor.'));
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text('No se encontraron detalles para esta programación.'));
          } else {
            final proveedor = snapshot.data!;

            return SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              child: Card(
                elevation: 4,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                child: Padding(
                  padding: const EdgeInsets.all(20.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildDetailRow(Icons.business_center, 'Proveedor', proveedor['proveedor'] ?? 'No disponible'),
                      Divider(height: 30),
                      _buildDetailRow(Icons.attach_money, 'Precio', 'Bs. ${proveedor['precio'] ?? '0.00'}'),
                      Divider(height: 30),
                      _buildDetailRow(Icons.timer_outlined, 'Horario', '${proveedor['horarioinicial'] ?? '--:--'} - ${proveedor['horariofinal'] ?? '--:--'}'),
                      Divider(height: 30),
                      _buildDetailRow(Icons.hourglass_bottom, 'Tiempo de Atención', '${proveedor['tiempoatencion'] ?? 'N/A'} minutos'),
                    ],
                  ),
                ),
              ),
            );
          }
        },
      ),
    );
  }

  Widget _buildDetailRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, color: verde, size: 28),
        SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(color: Colors.grey.shade600, fontSize: 14),
              ),
              SizedBox(height: 4),
              Text(
                value,
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
