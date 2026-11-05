import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:intl/intl.dart' as intl;

import 'app_localizations_en.dart';
import 'app_localizations_id.dart';

// ignore_for_file: type=lint

/// Callers can lookup localized strings with an instance of AppLocalizations
/// returned by `AppLocalizations.of(context)`.
///
/// Applications need to include `AppLocalizations.delegate()` in their app's
/// `localizationDelegates` list, and the locales they support in the app's
/// `supportedLocales` list. For example:
///
/// ```dart
/// import 'l10n/app_localizations.dart';
///
/// return MaterialApp(
///   localizationsDelegates: AppLocalizations.localizationsDelegates,
///   supportedLocales: AppLocalizations.supportedLocales,
///   home: MyApplicationHome(),
/// );
/// ```
///
/// ## Update pubspec.yaml
///
/// Please make sure to update your pubspec.yaml to include the following
/// packages:
///
/// ```yaml
/// dependencies:
///   # Internationalization support.
///   flutter_localizations:
///     sdk: flutter
///   intl: any # Use the pinned version from flutter_localizations
///
///   # Rest of dependencies
/// ```
///
/// ## iOS Applications
///
/// iOS applications define key application metadata, including supported
/// locales, in an Info.plist file that is built into the application bundle.
/// To configure the locales supported by your app, you’ll need to edit this
/// file.
///
/// First, open your project’s ios/Runner.xcworkspace Xcode workspace file.
/// Then, in the Project Navigator, open the Info.plist file under the Runner
/// project’s Runner folder.
///
/// Next, select the Information Property List item, select Add Item from the
/// Editor menu, then select Localizations from the pop-up menu.
///
/// Select and expand the newly-created Localizations item then, for each
/// locale your application supports, add a new item and select the locale
/// you wish to add from the pop-up menu in the Value field. This list should
/// be consistent with the languages listed in the AppLocalizations.supportedLocales
/// property.
abstract class AppLocalizations {
  AppLocalizations(String locale)
    : localeName = intl.Intl.canonicalizedLocale(locale.toString());

  final String localeName;

  static AppLocalizations of(BuildContext context) {
    return Localizations.of<AppLocalizations>(context, AppLocalizations)!;
  }

  static const LocalizationsDelegate<AppLocalizations> delegate =
      _AppLocalizationsDelegate();

  /// A list of this localizations delegate along with the default localizations
  /// delegates.
  ///
  /// Returns a list of localizations delegates containing this delegate along with
  /// GlobalMaterialLocalizations.delegate, GlobalCupertinoLocalizations.delegate,
  /// and GlobalWidgetsLocalizations.delegate.
  ///
  /// Additional delegates can be added by appending to this list in
  /// MaterialApp. This list does not have to be used at all if a custom list
  /// of delegates is preferred or required.
  static const List<LocalizationsDelegate<dynamic>> localizationsDelegates =
      <LocalizationsDelegate<dynamic>>[
        delegate,
        GlobalMaterialLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
      ];

  /// A list of this localizations delegate's supported locales.
  static const List<Locale> supportedLocales = <Locale>[
    Locale('en'),
    Locale('id'),
  ];

  /// No description provided for @home.
  ///
  /// In id, this message translates to:
  /// **'Beranda'**
  String get home;

  /// No description provided for @articles.
  ///
  /// In id, this message translates to:
  /// **'Artikel'**
  String get articles;

  /// No description provided for @faq.
  ///
  /// In id, this message translates to:
  /// **'FAQ'**
  String get faq;

  /// No description provided for @questions.
  ///
  /// In id, this message translates to:
  /// **'Pertanyaan'**
  String get questions;

  /// No description provided for @ask.
  ///
  /// In id, this message translates to:
  /// **'Ajukan pertanyaan'**
  String get ask;

  /// No description provided for @search.
  ///
  /// In id, this message translates to:
  /// **'Cari'**
  String get search;

