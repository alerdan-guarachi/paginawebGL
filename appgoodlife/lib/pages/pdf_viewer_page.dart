import 'package:flutter/material.dart';
import 'package:syncfusion_flutter_pdfviewer/pdfviewer.dart';

class PdfViewerPage extends StatelessWidget {
  final String url;
  final String title;

  const PdfViewerPage({super.key, required this.url, required this.title});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(title),
        backgroundColor: const Color(0xFF94C93B),
      ),
      body: SfPdfViewer.network(
        url,
        // Deshabilitar la barra de herramientas de copia/pegado si es necesario
        enableTextSelection: false,
      ),
    );
  }
}
