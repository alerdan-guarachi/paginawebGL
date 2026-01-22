import 'package:flutter/material.dart';

class GoodLifeLoader extends StatefulWidget {
  @override
  _GoodLifeLoaderState createState() => _GoodLifeLoaderState();
}

class _GoodLifeLoaderState extends State<GoodLifeLoader> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  final String text = "GOOD LIFE";
  final Color verde = Color(0xFF94C93B);   // GOOD
  final Color naranja = Color(0xFFF7941D); // LIFE

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: Duration(seconds: 2),
    )..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Widget _buildAnimatedLetter(int index, String letter) {
    Color color;
    if (index < 4) {
      color = verde;
    } else if (index > 4) {
      color = naranja;
    } else {
      color = Colors.transparent;
    }

    final start = index / text.length;
    final end = (index + 1) / text.length;

    return FadeTransition(
      opacity: Tween(begin: 0.3, end: 1.0)
          .animate(CurvedAnimation(
        parent: _controller,
        curve: Interval(start, end, curve: Curves.easeInOut),
      )),
      child: ScaleTransition(
        scale: Tween(begin: 0.9, end: 1.1)
            .animate(CurvedAnimation(
          parent: _controller,
          curve: Interval(start, end, curve: Curves.easeInOut),
        )),
        child: Text(
          letter,
          style: TextStyle(
            fontSize: 32,
            fontWeight: FontWeight.w600,
            color: color,
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: List.generate(
          text.length,
              (index) => _buildAnimatedLetter(index, text[index]),
        ),
      ),
    );
  }
}