  /// No description provided for @searchHint.
  ///
  /// In id, this message translates to:
  /// **'Cari judul atau ringkasan'**
  String get searchHint;

  /// No description provided for @featured.
  ///
  /// In id, this message translates to:
  /// **'Unggulan'**
  String get featured;

  /// No description provided for @latest.
  ///
  /// In id, this message translates to:
  /// **'Terbaru'**
  String get latest;

  /// No description provided for @categories.
  ///
  /// In id, this message translates to:
  /// **'Kategori'**
  String get categories;

  /// No description provided for @allCategories.
  ///
  /// In id, this message translates to:
  /// **'Semua kategori'**
  String get allCategories;

  /// No description provided for @readMore.
  ///
  /// In id, this message translates to:
  /// **'Baca selengkapnya'**
  String get readMore;

  /// No description provided for @allArticles.
  ///
  /// In id, this message translates to:
  /// **'Semua artikel'**
  String get allArticles;

  /// No description provided for @emptyPosts.
  ///
  /// In id, this message translates to:
  /// **'Belum ada artikel.'**
  String get emptyPosts;

  /// No description provided for @emptyFaqs.
  ///
  /// In id, this message translates to:
  /// **'Belum ada pertanyaan umum.'**
  String get emptyFaqs;

  /// No description provided for @emptyQuestions.
  ///
  /// In id, this message translates to:
  /// **'Belum ada pertanyaan yang sudah dijawab.'**
  String get emptyQuestions;

  /// No description provided for @errorTitle.
  ///
  /// In id, this message translates to:
  /// **'Tidak dapat memuat data'**
  String get errorTitle;

  /// No description provided for @retry.
  ///
  /// In id, this message translates to:
  /// **'Coba lagi'**
  String get retry;

  /// No description provided for @contact.
  ///
  /// In id, this message translates to:
  /// **'Kontak'**
  String get contact;

  /// No description provided for @address.
  ///
  /// In id, this message translates to:
  /// **'Alamat'**
  String get address;

  /// No description provided for @phone.
  ///
  /// In id, this message translates to:
  /// **'Telepon'**
  String get phone;

  /// No description provided for @email.
  ///
  /// In id, this message translates to:
  /// **'Email'**
  String get email;

  /// No description provided for @whatsapp.
  ///
  /// In id, this message translates to:
  /// **'WhatsApp'**
  String get whatsapp;

  /// No description provided for @officeHours.
  ///
  /// In id, this message translates to:
  /// **'Jam layanan'**
  String get officeHours;

  /// No description provided for @previous.
  ///
  /// In id, this message translates to:
  /// **'Sebelumnya'**
  String get previous;

  /// No description provided for @next.
  ///
  /// In id, this message translates to:
  /// **'Berikutnya'**
  String get next;

  /// No description provided for @pageOf.
  ///
  /// In id, this message translates to:
  /// **'Halaman {page} dari {pages}'**
  String pageOf(int page, int pages);

  /// No description provided for @byAuthor.
  ///
  /// In id, this message translates to:
  /// **'Oleh {name}'**
  String byAuthor(String name);

  /// No description provided for @views.
  ///
  /// In id, this message translates to:
  /// **'{count} kali dilihat'**
  String views(int count);

  /// No description provided for @resultCount.
  ///
  /// In id, this message translates to:
  /// **'{count} hasil'**
  String resultCount(int count);

  /// No description provided for @notFound.
  ///
  /// In id, this message translates to:
  /// **'Halaman tidak ditemukan'**
  String get notFound;

  /// No description provided for @notFoundBody.
  ///
  /// In id, this message translates to:
  /// **'Tautan ini tidak ada, atau kontennya belum diterbitkan.'**
  String get notFoundBody;

  /// No description provided for @questionsClosed.
  ///
  /// In id, this message translates to:
  /// **'Penerimaan pertanyaan sedang ditutup.'**
  String get questionsClosed;

  /// No description provided for @submit.
  ///
  /// In id, this message translates to:
  /// **'Kirim pertanyaan'**
  String get submit;

