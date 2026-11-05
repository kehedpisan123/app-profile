import 'dart:async';

import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../../../l10n/app_localizations.dart';
import '../../data/portal_repository.dart';
import '../../models/models.dart';
import '../../routing/paths.dart';
import '../portal_scope.dart';
import '../theme.dart';
import '../widgets.dart';

class PortalShell extends StatefulWidget {
  const PortalShell({required this.locale, required this.child, super.key});

  final String locale;
  final Widget child;

  @override
  State<PortalShell> createState() => _PortalShellState();
}

class _PortalShellState extends State<PortalShell> {
  PortalRepository? _repository;
  late Future<ShellData> _future;

  @override
  void initState() {
    super.initState();
    _restart();
  }

  @override
  void didUpdateWidget(PortalShell oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.locale != widget.locale) {
      _restart();
    }
  }

  void _restart() {
    final repository = PortalRepository(locale: widget.locale);
    _repository = repository;
    _future = repository.shell();
  }

  Future<void> _switchLocale(String locale) async {
    if (locale == widget.locale) {
      return;
    }
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('locale', locale);
    if (!mounted) {
      return;
    }
    final uri = GoRouterState.of(context).uri;
    context.go(replaceLocale(uri, locale));
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);

    return FutureBuilder<ShellData>(
      future: _future,
      builder: (context, snapshot) {
        final data = snapshot.data;
        final wide = MediaQuery.sizeOf(context).width >= 960;
        final repository = _repository;

        return Title(
          title: data?.settings.name ?? 'Portal',
          color: portalInk,
          child: Scaffold(
            appBar: AppBar(
              title: InkWell(
                onTap: () => context.go('/${widget.locale}'),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    if (data?.settings.logoUrl != null) ...[
                      Image.network(
                        data!.settings.logoUrl!,
                        height: 32,
                        errorBuilder: (context, error, stackTrace) =>
                            const SizedBox.shrink(),
                      ),
                      const SizedBox(width: 10),
                    ],
                    Flexible(
                      child: Text(
                        data?.settings.name ?? 'Portal',
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              ),
              actions: [
                _LocaleButton(
                  label: 'ID',
                  selected: widget.locale == 'id',
                  onPressed: () => _switchLocale('id'),
                ),
                _LocaleButton(
                  label: 'EN',
                  selected: widget.locale == 'en',
                  onPressed: () => _switchLocale('en'),
                ),
                const SizedBox(width: 8),
              ],
            ),
            drawer: wide || data == null
                ? null
                : Drawer(
                    child: SafeArea(
                      child: ListView(
                        children: [
                          ListTile(
                            title: Text(data.settings.name),
                            subtitle: Text(l10n.menu),
                          ),
                          for (final item
                              in data.header?.items ?? _fallback(l10n))
                            _DrawerItem(item: item),
                        ],
                      ),
                    ),
                  ),
            body: Column(
              children: [
                if (wide && data != null)
                  ColoredBox(
                    color: portalInkDeep,
                    child: SizedBox(
                      height: 46,
                      child: Align(
                        alignment: Alignment.centerLeft,
                        child: _MenuBar(
                          items: data.header?.items ?? _fallback(l10n),
                        ),
                      ),
                    ),
                  ),
                Expanded(
                  child: snapshot.connectionState != ConnectionState.done
                      ? const Center(child: CircularProgressIndicator())
                      : snapshot.hasError || data == null || repository == null
                      ? StatusMessage(
                          title: l10n.errorTitle,
                          action: FilledButton(
                            onPressed: () => setState(_restart),
                            child: Text(l10n.retry),
                          ),
                        )
                      : PortalScope(
                          repository: repository,
                          settings: data.settings,
                          header: data.header,
                          footer: data.footer,
                          child: widget.child,
                        ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  List<MenuItem> _fallback(AppLocalizations l10n) {
    return [
      MenuItem(id: 0, label: l10n.home, type: 'custom', url: '/'),
      MenuItem(id: 1, label: l10n.articles, type: 'custom', url: '/posts'),
      MenuItem(id: 2, label: l10n.faq, type: 'custom', url: '/faq'),
      MenuItem(
        id: 3,
        label: l10n.questions,
        type: 'custom',
        url: '/pertanyaan',
      ),
    ];
  }
}

class _MenuBar extends StatelessWidget {
  const _MenuBar({required this.items});

  final List<MenuItem> items;

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(children: [for (final item in items) _BarItem(item: item)]),
    );
  }
}

class _BarItem extends StatelessWidget {
  const _BarItem({required this.item});

  final MenuItem item;

  @override
  Widget build(BuildContext context) {
    final locale = localeFromPath(GoRouterState.of(context).uri);
    final destination = destinationForMenuItem(item, locale);
    final current = GoRouterState.of(context).uri;

    if (item.children.isEmpty) {
      return TextButton(
        onPressed: destination == null
            ? null
            : () => followDestination(context, destination),
        child: Text(
          item.label,
          style: TextStyle(
            color: _current(destination, current)
                ? const Color(0xFFE6C27A)
                : Colors.white,
          ),
        ),
      );
    }

    return PopupMenuButton<MenuDestination>(
      tooltip: item.label,
      onSelected: (value) => followDestination(context, value),
      itemBuilder: (context) => [
        for (final child in item.children)
          if (destinationForMenuItem(child, locale)
              case final childDestination?)
            PopupMenuItem(value: childDestination, child: Text(child.label)),
      ],
      child: ExcludeSemantics(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12),
          child: Row(
            children: [
              Text(item.label, style: const TextStyle(color: Colors.white)),
              const Icon(Icons.arrow_drop_down, color: Colors.white),
            ],
          ),
        ),
      ),
    );
  }

  bool _current(MenuDestination? destination, Uri current) {
    if (destination is! InAppDestination) {
      return false;
    }
    final target = Uri.parse(destination.location);
    if (target.path != current.path) {
      return false;
    }
    for (final entry in target.queryParameters.entries) {
      if (current.queryParameters[entry.key] != entry.value) {
        return false;
      }
    }
    return true;
  }
}

class _DrawerItem extends StatelessWidget {
  const _DrawerItem({required this.item});

  final MenuItem item;

  @override
  Widget build(BuildContext context) {
    final locale = localeFromPath(GoRouterState.of(context).uri);
    final destination = destinationForMenuItem(item, locale);

    if (item.children.isEmpty) {
      return ListTile(
        title: Text(item.label),
        onTap: destination == null
            ? null
            : () {
                Navigator.pop(context);
                followDestination(context, destination);
              },
      );
    }

    return ExpansionTile(
      title: Text(item.label),
      children: [for (final child in item.children) _DrawerItem(item: child)],
    );
  }
}

class _LocaleButton extends StatelessWidget {
  const _LocaleButton({
    required this.label,
    required this.selected,
    required this.onPressed,
  });

  final String label;
  final bool selected;
  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    return TextButton(
      onPressed: onPressed,
      child: Text(
        label,
        style: TextStyle(
          color: selected ? const Color(0xFFE6C27A) : Colors.white,
          fontWeight: selected ? FontWeight.w800 : FontWeight.w500,
        ),
      ),
    );
  }
}
