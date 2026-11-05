import 'package:flutter/material.dart';

const portalInk = Color(0xFF17343C);
const portalInkDeep = Color(0xFF10262C);
const portalPaper = Color(0xFFF4F0E8);
const portalCard = Color(0xFFFFFCF7);
const portalBrass = Color(0xFF8C6732);
const portalLine = Color(0xFFE3DACE);
const portalMuted = Color(0xFF5C574F);

ThemeData portalTheme() {
  final base = ThemeData(
    useMaterial3: true,
    colorScheme: const ColorScheme.light(
      primary: portalInk,
      onPrimary: Colors.white,
      secondary: portalBrass,
      onSecondary: Colors.white,
      surface: portalPaper,
      onSurface: Color(0xFF1C1917),
      error: Color(0xFF8C3A32),
    ),
    scaffoldBackgroundColor: portalPaper,
  );

  return base.copyWith(
    appBarTheme: const AppBarTheme(
      backgroundColor: portalInk,
      foregroundColor: Colors.white,
      elevation: 0,
      centerTitle: false,
    ),
    dividerColor: portalLine,
    cardTheme: const CardThemeData(
      color: portalCard,
      elevation: 0,
      margin: EdgeInsets.zero,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.all(Radius.circular(16)),
        side: BorderSide(color: portalLine),
      ),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        backgroundColor: portalInk,
        foregroundColor: Colors.white,
        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.all(Radius.circular(10)),
        ),
      ),
    ),
    inputDecorationTheme: const InputDecorationTheme(
      filled: true,
      fillColor: Colors.white,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.all(Radius.circular(10)),
        borderSide: BorderSide(color: portalLine),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.all(Radius.circular(10)),
        borderSide: BorderSide(color: portalLine),
      ),
    ),
    textTheme: base.textTheme.apply(
      bodyColor: const Color(0xFF1C1917),
      displayColor: portalInkDeep,
    ),
  );
}
