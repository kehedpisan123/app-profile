import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html_core/flutter_widget_from_html_core.dart';
import 'package:go_router/go_router.dart';

import '../../../l10n/app_localizations.dart';
import '../../api/api_exception.dart';
import '../../data/portal_repository.dart';
import '../../models/models.dart';
import '../../routing/paths.dart';
import '../portal_scope.dart';
import '../post_card.dart';
import '../theme.dart';
import '../widgets.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  late Future<_HomeData> _future;
  String? _locale;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    final locale = PortalScope.of(context).repository.locale;
    if (_locale == locale) {
      return;
    }
    _locale = locale;
    _future = _load();
  }

  Future<_HomeData> _load() {
    final repository = PortalScope.of(context).repository;
    return _HomeData.load(repository);
  }

  Future<void> _reload() async {
    setState(() => _future = _load());
    await _future;
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);
    final settings = PortalScope.of(context).settings;

    return FutureBuilder<_HomeData>(
      future: _future,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return StatusMessage(
            title: l10n.errorTitle,
            action: FilledButton(onPressed: _reload, child: Text(l10n.retry)),
          );
        }

        final data = snapshot.data!;
        return PortalPage(
          onRefresh: _reload,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (data.slides.isNotEmpty)
                SlideCarousel(slides: data.slides)
              else
                _Intro(settings: settings),
              if (data.slides.isNotEmpty) ...[
                const SizedBox(height: 28),
                _Intro(settings: settings),
              ],
              if (data.featured.items.isNotEmpty) ...[
                const SizedBox(height: 36),
                SectionTitle(l10n.featured),
                PostGrid(posts: data.featured.items),
              ],
              const SizedBox(height: 36),
              SectionTitle(
                l10n.latest,
                trailing: TextButton(
                  onPressed: () => context.go(
                    '/${localeFromPath(GoRouterState.of(context).uri)}/posts',
                  ),
                  child: Text(l10n.allArticles),
                ),
              ),
              if (data.latest.items.isEmpty)
                StatusMessage(title: l10n.emptyPosts)
              else
                PostGrid(posts: data.latest.items),
              if (data.categories.isNotEmpty) ...[
                const SizedBox(height: 36),
                SectionTitle(l10n.categories),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    for (final category in data.categories)
                      ActionChip(
                        label: Text(category.name),
                        onPressed: () => context.go(
                          '/${localeFromPath(GoRouterState.of(context).uri)}/posts?category=${category.slug}',
                        ),
                      ),
                  ],
                ),
              ],
            ],
          ),
        );
      },
    );
  }
}

class _Intro extends StatelessWidget {
  const _Intro({required this.settings});

  final SiteSettings settings;

  @override
  Widget build(BuildContext context) {
    final tagline = settings.tagline;
    final description = settings.description;
    if (tagline == null && description == null) {
      return const SizedBox.shrink();
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (tagline != null)
          Text(
            tagline,
            style: Theme.of(context).textTheme.headlineMedium?.copyWith(
              color: portalInkDeep,
              fontWeight: FontWeight.w700,
            ),
          ),
        if (description != null) ...[
          const SizedBox(height: 8),
          Text(
            description,
            style: const TextStyle(color: portalMuted, fontSize: 16, height: 1.5),
          ),
        ],
      ],
    );
  }
}

class SlideCarousel extends StatefulWidget {
  const SlideCarousel({required this.slides, super.key});

  final List<Slide> slides;

  @override
  State<SlideCarousel> createState() => _SlideCarouselState();
}

class _SlideCarouselState extends State<SlideCarousel> {
  final _controller = PageController();
  Timer? _timer;
  var _index = 0;

  @override
  void initState() {
    super.initState();
    if (widget.slides.length > 1) {
      _timer = Timer.periodic(const Duration(seconds: 6), (_) => _advance());
    }
  }

  void _advance() {
    if (!mounted || widget.slides.isEmpty) {
      return;
    }
    _index = (_index + 1) % widget.slides.length;
    _controller.animateToPage(
      _index,
      duration: const Duration(milliseconds: 450),
      curve: Curves.easeOut,
    );
  }

  @override
  void dispose() {
    _timer?.cancel();
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: const BorderRadius.all(Radius.circular(18)),
      child: SizedBox(
        height: 360,
        child: Stack(
          children: [
            PageView.builder(
              controller: _controller,
              itemCount: widget.slides.length,
              onPageChanged: (index) => setState(() => _index = index),
              itemBuilder: (context, index) {
                final slide = widget.slides[index];
                return InkWell(
                  onTap: slide.linkUrl == null
                      ? null
                      : () => openUrl(context, slide.linkUrl!),
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      Image.network(slide.imageUrl, fit: BoxFit.cover),
                      const DecoratedBox(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.bottomCenter,
                            end: Alignment.center,
                            colors: [Color(0xCC10262C), Color(0x0010262C)],
                          ),
                        ),
                      ),
                      if (slide.title != null || slide.description != null)
                        Positioned(
                          left: 24,
                          right: 24,
                          bottom: 28,
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              if (slide.title != null)
                                Text(
                                  slide.title!,
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 28,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                              if (slide.description != null)
                                Text(
                                  slide.description!,
                                  style: const TextStyle(
                                    color: Color(0xFFE7E1D6),
                                    height: 1.4,
                                  ),
                                ),
                            ],
                          ),
                        ),
                    ],
                  ),
                );
              },
            ),
            if (widget.slides.length > 1)
              Positioned(
                right: 16,
                bottom: 16,
                child: Text(
                  '${_index + 1}/${widget.slides.length}',
                  style: const TextStyle(color: Colors.white),
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class _HomeData {
  const _HomeData({
    required this.slides,
    required this.featured,
    required this.latest,
    required this.categories,
  });

  final List<Slide> slides;
  final PageResult<Post> featured;
  final PageResult<Post> latest;
  final List<Category> categories;

  static Future<_HomeData> load(PortalRepository repository) async {
    final results = await Future.wait([
      repository.slides(),
      repository.posts(featured: true, perPage: 3),
      repository.posts(perPage: 6),
      repository.categories(),
    ]);

    return _HomeData(
      slides: results[0] as List<Slide>,
      featured: results[1] as PageResult<Post>,
      latest: results[2] as PageResult<Post>,
      categories: results[3] as List<Category>,
    );
  }
}

class ArticleBody extends StatelessWidget {
  const ArticleBody(this.html, {super.key});

  final String? html;

  @override
  Widget build(BuildContext context) {
    final content = html;
    if (content == null || content.trim().isEmpty) {
      return const SizedBox.shrink();
    }

    return HtmlWidget(
      content,
      textStyle: const TextStyle(fontSize: 16, height: 1.6),
      onTapUrl: (url) => openUrl(context, url),
    );
  }
}

class LoadFailure extends StatelessWidget {
  const LoadFailure({required this.error, required this.onRetry, super.key});

  final Object error;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);
    final notFound = error is ApiException && (error as ApiException).isNotFound;

    return PortalPage(
      child: StatusMessage(
        title: notFound ? l10n.notFound : l10n.errorTitle,
        body: notFound ? l10n.notFoundBody : null,
        action: notFound
            ? null
            : FilledButton(onPressed: onRetry, child: Text(l10n.retry)),
      ),
    );
  }
}
