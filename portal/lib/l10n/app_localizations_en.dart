// ignore: unused_import
import 'package:intl/intl.dart' as intl;

import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for English (`en`).
class AppLocalizationsEn extends AppLocalizations {
  AppLocalizationsEn([String locale = 'en']) : super(locale);

  @override
  String get home => 'Home';

  @override
  String get articles => 'Articles';

  @override
  String get faq => 'FAQ';

  @override
  String get questions => 'Questions';

  @override
  String get ask => 'Ask a question';

  @override
  String get search => 'Search';

  @override
  String get searchHint => 'Search titles or summaries';

  @override
  String get featured => 'Featured';

  @override
  String get latest => 'Latest';

  @override
  String get categories => 'Categories';

  @override
  String get allCategories => 'All categories';

  @override
  String get readMore => 'Read more';

  @override
  String get allArticles => 'All articles';

  @override
  String get emptyPosts => 'There are no articles yet.';

  @override
  String get emptyFaqs => 'There are no FAQs yet.';

  @override
  String get emptyQuestions => 'There are no answered questions yet.';

  @override
  String get errorTitle => 'The data could not be loaded';

  @override
  String get retry => 'Try again';

  @override
  String get contact => 'Contact';

  @override
  String get address => 'Address';

  @override
  String get phone => 'Phone';

  @override
  String get email => 'Email';

  @override
  String get whatsapp => 'WhatsApp';

  @override
  String get officeHours => 'Office hours';

  @override
  String get previous => 'Previous';

  @override
  String get next => 'Next';

  @override
  String pageOf(int page, int pages) {
    return 'Page $page of $pages';
  }

  @override
  String byAuthor(String name) {
    return 'By $name';
  }

  @override
  String views(int count) {
    return '$count views';
  }

  @override
  String resultCount(int count) {
    return '$count results';
  }

  @override
  String get notFound => 'Page not found';

  @override
  String get notFoundBody =>
      'This link does not exist, or the content is not published yet.';

  @override
  String get questionsClosed => 'Questions are not being accepted right now.';

  @override
  String get submit => 'Send question';

  @override
  String get successTitle => 'Question sent';

  @override
  String get successBody =>
      'We have received your question and will review it shortly.';

  @override
  String get backToQuestions => 'Back to the list';

  @override
  String get required => 'Required';

  @override
  String get invalidEmail => 'Enter a valid email address';

  @override
  String get invalidPhone => 'The phone number is not valid';

  @override
  String get bodyTooShort => 'The question must be at least 10 characters';

  @override
  String get name => 'Name';

  @override
  String get topic => 'Topic';

  @override
  String get subject => 'Subject';

  @override
  String get questionBody => 'Question';

  @override
  String get optional => 'Optional';

  @override
  String get answers => 'Answers';

  @override
  String askedOn(String date) {
    return 'Asked $date';
  }

  @override
  String get menu => 'Menu';

  @override
  String get language => 'Language';

  @override
  String get indonesian => 'Bahasa Indonesia';

  @override
  String get english => 'English';

  @override
  String get footerNote => 'Public information and services.';

  @override
  String get clearSearch => 'Clear search';
}
