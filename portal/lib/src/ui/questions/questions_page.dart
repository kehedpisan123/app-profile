import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../../l10n/app_localizations.dart';
import '../../api/api_exception.dart';
import '../../models/models.dart';
import '../../routing/paths.dart';
import '../format.dart';
import '../home/home_page.dart';
import '../portal_scope.dart';
import '../theme.dart';
import '../widgets.dart';

class QuestionsPage extends StatefulWidget {
  const QuestionsPage({this.search, this.page = 1, super.key});

  final String? search;
  final int page;

  @override
  State<QuestionsPage> createState() => _QuestionsPageState();
}

class _QuestionsPageState extends State<QuestionsPage> {
  late final TextEditingController _search;
  late Future<PageResult<Question>> _future;
  var _ready = false;

  @override
  void initState() {
    super.initState();
    _search = TextEditingController(text: widget.search ?? '');
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_ready) {
      return;
    }
    _ready = true;
    _future = PortalScope.of(context).repository.questions(
      search: widget.search,
      page: widget.page,
    );
  }

  @override
  void dispose() {
    _search.dispose();
    super.dispose();
  }

  void _go({String? search, int page = 1}) {
    final locale = localeFromPath(GoRouterState.of(context).uri);
    final query = <String, String>{};
    final nextSearch = search ?? widget.search;
    if (nextSearch != null && nextSearch.isNotEmpty) {
      query['search'] = nextSearch;
    }
    if (page > 1) {
      query['page'] = '$page';
    }
    context.go(
      Uri(path: '/$locale/pertanyaan', queryParameters: query).toString(),
    );
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);
    final locale = localeFromPath(GoRouterState.of(context).uri);
    final enabled = PortalScope.of(context).settings.questionsEnabled;

    return FutureBuilder<PageResult<Question>>(
      future: _future,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return LoadFailure(
            error: snapshot.error ?? 'error',
            onRetry: () => setState(() {
              _future = PortalScope.of(context).repository.questions(
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
              Row(
                children: [
                  Expanded(
                    child: Text(
                      l10n.questions,
                      style: Theme.of(context).textTheme.headlineMedium
                          ?.copyWith(
                            color: portalInkDeep,
                            fontWeight: FontWeight.w700,
                          ),
                    ),
                  ),
                  if (enabled)
                    FilledButton(
                      onPressed: () => context.go('/$locale/pertanyaan/baru'),
                      child: Text(l10n.ask),
                    ),
                ],
              ),
              const SizedBox(height: 16),
              TextField(
                controller: _search,
                textInputAction: TextInputAction.search,
                decoration: InputDecoration(
                  hintText: l10n.searchHint,
                  prefixIcon: const Icon(Icons.search),
                ),
                onSubmitted: (value) => _go(search: value.trim()),
              ),
              const SizedBox(height: 8),
              Text(
                l10n.resultCount(result.total),
                style: const TextStyle(color: portalMuted),
              ),
              const SizedBox(height: 12),
              if (result.items.isEmpty)
                StatusMessage(title: l10n.emptyQuestions)
              else
                for (final question in result.items)
                  Card(
                    margin: const EdgeInsets.only(bottom: 12),
                    child: ListTile(
                      title: Text(
                        question.subject,
                        style: const TextStyle(fontWeight: FontWeight.w700),
                      ),
                      subtitle: Text(
                        [
                          question.name,
                          if (question.topic != null) question.topic!.name,
                          formatDate(question.createdAt, locale),
                        ].where((part) => part.isNotEmpty).join(' · '),
                      ),
                      onTap: () =>
                          context.go('/$locale/pertanyaan/${question.id}'),
                    ),
                  ),
              Pager(
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

class QuestionPage extends StatefulWidget {
  const QuestionPage({required this.id, super.key});

  final int id;

  @override
  State<QuestionPage> createState() => _QuestionPageState();
}

class _QuestionPageState extends State<QuestionPage> {
  late Future<Question> _future;
  var _ready = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_ready) {
      return;
    }
    _ready = true;
    _future = PortalScope.of(context).repository.question(widget.id);
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);
    final locale = localeFromPath(GoRouterState.of(context).uri);

    return FutureBuilder<Question>(
      future: _future,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return LoadFailure(
            error: snapshot.error ?? 'error',
            onRetry: () => setState(() {
              _future = PortalScope.of(context).repository.question(widget.id);
            }),
          );
        }

        final question = snapshot.data!;
        return PortalPage(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                question.subject,
                style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                  color: portalInkDeep,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                [
                  question.name,
                  if (question.topic != null) question.topic!.name,
                  if (question.createdAt != null)
                    l10n.askedOn(formatDate(question.createdAt, locale)),
                ].where((part) => part.isNotEmpty).join(' · '),
                style: const TextStyle(color: portalMuted),
              ),
              const SizedBox(height: 20),
              Text(question.body, style: const TextStyle(height: 1.6)),
              const SizedBox(height: 28),
              Text(
                l10n.answers,
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 12),
              for (final answer in question.answers)
                Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (answer.answeredAt != null)
                          Text(
                            formatDate(answer.answeredAt, locale),
                            style: const TextStyle(color: portalMuted),
                          ),
                        const SizedBox(height: 8),
                        ArticleBody(answer.body),
                      ],
                    ),
                  ),
                ),
            ],
          ),
        );
      },
    );
  }
}

