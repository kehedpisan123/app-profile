import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../../l10n/app_localizations.dart';
import '../../models/models.dart';
import '../../routing/paths.dart';
import '../format.dart';
import '../home/home_page.dart';
import '../portal_scope.dart';
import '../post_card.dart';
import '../theme.dart';
import '../widgets.dart';

class PostsPage extends StatefulWidget {
  const PostsPage({this.category, this.search, this.page = 1, super.key});

  final String? category;
  final String? search;
  final int page;

  @override
  State<PostsPage> createState() => _PostsPageState();
}

class _PostsPageState extends State<PostsPage> {
  late final TextEditingController _search;
  late Future<PageResult<Post>> _posts;
  late Future<List<Category>> _categories;
  var _ready = false;

  @override
  void initState() {
    super.initState();
    _search = TextEditingController(text: widget.search ?? '');
    _search.addListener(() {
      if (mounted) {
        setState(() {});
      }
    });
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_ready) {
      return;
    }
    _ready = true;
    final repository = PortalScope.of(context).repository;
    _posts = repository.posts(
      category: widget.category,
      search: widget.search,
      page: widget.page,
    );
    _categories = repository.categories();
  }

  @override
  void dispose() {
    _search.dispose();
    super.dispose();
  }

  void _go({String? category, String? search, int page = 1, bool keepCategory = true}) {
    final locale = localeFromPath(GoRouterState.of(context).uri);
    final query = <String, String>{};
    final nextCategory = keepCategory ? category ?? widget.category : category;
    final nextSearch = search ?? widget.search;
    if (nextCategory != null && nextCategory.isNotEmpty) {
      query['category'] = nextCategory;
    }
    if (nextSearch != null && nextSearch.isNotEmpty) {
      query['search'] = nextSearch;
    }
    if (page > 1) {
      query['page'] = '$page';
    }
    context.go(Uri(path: '/$locale/posts', queryParameters: query).toString());
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);

    return FutureBuilder<PageResult<Post>>(
      future: _posts,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return LoadFailure(
            error: snapshot.error ?? 'error',
            onRetry: () => setState(() {
              _posts = PortalScope.of(context).repository.posts(
                category: widget.category,
                search: widget.search,
                page: widget.page,
              );
            }),
          );
        }

        final result = snapshot.data!;
        return PortalPage(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                l10n.articles,
                style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                  color: portalInkDeep,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: _search,
                textInputAction: TextInputAction.search,
                decoration: InputDecoration(
                  hintText: l10n.searchHint,
                  prefixIcon: const Icon(Icons.search),
                  suffixIcon: _search.text.isEmpty
                      ? null
                      : IconButton(
                          tooltip: l10n.clearSearch,
                          onPressed: () {
                            _search.clear();
                            _go(search: '', page: 1);
                          },
                          icon: const Icon(Icons.close),
                        ),
                ),
                onSubmitted: (value) => _go(search: value.trim(), page: 1),
              ),
              const SizedBox(height: 12),
              FutureBuilder<List<Category>>(
                future: _categories,
                builder: (context, categorySnapshot) {
                  final categories = categorySnapshot.data ?? const [];
                  if (categories.isEmpty) {
                    return const SizedBox.shrink();
                  }
                  return Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [
                      ChoiceChip(
                        label: Text(l10n.allCategories),
                        selected: widget.category == null,
                        onSelected: (_) => _go(category: null, keepCategory: false),
                      ),
                      for (final category in categories)
                        ChoiceChip(
                          label: Text(category.name),
                          selected: widget.category == category.slug,
                          onSelected: (_) => _go(category: category.slug, page: 1),
                        ),
                    ],
                  );
                },
              ),
              const SizedBox(height: 16),
              Text(
                l10n.resultCount(result.total),
                style: const TextStyle(color: portalMuted),
              ),
              const SizedBox(height: 16),
              if (result.items.isEmpty)
                StatusMessage(title: l10n.emptyPosts)
              else
                PostGrid(posts: result.items),
              const SizedBox(height: 20),
              _Pager(
                page: result.page,
                lastPage: result.lastPage,
                onPrevious: result.hasPrevious
                    ? () => _go(page: result.page - 1)
                    : null,
                onNext: result.hasNext ? () => _go(page: result.page + 1) : null,
              ),
            ],
          ),
        );
      },
    );
  }
}

