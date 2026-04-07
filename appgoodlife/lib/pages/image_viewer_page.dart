import 'package:flutter/material.dart';
import 'package:screen_protector/screen_protector.dart';
import '../widgets/good_life_loader.dart';

class ImageViewerPage extends StatefulWidget {
  final String url;
  final String title;

  const ImageViewerPage({super.key, required this.url, required this.title});

  @override
  State<ImageViewerPage> createState() => _ImageViewerPageState();
}

class _ImageViewerPageState extends State<ImageViewerPage> {
  final Color verde = const Color(0xFF94C93B);

  @override
  void initState() {
    super.initState();
    _enableScreenProtection();
  }

  @override
  void dispose() {
    _disableScreenProtection();
    super.dispose();
  }

  Future<void> _enableScreenProtection() async {
    await ScreenProtector.preventScreenshotOn();
  }

  Future<void> _disableScreenProtection() async {
    await ScreenProtector.preventScreenshotOff();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
        backgroundColor: verde,
      ),
      body: Center(
        child: InteractiveViewer(
          panEnabled: true,
          minScale: 0.5,
          maxScale: 4,
          child: Image.network(
            widget.url,
            loadingBuilder: (context, child, loadingProgress) {
              if (loadingProgress == null) return child;
              return Center(child: CircularProgressIndicator(color: verde));
            },
            errorBuilder: (context, error, stackTrace) =>
                const Center(child: Text('No se pudo cargar la imagen')),
          ),
        ),
      ),
    );
  }
}