class AskQuestionPage extends StatefulWidget {
  const AskQuestionPage({super.key});

  @override
  State<AskQuestionPage> createState() => _AskQuestionPageState();
}

class _AskQuestionPageState extends State<AskQuestionPage> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _subject = TextEditingController();
  final _body = TextEditingController();
  late Future<List<QuestionTopic>> _topics;
  var _ready = false;
  int? _topicId;
  var _sending = false;
  var _sent = false;
  ApiException? _error;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_ready) {
      return;
    }
    _ready = true;
    _topics = PortalScope.of(context).repository.topics();
  }

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    _phone.dispose();
    _subject.dispose();
    _body.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate() || _topicId is! int) {
      return;
    }

    setState(() {
      _sending = true;
      _error = null;
    });

    try {
      await PortalScope.of(context).repository.ask({
        'topic_id': _topicId,
        'name': _name.text.trim(),
        'email': _email.text.trim(),
        if (_phone.text.trim().isNotEmpty) 'phone': _phone.text.trim(),
        'subject': _subject.text.trim(),
        'body': _body.text.trim(),
      });
      if (mounted) {
        setState(() => _sent = true);
      }
    } on ApiException catch (error) {
      if (mounted) {
        setState(() => _error = error);
        _formKey.currentState!.validate();
      }
    } finally {
      if (mounted) {
        setState(() => _sending = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);
    final locale = localeFromPath(GoRouterState.of(context).uri);
    final settings = PortalScope.of(context).settings;

    if (!settings.questionsEnabled) {
      return PortalPage(child: StatusMessage(title: l10n.questionsClosed));
    }
    if (_sent) {
      return PortalPage(
        child: StatusMessage(
          title: l10n.successTitle,
          body: l10n.successBody,
          action: FilledButton(
            onPressed: () => context.go('/$locale/pertanyaan'),
            child: Text(l10n.backToQuestions),
          ),
        ),
      );
    }

    return FutureBuilder<List<QuestionTopic>>(
      future: _topics,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return LoadFailure(
            error: snapshot.error ?? 'error',
            onRetry: () => setState(() {
              _topics = PortalScope.of(context).repository.topics();
            }),
          );
        }

        final topics = snapshot.data!;
        return PortalPage(
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  l10n.ask,
                  style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                    color: portalInkDeep,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 20),
                DropdownButtonFormField<int>(
                  initialValue: _topicId,
                  decoration: InputDecoration(labelText: l10n.topic),
                  items: [
                    for (final topic in topics)
                      DropdownMenuItem(value: topic.id, child: Text(topic.name)),
                  ],
                  validator: (value) => value == null ? l10n.required : null,
                  onChanged: (value) => setState(() => _topicId = value),
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _name,
                  decoration: InputDecoration(
                    labelText: l10n.name,
                    errorText: _error?.fieldError('name'),
                  ),
                  validator: (value) =>
                      value == null || value.trim().isEmpty ? l10n.required : null,
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _email,
                  keyboardType: TextInputType.emailAddress,
                  decoration: InputDecoration(
                    labelText: l10n.email,
                    errorText: _error?.fieldError('email'),
                  ),
                  validator: (value) {
                    final text = value?.trim() ?? '';
                    if (text.isEmpty) {
                      return l10n.required;
                    }
                    if (!RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$').hasMatch(text)) {
                      return l10n.invalidEmail;
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _phone,
                  keyboardType: TextInputType.phone,
                  decoration: InputDecoration(
                    labelText: '${l10n.phone} (${l10n.optional})',
                    errorText: _error?.fieldError('phone'),
                  ),
                  validator: (value) {
                    final text = value?.trim() ?? '';
                    if (text.isEmpty) {
                      return null;
                    }
                    if (!RegExp(r'^\+?[0-9\s\-]{6,30}$').hasMatch(text)) {
                      return l10n.invalidPhone;
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _subject,
                  decoration: InputDecoration(
                    labelText: l10n.subject,
                    errorText: _error?.fieldError('subject'),
                  ),
                  validator: (value) =>
                      value == null || value.trim().isEmpty ? l10n.required : null,
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _body,
                  minLines: 5,
                  maxLines: 8,
                  decoration: InputDecoration(
                    labelText: l10n.questionBody,
                    alignLabelWithHint: true,
                    errorText: _error?.fieldError('body'),
                  ),
                  validator: (value) {
                    final text = value?.trim() ?? '';
                    if (text.isEmpty) {
                      return l10n.required;
                    }
                    if (text.length < 10) {
                      return l10n.bodyTooShort;
                    }
                    return null;
                  },
                ),
                if (_error != null && _error!.errors.isEmpty) ...[
                  const SizedBox(height: 12),
                  Text(
                    _error!.message,
                    style: TextStyle(color: Theme.of(context).colorScheme.error),
                  ),
                ],
                const SizedBox(height: 20),
                FilledButton(
                  onPressed: _sending ? null : _submit,
                  child: Text(l10n.submit),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}

class Pager extends StatelessWidget {
  const Pager({
    required this.page,
    required this.lastPage,
    required this.onPrevious,
    required this.onNext,
    super.key,
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
