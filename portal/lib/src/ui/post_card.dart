import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../l10n/app_localizations.dart';
import '../models/models.dart';
import '../routing/paths.dart';
import 'format.dart';
import 'theme.dart';

class PostCard extends StatelessWidget {
  const PostCard({required this.post, super.key});

  final Post post;

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);
    final locale = localeFromPath(GoRouterState.of(context).uri);
    final summary = plainText(post.summary);
    final date = formatDate(post.publishedAt, locale);

    return Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => context.go('/$locale/posts/${post.slug}'),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (post.imageUrl != null)
              Image.network(
                post.imageUrl!,
                height: 160,
                width: double.infinity,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) =>
                    const SizedBox(height: 12),
              ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (post.category != null)
                    Text(
                      post.category!.name.toUpperCase(),
                      style: const TextStyle(
                        color: portalBrass,
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        letterSpacing: 0.6,
                      ),
                    ),
                  const SizedBox(height: 6),
                  Text(
                    post.title,
                    maxLines: 3,
                    overflow: TextOverflow.ellipsis,
                    style: Theme.of(context).textTheme.titleMedium
                        ?.copyWith(fontWeight: FontWeight.w700, height: 1.3),
                  ),
                  if (summary.isNotEmpty) ...[
                    const SizedBox(height: 8),
                    Text(
                      summary,
                      maxLines: 3,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: portalMuted, height: 1.45),
                    ),
                  ],
                  const SizedBox(height: 12),
                  Text(
                    [
                      if (date.isNotEmpty) date,
                      if (post.authorName != null)
                        l10n.byAuthor(post.authorName!),
                    ].join(' · '),
                    style: const TextStyle(color: portalMuted, fontSize: 12),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class PostGrid extends StatelessWidget {
  const PostGrid({required this.posts, super.key});

  final List<Post> posts;

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final columns = constraints.maxWidth >= 900
            ? 3
            : constraints.maxWidth >= 620
            ? 2
            : 1;

        final width = (constraints.maxWidth - (16 * (columns - 1))) / columns;

        return Wrap(
          spacing: 16,
          runSpacing: 16,
          children: [
            for (final post in posts)
              SizedBox(
                width: width,
                child: PostCard(post: post),
              ),
          ],
        );
      },
    );
  }
}
