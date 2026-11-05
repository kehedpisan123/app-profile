import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../l10n/app_localizations.dart';
import '../models/models.dart';
import '../routing/paths.dart';
import 'portal_scope.dart';
import 'theme.dart';

class PortalPage extends StatelessWidget {
  const PortalPage({required this.child, this.onRefresh, super.key});

  final Widget child;
  final Future<void> Function()? onRefresh;

  @override
  Widget build(BuildContext context) {
    final scope = PortalScope.of(context);
    final scroll = CustomScrollView(
      slivers: [
        SliverToBoxAdapter(
          child: Align(
            alignment: Alignment.topCenter,
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 1120),
              child: Padding(
                padding: const EdgeInsets.fromLTRB(20, 28, 20, 36),
                child: child,
              ),
            ),
          ),
        ),
        SliverToBoxAdapter(
          child: PortalFooter(settings: scope.settings, menu: scope.footer),
        ),
      ],
    );

    if (onRefresh == null) {
      return scroll;
    }

    return RefreshIndicator(onRefresh: onRefresh!, child: scroll);
  }
}

class SectionTitle extends StatelessWidget {
  const SectionTitle(this.label, {this.trailing, super.key});

  final String label;
  final Widget? trailing;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Row(
        children: [
          Text(
            label,
            style: Theme.of(context).textTheme.headlineSmall
                ?.copyWith(color: portalInkDeep, fontWeight: FontWeight.w700),
          ),
          const Spacer(),
          ?trailing,
        ],
      ),
    );
  }
}

class StatusMessage extends StatelessWidget {
  const StatusMessage({required this.title, this.body, this.action, super.key});

  final String title;
  final String? body;
  final Widget? action;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 48),
      child: Column(
        children: [
          Text(
            title,
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.titleLarge,
          ),
          if (body != null) ...[
            const SizedBox(height: 8),
            Text(
              body!,
              textAlign: TextAlign.center,
              style: const TextStyle(color: portalMuted, height: 1.5),
            ),
          ],
          if (action != null) ...[const SizedBox(height: 16), action!],
        ],
      ),
    );
  }
}

class RemoteImage extends StatelessWidget {
  const RemoteImage({
    required this.url,
    this.height = 180,
    this.borderRadius = const BorderRadius.vertical(top: Radius.circular(16)),
    super.key,
  });

  final String? url;
  final double height;
  final BorderRadius borderRadius;

  @override
  Widget build(BuildContext context) {
    final imageUrl = url;
    if (imageUrl == null) {
      return const SizedBox.shrink();
    }

    return ClipRRect(
      borderRadius: borderRadius,
      child: Image.network(
        imageUrl,
        height: height,
        width: double.infinity,
        fit: BoxFit.cover,
        errorBuilder: (context, error, stackTrace) => const SizedBox.shrink(),
      ),
    );
  }
}

class PortalFooter extends StatelessWidget {
  const PortalFooter({required this.settings, required this.menu, super.key});

  final SiteSettings settings;
  final Menu? menu;

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);

    return ColoredBox(
      color: portalInkDeep,
      child: Align(
        alignment: Alignment.topCenter,
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 1120),
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 32, 20, 28),
            child: DefaultTextStyle(
              style: const TextStyle(color: Color(0xFFE7E1D6), height: 1.5),
              child: Wrap(
                spacing: 32,
                runSpacing: 24,
                children: [
                  SizedBox(
                    width: 320,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          settings.name,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 20,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(settings.description ?? l10n.footerNote),
                      ],
                    ),
                  ),
                  SizedBox(
                    width: 280,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          l10n.contact,
                          style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 8),
                        if (settings.optionalText('contact_address') != null)
                          Text(settings.text('contact_address')),
                        if (settings.optionalText('contact_phone') != null)
                          LinkText(
                            settings.text('contact_phone'),
                            Uri.parse(
                              'tel:${settings.text('contact_phone').replaceAll(RegExp(r'[^\d+]'), '')}',
                            ),
                          ),
                        if (settings.optionalText('contact_email') != null)
                          LinkText(
                            settings.text('contact_email'),
                            Uri.parse(
                              'mailto:${settings.text('contact_email')}',
                            ),
                          ),
                        for (final hour in settings.officeHours)
                          Text('${hour.day}: ${hour.hours}'),
                      ],
                    ),
                  ),
                  if (menu != null && menu!.items.isNotEmpty)
                    SizedBox(
                      width: 220,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          for (final item in menu!.items)
                            MenuLink(item: item, light: true),
                        ],
                      ),
                    ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class MenuLink extends StatelessWidget {
  const MenuLink({required this.item, this.light = false, super.key});

  final MenuItem item;
  final bool light;

  @override
  Widget build(BuildContext context) {
    final locale = localeFromPath(GoRouterState.of(context).uri);
    final destination = destinationForMenuItem(item, locale);
    final style = TextStyle(
      color: light ? const Color(0xFFE7E1D6) : portalInk,
      fontWeight: FontWeight.w600,
    );

    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: InkWell(
        onTap: destination == null
            ? null
            : () => followDestination(context, destination),
        child: Text(item.label, style: style),
      ),
    );
  }
}

class LinkText extends StatelessWidget {
  const LinkText(this.label, this.uri, {super.key});

  final String label;
  final Uri uri;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () => launchUrl(uri, mode: LaunchMode.externalApplication),
      child: Text(label),
    );
  }
}

Future<void> followDestination(
  BuildContext context,
  MenuDestination destination,
) async {
  switch (destination) {
    case InAppDestination(:final location):
      context.go(location);
    case ExternalDestination(:final uri):
      await launchUrl(uri, mode: LaunchMode.externalApplication);
  }
}

Future<bool> openUrl(BuildContext context, String url) async {
  final locale = localeFromPath(GoRouterState.of(context).uri);
  if (url.startsWith('/')) {
    context.go(url == '/' ? '/$locale' : '/$locale$url');
    return true;
  }

  final uri = Uri.tryParse(url);
  if (uri == null) {
    return false;
  }

  return launchUrl(uri, mode: LaunchMode.externalApplication);
}
