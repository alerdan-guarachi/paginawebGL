import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_service.dart';
import '../widgets/good_life_loader.dart';

class ProveedorDetallePage extends StatefulWidget {
  final String accionId;
  final String bateriaId;
  final String accionNombre;
  final String? accionesDetalle;
  final String? precioTotal;
  final String? fechaProgramada;
  final String? horaDesdeProgramada;
  final String? horaHastaProgramada;

  const ProveedorDetallePage({
    Key? key,
    required this.accionId,
    required this.bateriaId,
    required this.accionNombre,
    this.accionesDetalle,
    this.precioTotal,
    this.fechaProgramada,
    this.horaDesdeProgramada,
    this.horaHastaProgramada,
  }) : super(key: key);

  @override
  _ProveedorDetallePageState createState() => _ProveedorDetallePageState();
}

class _ProveedorDetallePageState extends State<ProveedorDetallePage> {
  late Future<Map<String, dynamic>> _proveedorFuture;
  final Color verde = Color(0xFF94C93B);

  DateTime? _selectedDate;
  final TextEditingController _dateController = TextEditingController();
  List<TimeOfDay> _availableTickets = [];
  TimeOfDay? _selectedTicket;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _proveedorFuture = ApiService.getProveedorDetalle(widget.accionId);
  }

  @override
  void dispose() {
    _dateController.dispose();
    super.dispose();
  }

  Future<void> _confirmarProgramacion(Map<String, dynamic> proveedorData) async {
    if (_selectedDate == null || _selectedTicket == null) return;

    setState(() => _isSaving = true);

    try {
      final String fecha = DateFormat('yyyy-MM-dd').format(_selectedDate!);
      final TimeOfDay startTime = _selectedTicket!;
      final int duracionCita = int.tryParse(proveedorData['duracioncita']?.toString() ?? '0') ?? 0;
      final int startMinutes = startTime.hour * 60 + startTime.minute;
      final int endMinutes = startMinutes + duracionCita;
      final TimeOfDay endTime = TimeOfDay(hour: endMinutes ~/ 60, minute: endMinutes % 60);

      final String horaDesde = '${startTime.hour.toString().padLeft(2, '0')}:${startTime.minute.toString().padLeft(2, '0')}';
      final String horaHasta = '${endTime.hour.toString().padLeft(2, '0')}:${endTime.minute.toString().padLeft(2, '0')}';

      await ApiService.programarCita(
        bateriaId: widget.bateriaId,
        fecha: fecha,
        horaDesde: horaDesde,
        horaHasta: horaHasta,
      );

      if(mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('¡Programación guardada con éxito!'), backgroundColor: Colors.green),
        );
        Navigator.of(context).pop(true);
      }

    } catch (e) {
      if(mounted) {
        if (e.toString().contains('Este horario ya no está disponible')) {
          showDialog(
            context: context,
            builder: (context) => AlertDialog(
              title: Text('Horario Ocupado'),
              content: Text(e.toString().replaceAll('Exception: ', '')),
              actions: [
                TextButton(
                  onPressed: () {
                    Navigator.of(context).pop();
                    setState(() {
                      _proveedorFuture = ApiService.getProveedorDetalle(widget.accionId);
                      _selectedTicket = null;
                    });
                  },
                  child: Text('ACEPTAR'),
                ),
              ],
            ),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Error al guardar: $e'), backgroundColor: Colors.red),
          );
        }
      }
    } finally {
      if(mounted) {
        setState(() => _isSaving = false);
      }
    }
  }

  void _generateTickets(DateTime date, List<dynamic> horarios, List<dynamic> diarios, List<dynamic> bloqueos, List<dynamic> reservas, dynamic duracionRaw) {
    _availableTickets.clear();
    final int duracionCita = int.tryParse(duracionRaw?.toString() ?? '0') ?? 0;
    if (duracionCita <= 0) return;

    final String selectedDateString = DateFormat('yyyy-MM-dd').format(date);

    var horarioFinal = diarios.firstWhere((d) => d['fecha'] == selectedDateString, orElse: () => null);

    if (horarioFinal == null) {
      final String weekdayString = _mapWeekdayToString(date.weekday);
      horarioFinal = horarios.firstWhere((h) => _normalizeString(h['dia'] as String) == _normalizeString(weekdayString), orElse: () => null);
    }

    if (horarioFinal != null) {
      final TimeOfDay startTime = _parseTime(horarioFinal['horainicio']);
      final TimeOfDay endTime = _parseTime(horarioFinal['horafin']);

      final String dateString = DateFormat('yyyy-MM-dd').format(date);
      final bloqueosDelDia = bloqueos.where((b) => b['fecha'] == dateString).toList();
      final reservasDelDia = reservas.where((r) => r['fechaasignada'] == dateString).toList();

      final now = DateTime.now();
      final isToday = date.year == now.year && date.month == now.month && date.day == now.day;

      TimeOfDay currentTime = startTime;
      while (_timeAsMinutes(currentTime) < _timeAsMinutes(endTime)) {

        bool isPast = false;
        if (isToday) {
          final timeNow = TimeOfDay.fromDateTime(now);
          if (_timeAsMinutes(currentTime) < _timeAsMinutes(timeNow)) {
            isPast = true;
          }
        }

        bool isBlocked = false;
        for (var bloqueo in bloqueosDelDia) {
          final bloqueoStart = _parseTime(bloqueo['horainicio']);
          final bloqueoEnd = _parseTime(bloqueo['horafin']);
          if (_timeAsMinutes(currentTime) >= _timeAsMinutes(bloqueoStart) && _timeAsMinutes(currentTime) < _timeAsMinutes(bloqueoEnd)) {
            isBlocked = true;
            break;
          }
        }

        bool isBooked = false;
        final ticketStartTimeString = '${currentTime.hour.toString().padLeft(2, '0')}:${currentTime.minute.toString().padLeft(2, '0')}:00';
        for (var reserva in reservasDelDia) {
          if (reserva['horadesde'] == ticketStartTimeString) {
            isBooked = true;
            break;
          }
        }

        if (!isPast && !isBlocked && !isBooked) {
          _availableTickets.add(currentTime);
        }

        final newMinutes = _timeAsMinutes(currentTime) + duracionCita;
        currentTime = TimeOfDay(hour: newMinutes ~/ 60, minute: newMinutes % 60);
      }
    }
    setState(() {});
  }

  // ▼▼▼ LÓGICA DE CALENDARIO CORREGIDA SEGÚN REGLA DE PRÓXIMA SEMANA ▼▼▼
  Future<void> _selectDate(BuildContext context, List<dynamic> horarios, List<dynamic> diarios, List<dynamic> bloqueos, List<dynamic> reservas, dynamic duracion) async {
    final DateTime now = DateTime.now();

    // Rango amplio para que el calendario pueda abrirse y mostrar fechas de atmeddiario lejanas
    final DateTime firstDate = DateTime(now.year, now.month, now.day - 30);
    final DateTime lastDate = DateTime(now.year, now.month, now.day + 90);

    // BUSCAMOS LA FECHA INICIAL: El primer día habilitado desde mañana en adelante
    DateTime initialDate = DateTime(now.year, now.month, now.day + 1);
    bool found = false;
    for (int i = 1; i < 90; i++) {
      DateTime check = DateTime(now.year, now.month, now.day + i);
      if (_isDayEnabled(check, horarios, diarios)) {
        initialDate = check;
        found = true;
        break;
      }
    }

    if (!found) {
       ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('No hay fechas disponibles próximas.')),
        );
       return;
    }

    final DateTime? picked = await showDatePicker(
      context: context,
      locale: const Locale('es', 'ES'),
      initialDate: initialDate,
      firstDate: DateTime(now.year, now.month, now.day + 1), // No programar en el pasado
      lastDate: lastDate,
      selectableDayPredicate: (DateTime day) => _isDayEnabled(day, horarios, diarios),
    );

    if (picked != null && picked != _selectedDate) {
      setState(() {
        _selectedDate = picked;
        _dateController.text = DateFormat('d \'de\' MMMM \'de\' y', 'es_ES').format(picked);
        _selectedTicket = null;
        _generateTickets(picked, horarios, diarios, bloqueos, reservas, duracion);
      });
    }
  }

  // ▼▼▼ LÓGICA DE HABILITACIÓN DE DÍAS CORREGIDA ▼▼▼
  bool _isDayEnabled(DateTime day, List<dynamic> horarios, List<dynamic> diarios) {
    // 1. DIARIOS (atmeddiario): Siempre habilitar si existe el registro (No tocar)
    final String dayString = DateFormat('yyyy-MM-dd').format(day);
    if (diarios.any((d) => d['fecha'] == dayString)) return true;

    // 2. FIJOS (atmedhorarios): SOLO HABILITAR SI ES DE LA PRÓXIMA SEMANA
    final DateTime now = DateTime.now();

    // Calculamos el lunes de la próxima semana
    int daysUntilNextMonday = 8 - now.weekday;
    final DateTime nextMonday = DateTime(now.year, now.month, now.day + daysUntilNextMonday);
    final DateTime nextSunday = nextMonday.add(const Duration(days: 6));

    // Normalizar fechas a medianoche para comparar rangos
    final dCheck = DateTime(day.year, day.month, day.day);
    final start = DateTime(nextMonday.year, nextMonday.month, nextMonday.day);
    final end = DateTime(nextSunday.year, nextSunday.month, nextSunday.day);

    // Si el día está fuera del rango de la próxima semana, no habilitamos por horario FIJO
    if (dCheck.isBefore(start) || dCheck.isAfter(end)) return false;

    // Si está dentro de la próxima semana, verificamos si el médico atiende ese día de la semana
    final String weekdayName = _mapWeekdayToString(day.weekday);
    return horarios.any((h) => _normalizeString(h['dia'] as String) == _normalizeString(weekdayName));
  }

  void _solicitarProgramacionPorWhatsapp(String proveedorNombre) async {
    final prefs = await SharedPreferences.getInstance();
    final sucursal = prefs.getString('sucursalUsuario');
    String? phone;

    if (sucursal == 'SANTA CRUZ') phone = '59167409620';
    else if (sucursal == 'COCHABAMBA') phone = '59168421919';

    if (phone != null) {
      final message = Uri.encodeComponent('Hola, quisiera solicitar una programación para ${widget.accionNombre} con el médico $proveedorNombre.');
      final url = 'https://api.whatsapp.com/send?phone=$phone&text=$message';
      final uri = Uri.parse(url);
      if (await canLaunchUrl(uri)) await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  String _mapWeekdayToString(int weekday) {
    const days = { 1: 'LUNES', 2: 'MARTES', 3: 'MIÉRCOLES', 4: 'JUEVES', 5: 'VIERNES', 6: 'SÁBADO', 7: 'DOMINGO' };
    return days[weekday] ?? '';
  }

  String _normalizeString(String text) {
    return text.toUpperCase().replaceAll('Á', 'A').replaceAll('É', 'E').replaceAll('Í', 'I').replaceAll('Ó', 'O').replaceAll('Ú', 'U');
  }

  TimeOfDay _parseTime(String? time) {
    if (time == null || time.length < 5) return TimeOfDay(hour: 0, minute: 0);
    return TimeOfDay(hour: int.parse(time.substring(0, 2)), minute: int.parse(time.substring(3, 5)));
  }

  int _timeAsMinutes(TimeOfDay time) => time.hour * 60 + time.minute;

  @override
  Widget build(BuildContext context) {
    final bool isProgramado = widget.fechaProgramada != null;

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.accionNombre),
        backgroundColor: verde,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _proveedorFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) return Center(child: GoodLifeLoader());
          if (snapshot.hasError) return Center(child: Text('Error: ${snapshot.error}'));
          if (!snapshot.hasData || snapshot.data!.isEmpty) return Center(child: Text('No se encontraron detalles.'));

          final proveedor = snapshot.data!;
          final List<dynamic> horarios = proveedor['horarios'] ?? [];
          final List<dynamic> diarios = proveedor['diarios'] ?? [];
          final List<dynamic> bloqueos = proveedor['bloqueos'] ?? [];
          final List<dynamic> reservas = proveedor['reservas'] ?? [];
          final duracion = proveedor['duracioncita'];
          final String nombreProveedor = proveedor['proveedor'] ?? 'No disponible';
          final String tipoAtencion = horarios.isNotEmpty ? (horarios.first['tipo'] ?? '') : 'ATENCIÓN POR ORDEN DE LLEGADA';

          final List<String> listaAcciones = widget.accionesDetalle != null
              ? widget.accionesDetalle!.split(',').map((e) => e.trim()).toList()
              : [];

          return SingleChildScrollView(
            padding: const EdgeInsets.all(12.0),
            child: Column(
              children: [
                Card(
                  elevation: 3,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (listaAcciones.isNotEmpty) ...[
                          Text('Servicios incluidos:', style: TextStyle(color: Colors.grey.shade600, fontSize: 12, fontWeight: FontWeight.bold)),
                          SizedBox(height: 8),
                          ...listaAcciones.map((accion) => Padding(
                            padding: const EdgeInsets.only(bottom: 4.0),
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('• ', style: TextStyle(color: verde, fontWeight: FontWeight.bold)),
                                Expanded(child: Text(accion, style: TextStyle(fontSize: 13, color: Colors.black87, fontStyle: FontStyle.italic))),
                              ],
                            ),
                          )).toList(),
                          Divider(height: 24),
                        ],

                        _buildDetailRow(Icons.business_center, 'Médico:', nombreProveedor),
                        SizedBox(height: 12),
                        _buildDetailRow(Icons.payments, 'Precio:', 'Bs. ${widget.precioTotal ?? proveedor['precio'] ?? '0.00'}'),

                        if (duracion != null && duracion.toString() != '0') ...[
                          SizedBox(height: 12),
                          _buildDetailRow(Icons.hourglass_bottom, 'Duración Cita:', '$duracion min'),
                        ],

                        if (horarios.isNotEmpty || diarios.isNotEmpty) ...[
                          Divider(height: 24),
                          Text('Horarios de Atención:', style: TextStyle(color: Colors.black, fontSize: 12, fontWeight: FontWeight.bold)),
                          SizedBox(height: 8),
                          _buildHorariosSection(horarios, diarios),
                          if (tipoAtencion == 'ATENCIÓN POR ORDEN DE LLEGADA')
                            Padding(
                              padding: const EdgeInsets.only(top: 8.0),
                              child: Text(
                                "NOTA ACLARATIVA: El horario registrado puede presentar un retraso de entre 10 y 15 minutos.",
                                style: TextStyle(fontSize: 11, fontStyle: FontStyle.italic, color: Colors.orange.shade800, fontWeight: FontWeight.w500),
                              ),
                            ),
                        ],
                      ],
                    ),
                  ),
                ),
                SizedBox(height: 16),

                if (isProgramado) _buildProgramacionExistenteCard()
                else if (horarios.isEmpty && diarios.isEmpty) _buildWhatsappCard(nombreProveedor, isInformational: false)
                else if (tipoAtencion == 'ATENCIÓN POR ORDEN DE LLEGADA') _buildDatePickerCard(context, horarios, diarios, bloqueos, reservas, duracion, proveedor)
                else _buildWhatsappCard(nombreProveedor, isInformational: true),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildProgramacionExistenteCard() {
    String fechaFormateada = widget.fechaProgramada!;
    try {
      final date = DateTime.parse(widget.fechaProgramada!);
      fechaFormateada = DateFormat('EEEE d \'de\' MMMM', 'es_ES').format(date);
      fechaFormateada = fechaFormateada[0].toUpperCase() + fechaFormateada.substring(1);
    } catch (e) {}

    final horaDesde = (widget.horaDesdeProgramada?.length ?? 0) >= 5 ? widget.horaDesdeProgramada!.substring(0, 5) : '--:--';
    final horaHasta = (widget.horaHastaProgramada?.length ?? 0) >= 5 ? widget.horaHastaProgramada!.substring(0, 5) : '--:--';

    return Card(
      elevation: 3,
      color: Colors.green.shade50,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12), side: BorderSide(color: Colors.green.shade200)),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Cita Programada', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.green.shade800)),
            SizedBox(height: 16),
            _buildDetailRow(Icons.calendar_month, 'Fecha:', fechaFormateada),
            SizedBox(height: 12),
            _buildDetailRow(Icons.timer, 'Horario:', '$horaDesde - $horaHasta'),
          ],
        ),
      ),
    );
  }

  Widget _buildDatePickerCard(BuildContext context, List<dynamic> horarios, List<dynamic> diarios, List<dynamic> bloqueos, List<dynamic> reservas, dynamic duracion, Map<String, dynamic> proveedor) {
    return Card(
      elevation: 3,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Seleccione una Fecha', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            SizedBox(height: 8),
            TextField(
              controller: _dateController,
              readOnly: true,
              decoration: InputDecoration(
                hintText: 'Toca para elegir una fecha',
                prefixIcon: Icon(Icons.calendar_today, color: verde),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
              ),
              onTap: () => _selectDate(context, horarios, diarios, bloqueos, reservas, duracion),
            ),
            if (_selectedDate != null)
              Padding(
                padding: const EdgeInsets.only(top: 16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Horarios Disponibles', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    SizedBox(height: 8),
                    _availableTickets.isEmpty
                        ? Text('No hay horarios disponibles para el día seleccionado.', style: TextStyle(fontStyle: FontStyle.italic))
                        : Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: _availableTickets.map((ticket) {
                              final isSelected = _selectedTicket == ticket;
                              final int durCita = int.tryParse(duracion?.toString() ?? '0') ?? 0;
                              final int startMin = ticket.hour * 60 + ticket.minute;
                              final int endMin = startMin + durCita;
                              final TimeOfDay endTime = TimeOfDay(hour: endMin ~/ 60, minute: endMin % 60);
                              final String labelText = '${ticket.format(context)} - ${endTime.format(context)}';

                              return Padding(
                                padding: const EdgeInsets.symmetric(vertical: 4.0),
                                child: InkWell(
                                  onTap: () {
                                    setState(() {
                                      _selectedTicket = isSelected ? null : ticket;
                                    });
                                  },
                                  borderRadius: BorderRadius.circular(8),
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(vertical: 12.0),
                                    decoration: BoxDecoration(
                                      color: isSelected ? verde : Colors.grey.shade100,
                                      borderRadius: BorderRadius.circular(8),
                                      border: Border.all(
                                        color: isSelected ? Colors.green.shade700 : Colors.grey.shade300,
                                        width: isSelected ? 2.0 : 1.0,
                                      ),
                                    ),
                                    child: Center(
                                      child: Text(
                                        labelText,
                                        style: TextStyle(
                                          color: isSelected ? Colors.white : Colors.black87,
                                          fontSize: 14,
                                          fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                              );
                            }).toList(),
                          ),
                    SizedBox(height: 16),
                    if (_selectedTicket != null)
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed: _isSaving ? null : () => _confirmarProgramacion(proveedor),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.orange,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                          icon: _isSaving ? Container(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : Icon(Icons.check_circle_outline),
                          label: Text(_isSaving ? 'GUARDANDO...' : 'PROGRAMAR CITA', style: TextStyle(fontWeight: FontWeight.bold)),
                        ),
                      )
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildWhatsappCard(String proveedorNombre, {bool isInformational = false}) {
    return Card(
      elevation: 3,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              'Solicitud Directa',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            SizedBox(height: 8),
            Text(
              isInformational
                ? 'Contacta directamente para agendar tu cita.'
                : 'Este médico no tiene horarios fijos. Solicita tu programación directamente.',
              style: TextStyle(color: Colors.grey.shade700, fontSize: 13),
            ),
            SizedBox(height: 16),
            ElevatedButton.icon(
              onPressed: () => _solicitarProgramacionPorWhatsapp(proveedorNombre),
              style: ElevatedButton.styleFrom(
                backgroundColor: Color(0xFF94C93B),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              icon: Icon(Icons.message),
              label: Text('SOLICITAR CITA', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, color: verde, size: 20),
        SizedBox(width: 10),
        Text(
          label,
          style: TextStyle(color: Colors.grey.shade700, fontSize: 13, fontWeight: FontWeight.w500),
        ),
        SizedBox(width: 8),
        Expanded(
          child: Text(
            value,
            textAlign: TextAlign.end,
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
          ),
        ),
      ],
    );
  }

  Widget _buildHorariosSection(List<dynamic> horarios, List<dynamic> diarios) {
    return Padding(
      padding: const EdgeInsets.only(left: 8.0),
      child: Column(
        children: [
          ...horarios.map((horario) {
            final dia = (horario['dia'] ?? 'Día no especificado').toUpperCase();
            final String inicio = (horario['horainicio'] != null && horario['horainicio'].length >= 5) ? horario['horainicio'].substring(0, 5) : '--:--';
            final String fin = (horario['horafin'] != null && horario['horafin'].length >= 5) ? horario['horafin'].substring(0, 5) : '--:--';
            return Padding(
              padding: const EdgeInsets.symmetric(vertical: 2.0),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(dia, style: TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
                  Text('$inicio - $fin', style: TextStyle(fontSize: 13, color: Colors.grey.shade900)),
                ],
              ),
            );
          }).toList(),

          if (diarios.isNotEmpty) ...[
            if (horarios.isNotEmpty) Divider(height: 20),
            ...diarios.map((diario) {
              String label = 'EXTRA';
              try {
                final date = DateTime.parse(diario['fecha']);
                label = DateFormat('d MMM', 'es_ES').format(date).toUpperCase();
              } catch (e) {}
              final String inicio = (diario['horainicio'] != null && diario['horainicio'].length >= 5) ? diario['horainicio'].substring(0, 5) : '--:--';
              final String fin = (diario['horafin'] != null && diario['horafin'].length >= 5) ? diario['horafin'].substring(0, 5) : '--:--';
              return Padding(
                padding: const EdgeInsets.symmetric(vertical: 2.0),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(label, style: TextStyle(fontWeight: FontWeight.w500, fontSize: 13, color: verde)),
                    Text('$inicio - $fin', style: TextStyle(fontSize: 13, color: Colors.grey.shade900)),
                  ],
                ),
              );
            }).toList(),
          ]
        ],
      ),
    );
  }
}
