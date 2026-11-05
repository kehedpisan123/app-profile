import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'l10n/app_localizations.dart';
import 'src/routing/app_router.dart';
import 'src/routing/paths.dart';
import 'src/ui/theme.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id');
  await initializeDateFormatting('en');
  runApp(PortalApp(initialLocale: await _savedLocale()));
}

Future<String> _savedLocale() async {
  try {
    final preferences = await SharedPreferences.getInstance();
    return preferences.getString('locale') == 'en' ? 'en' : 'id';
  } catch (_) {
    return 'id';
  }
}

class PortalApp extends StatefulWidget {
  const PortalApp({required this.initialLocale, super.key});

  final String initialLocale;

  @override
  State<PortalApp> createState() => _PortalAppState();
}

class _PortalAppState extends State<PortalApp> {
  late final GoRouter _router;
  late String _locale;

  @override
  void initState() {
    super.initState();
    _locale = widget.initialLocale;
    _router = createRouter(initialLocale: _locale);
    _locale = localeFromPath(_router.routeInformationProvider.value.uri);
    _router.routerDelegate.addListener(_syncLocale);
  }

  void _syncLocale() {
    final next = localeFromPath(_router.state.uri);
    if (next != _locale && mounted) {
      setState(() => _locale = next);
    }
  }

  @override
  void dispose() {
    _router.routerDelegate.removeListener(_syncLocale);
    _router.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      debugShowCheckedModeBanner: false,
      theme: portalTheme(),
      locale: Locale(_locale),
      routerConfig: _router,
      localizationsDelegates: const [
        AppLocalizations.delegate,
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      supportedLocales: AppLocalizations.supportedLocales,
    );
  }
}
