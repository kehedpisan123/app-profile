import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../l10n/app_localizations.dart';
import '../ui/faq/faq_page.dart';
import '../ui/home/home_page.dart';
import '../ui/posts/posts_page.dart';
import '../ui/questions/questions_page.dart';
import '../ui/shell/portal_shell.dart';
import '../ui/widgets.dart';
import 'paths.dart';

GoRouter createRouter({required String initialLocale}) {
  return GoRouter(
    initialLocation: '/$initialLocale',
    redirect: (context, state) {
      final segments = state.uri.pathSegments;
      if (segments.isEmpty) {
        return '/$initialLocale';
      }
      if (!supportedLocales.contains(segments.first)) {
        return state.uri
            .replace(path: '/$initialLocale/${segments.join('/')}')
            .toString();
      }
      return null;
    },
    errorBuilder: (context, state) {
      return NotFoundPage(locale: localeFromPath(state.uri));
    },
    routes: [
      ShellRoute(
        builder: (context, state, child) {
          return PortalShell(
            locale: localeFromPath(state.uri),
            child: child,
          );
        },
        routes: [
          GoRoute(
            path: '/:lang',
            builder: (context, state) => HomePage(
              key: ValueKey(state.uri.toString()),
            ),
            routes: [
              GoRoute(
                path: 'posts',
                builder: (context, state) => PostsPage(
                  key: ValueKey(state.uri.toString()),
                  category: state.uri.queryParameters['category'],
                  search: state.uri.queryParameters['search'],
                  page: int.tryParse(state.uri.queryParameters['page'] ?? '') ?? 1,
                ),
              ),
              GoRoute(
                path: 'posts/:slug',
                builder: (context, state) => PostPage(
                  key: ValueKey(state.uri.toString()),
                  slug: state.pathParameters['slug']!,
                ),
              ),
              GoRoute(
                path: 'pages/:slug',
                builder: (context, state) => PageDetailPage(
                  key: ValueKey(state.uri.toString()),
                  slug: state.pathParameters['slug']!,
                ),
              ),
              GoRoute(
                path: 'faq',
                builder: (context, state) => FaqPage(
                  key: ValueKey(state.uri.toString()),
                ),
              ),
              GoRoute(
                path: 'pertanyaan',
                builder: (context, state) => QuestionsPage(
                  key: ValueKey(state.uri.toString()),
                  search: state.uri.queryParameters['search'],
                  page: int.tryParse(state.uri.queryParameters['page'] ?? '') ?? 1,
                ),
                routes: [
                  GoRoute(
                    path: 'baru',
                    builder: (context, state) => const AskQuestionPage(),
                  ),
                  GoRoute(
                    path: ':id',
                    builder: (context, state) {
                      final id = int.tryParse(state.pathParameters['id'] ?? '');
                      if (id == null) {
                        return NotFoundPage(
                          locale: state.pathParameters['lang'] ?? initialLocale,
                          insideShell: true,
                        );
                      }
                      return QuestionPage(id: id);
                    },
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    ],
  );
}

class NotFoundPage extends StatelessWidget {
  const NotFoundPage({
    required this.locale,
    this.insideShell = false,
    super.key,
  });

  final String locale;
  final bool insideShell;

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);
    final message = StatusMessage(
      title: l10n.notFound,
      body: l10n.notFoundBody,
      action: FilledButton(
        onPressed: () => context.go('/$locale'),
        child: Text(l10n.home),
      ),
    );

    if (insideShell) {
      return PortalPage(child: message);
    }

    return Scaffold(body: Center(child: message));
  }
}
