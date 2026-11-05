import 'package:flutter/material.dart';

import '../data/portal_repository.dart';
import '../models/models.dart';

class PortalScope extends InheritedWidget {
  const PortalScope({
    required this.repository,
    required this.settings,
    required this.header,
    required this.footer,
    required super.child,
    super.key,
  });

  final PortalRepository repository;
  final SiteSettings settings;
  final Menu? header;
  final Menu? footer;

  static PortalScope of(BuildContext context) {
    final scope = context.dependOnInheritedWidgetOfExactType<PortalScope>();
    assert(scope != null, 'PortalScope is missing above this widget');
    return scope!;
  }

  @override
  bool updateShouldNotify(PortalScope oldWidget) {
    return repository != oldWidget.repository ||
        settings != oldWidget.settings ||
        header != oldWidget.header ||
        footer != oldWidget.footer;
  }
}
