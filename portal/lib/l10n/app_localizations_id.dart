// ignore: unused_import
import 'package:intl/intl.dart' as intl;

import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for Indonesian (`id`).
class AppLocalizationsId extends AppLocalizations {
  AppLocalizationsId([String locale = 'id']) : super(locale);

  @override
  String get home => 'Beranda';

  @override
  String get articles => 'Artikel';

  @override
  String get faq => 'FAQ';

  @override
  String get questions => 'Pertanyaan';

  @override
  String get ask => 'Ajukan pertanyaan';

  @override
  String get search => 'Cari';

  @override
  String get searchHint => 'Cari judul atau ringkasan';

  @override
  String get featured => 'Unggulan';

  @override
  String get latest => 'Terbaru';

  @override
  String get categories => 'Kategori';

  @override
  String get allCategories => 'Semua kategori';

  @override
  String get readMore => 'Baca selengkapnya';

  @override
  String get allArticles => 'Semua artikel';

  @override
  String get emptyPosts => 'Belum ada artikel.';

  @override
  String get emptyFaqs => 'Belum ada pertanyaan umum.';

  @override
  String get emptyQuestions => 'Belum ada pertanyaan yang sudah dijawab.';

  @override
  String get errorTitle => 'Tidak dapat memuat data';

  @override
  String get retry => 'Coba lagi';

  @override
  String get contact => 'Kontak';

  @override
  String get address => 'Alamat';

  @override
  String get phone => 'Telepon';

  @override
  String get email => 'Email';

  @override
  String get whatsapp => 'WhatsApp';

  @override
  String get officeHours => 'Jam layanan';

  @override
  String get previous => 'Sebelumnya';

  @override
  String get next => 'Berikutnya';

  @override
  String pageOf(int page, int pages) {
    return 'Halaman $page dari $pages';
  }

  @override
  String byAuthor(String name) {
    return 'Oleh $name';
  }

  @override
  String views(int count) {
    return '$count kali dilihat';
  }

  @override
  String resultCount(int count) {
    return '$count hasil';
  }

  @override
  String get notFound => 'Halaman tidak ditemukan';

  @override
  String get notFoundBody =>
      'Tautan ini tidak ada, atau kontennya belum diterbitkan.';

  @override
  String get questionsClosed => 'Penerimaan pertanyaan sedang ditutup.';

  @override
  String get submit => 'Kirim pertanyaan';

  @override
  String get successTitle => 'Pertanyaan terkirim';

  @override
  String get successBody =>
      'Pertanyaan Anda telah kami terima dan akan segera ditinjau.';

  @override
  String get backToQuestions => 'Kembali ke daftar';

  @override
  String get required => 'Wajib diisi';

  @override
  String get invalidEmail => 'Masukkan email yang valid';

  @override
  String get invalidPhone => 'Nomor telepon tidak valid';

  @override
  String get bodyTooShort => 'Isi pertanyaan minimal 10 karakter';

  @override
  String get name => 'Nama';

  @override
  String get topic => 'Topik';

  @override
  String get subject => 'Subjek';

  @override
  String get questionBody => 'Isi pertanyaan';

  @override
  String get optional => 'Opsional';

  @override
  String get answers => 'Jawaban';

  @override
  String askedOn(String date) {
    return 'Diajukan $date';
  }

  @override
  String get menu => 'Menu';

  @override
  String get language => 'Bahasa';

  @override
  String get indonesian => 'Bahasa Indonesia';

  @override
  String get english => 'English';

  @override
  String get footerNote => 'Situs informasi dan layanan publik.';

  @override
  String get clearSearch => 'Hapus pencarian';
}
