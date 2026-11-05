import 'package:flutter/material.dart';

import '../../../l10n/app_localizations.dart';
import '../../models/models.dart';
import '../home/home_page.dart';
import '../portal_scope.dart';
import '../theme.dart';
import '../widgets.dart';

class FaqPage extends StatefulWidget {
  const FaqPage({super.key});

  @override
  State<FaqPage> createState() => _FaqPageState();
}

class _FaqPageState extends State<FaqPage> {
  late Future<List<Faq>> _future;
  var _ready = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_ready) {
      return;
    }
    _ready = true;
    _future = PortalScope.of(context).repository.faqs();
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context);

    return FutureBuilder<List<Faq>>(
      future: _future,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return LoadFailure(
            error: snapshot.error ?? 'error',
            onRetry: () => setState(() {
              _future = PortalScope.of(context).repository.faqs();
            }),
          );
        }

        final faqs = snapshot.data!;
        return PortalPage(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                l10n.faq,
                style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                  color: portalInkDeep,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 16),
              if (faqs.isEmpty)
                StatusMessage(title: l10n.emptyFaqs)
              else
                for (final faq in faqs)
                  Card(
                    margin: const EdgeInsets.only(bottom: 12),
                    child: ExpansionTile(
                      title: Text(
                        faq.question,
                        style: const TextStyle(fontWeight: FontWeight.w700),
                      ),
                      subtitle: faq.topic == null ? null : Text(faq.topic!.name),
                      childrenPadding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
                      children: [ArticleBody(faq.answer)],
                    ),
                  ),
            ],
          ),
        );
      },
    );
  }
}
