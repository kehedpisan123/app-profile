import '../models/models.dart';

const supportedLocales = ['id', 'en'];

String localeFromPath(Uri uri) {
  if (uri.pathSegments.isNotEmpty &&
      supportedLocales.contains(uri.pathSegments.first)) {
    return uri.pathSegments.first;
  }

  return 'id';
}

String replaceLocale(Uri uri, String locale) {
  final segments = [...uri.pathSegments];
  if (segments.isNotEmpty && supportedLocales.contains(segments.first)) {
    segments[0] = locale;
  } else {
    segments.insert(0, locale);
  }

  final path = '/${segments.join('/')}';
  final query = uri.hasQuery ? '?${uri.query}' : '';

  return '$path$query';
}

sealed class MenuDestination {
  const MenuDestination();
}

class InAppDestination extends MenuDestination {
  const InAppDestination(this.location);

  final String location;

  @override
  bool operator ==(Object other) {
    return other is InAppDestination && other.location == location;
  }

  @override
  int get hashCode => location.hashCode;
}

class ExternalDestination extends MenuDestination {
  const ExternalDestination(this.uri);

  final Uri uri;

  @override
  bool operator ==(Object other) {
    return other is ExternalDestination && other.uri == uri;
  }

  @override
  int get hashCode => uri.hashCode;
}

MenuDestination? destinationForMenuItem(MenuItem item, String locale) {
  switch (item.type) {
    case 'page':
      final slug = item.slug;
      if (slug == null) {
        return null;
      }
      return InAppDestination('/$locale/pages/$slug');
    case 'post':
      final slug = item.slug;
      if (slug == null) {
        return null;
      }
      return InAppDestination('/$locale/posts/$slug');
    case 'category':
      final slug = item.slug;
      if (slug == null) {
        return null;
      }
      return InAppDestination('/$locale/posts?category=$slug');
    case 'custom':
      return _customDestination(item.url, locale);
    default:
      return _customDestination(item.url, locale);
  }
}

MenuDestination? _customDestination(String? url, String locale) {
  final value = url?.trim();
  if (value == null || value.isEmpty || value == '#') {
    return null;
  }

  final parsed = Uri.tryParse(value);
  if (parsed != null && parsed.hasScheme) {
    return ExternalDestination(parsed);
  }

  final path = value.startsWith('/') ? value : '/$value';
  if (path == '/') {
    return InAppDestination('/$locale');
  }

  return InAppDestination('/$locale$path');
}