  /// No description provided for @successTitle.
  ///
  /// In id, this message translates to:
  /// **'Pertanyaan terkirim'**
  String get successTitle;

  /// No description provided for @successBody.
  ///
  /// In id, this message translates to:
  /// **'Pertanyaan Anda telah kami terima dan akan segera ditinjau.'**
  String get successBody;

  /// No description provided for @backToQuestions.
  ///
  /// In id, this message translates to:
  /// **'Kembali ke daftar'**
  String get backToQuestions;

  /// No description provided for @required.
  ///
  /// In id, this message translates to:
  /// **'Wajib diisi'**
  String get required;

  /// No description provided for @invalidEmail.
  ///
  /// In id, this message translates to:
  /// **'Masukkan email yang valid'**
  String get invalidEmail;

  /// No description provided for @invalidPhone.
  ///
  /// In id, this message translates to:
  /// **'Nomor telepon tidak valid'**
  String get invalidPhone;

  /// No description provided for @bodyTooShort.
  ///
  /// In id, this message translates to:
  /// **'Isi pertanyaan minimal 10 karakter'**
  String get bodyTooShort;

  /// No description provided for @name.
  ///
  /// In id, this message translates to:
  /// **'Nama'**
  String get name;

  /// No description provided for @topic.
  ///
  /// In id, this message translates to:
  /// **'Topik'**
  String get topic;

  /// No description provided for @subject.
  ///
  /// In id, this message translates to:
  /// **'Subjek'**
  String get subject;

  /// No description provided for @questionBody.
  ///
  /// In id, this message translates to:
  /// **'Isi pertanyaan'**
  String get questionBody;

  /// No description provided for @optional.
  ///
  /// In id, this message translates to:
  /// **'Opsional'**
  String get optional;

  /// No description provided for @answers.
  ///
  /// In id, this message translates to:
  /// **'Jawaban'**
  String get answers;

  /// No description provided for @askedOn.
  ///
  /// In id, this message translates to:
  /// **'Diajukan {date}'**
  String askedOn(String date);

  /// No description provided for @menu.
  ///
  /// In id, this message translates to:
  /// **'Menu'**
  String get menu;

  /// No description provided for @language.
  ///
  /// In id, this message translates to:
  /// **'Bahasa'**
  String get language;

  /// No description provided for @indonesian.
  ///
  /// In id, this message translates to:
  /// **'Bahasa Indonesia'**
  String get indonesian;

  /// No description provided for @english.
  ///
  /// In id, this message translates to:
  /// **'English'**
  String get english;

  /// No description provided for @footerNote.
  ///
  /// In id, this message translates to:
  /// **'Situs informasi dan layanan publik.'**
  String get footerNote;

  /// No description provided for @clearSearch.
  ///
  /// In id, this message translates to:
  /// **'Hapus pencarian'**
  String get clearSearch;
}

class _AppLocalizationsDelegate
    extends LocalizationsDelegate<AppLocalizations> {
  const _AppLocalizationsDelegate();

  @override
  Future<AppLocalizations> load(Locale locale) {
    return SynchronousFuture<AppLocalizations>(lookupAppLocalizations(locale));
  }

  @override
  bool isSupported(Locale locale) =>
      <String>['en', 'id'].contains(locale.languageCode);

  @override
  bool shouldReload(_AppLocalizationsDelegate old) => false;
}

AppLocalizations lookupAppLocalizations(Locale locale) {
  // Lookup logic when only language code is specified.
  switch (locale.languageCode) {
    case 'en':
      return AppLocalizationsEn();
    case 'id':
      return AppLocalizationsId();
  }

  throw FlutterError(
    'AppLocalizations.delegate failed to load unsupported locale "$locale". This is likely '
    'an issue with the localizations generation tool. Please file an issue '
    'on GitHub with a reproducible sample app and the gen-l10n configuration '
    'that was used.',
  );
}