class PostPage extends StatefulWidget {
  const PostPage({required this.slug, super.key});

  final String slug;

  @override
  State<PostPage> createState() => _PostPageState();
}

class _PostPageState extends State<PostPage> {
  late Future<Post> _future;
  var _ready = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_ready) {
      return;
    }
    _ready = true;
    _future = PortalScope.of(context).repository.post(widget.slug);
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);
    final locale = localeFromPath(GoRouterState.of(context).uri);

    return FutureBuilder<Post>(
      future: _future,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return LoadFailure(
            error: snapshot.error ?? 'error',
            onRetry: () => setState(() {
              _future = PortalScope.of(context).repository.post(widget.slug);
            }),
          );
        }

        final post = snapshot.data!;
        final date = formatDate(post.publishedAt, locale);
        return PortalPage(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (post.category != null)
                TextButton(
                  onPressed: () => context.go(
                    '/$locale/posts?category=${post.category!.slug}',
                  ),
                  child: Text(post.category!.name.toUpperCase()),
                ),
              Text(
                post.title,
                style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                  color: portalInkDeep,
                  fontWeight: FontWeight.w700,
                  height: 1.25,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                [
                  if (date.isNotEmpty) date,
                  if (post.authorName != null) l10n.byAuthor(post.authorName!),
                  l10n.views(post.viewCount),
                ].join(' · '),
                style: const TextStyle(color: portalMuted),
              ),
              if (post.imageUrl != null) ...[
                const SizedBox(height: 20),
                ClipRRect(
                  borderRadius: const BorderRadius.all(Radius.circular(16)),
                  child: Image.network(post.imageUrl!, fit: BoxFit.cover),
                ),
              ],
              const SizedBox(height: 24),
              ArticleBody(post.content ?? post.summary),
            ],
          ),
        );
      },
    );
  }
}

class PageDetailPage extends StatefulWidget {
  const PageDetailPage({required this.slug, super.key});

  final String slug;

  @override
  State<PageDetailPage> createState() => _PageDetailPageState();
}

class _PageDetailPageState extends State<PageDetailPage> {
  late Future<ContentPage> _future;
  var _ready = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_ready) {
      return;
    }
    _ready = true;
    _future = PortalScope.of(context).repository.page(widget.slug);
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<ContentPage>(
      future: _future,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return LoadFailure(
            error: snapshot.error ?? 'error',
            onRetry: () => setState(() {
              _future = PortalScope.of(context).repository.page(widget.slug);
            }),
          );
        }

        final page = snapshot.data!;
        return PortalPage(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                page.title,
                style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                  color: portalInkDeep,
                  fontWeight: FontWeight.w700,
                ),
              ),
              if (page.imageUrl != null) ...[
                const SizedBox(height: 20),
                ClipRRect(
                  borderRadius: const BorderRadius.all(Radius.circular(16)),
                  child: Image.network(page.imageUrl!, fit: BoxFit.cover),
                ),
              ],
              const SizedBox(height: 24),
              ArticleBody(page.content),
            ],
          ),
        );
      },
    );
  }
}

class _Pager extends StatelessWidget {
  const _Pager({
    required this.page,
    required this.lastPage,
    required this.onPrevious,
    required this.onNext,
  });

  final int page;
  final int lastPage;
  final VoidCallback? onPrevious;
  final VoidCallback? onNext;

  @override
  Widget build(BuildContext context) {
    if (lastPage <= 1) {
      return const SizedBox.shrink();
    }
    final l10n = AppLocalizations.of(context);

    return Row(
      children: [
        OutlinedButton(onPressed: onPrevious, child: Text(l10n.previous)),
        const SizedBox(width: 12),
        Text(l10n.pageOf(page, lastPage)),
        const SizedBox(width: 12),
        OutlinedButton(onPressed: onNext, child: Text(l10n.next)),
      ],
    );
  }
}
