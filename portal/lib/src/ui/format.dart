import 'package:intl/intl.dart';

String formatDate(DateTime? date, String locale) {
  if (date == null) {
    return '';
  }

  return DateFormat.yMMMMd(locale).format(date.toLocal());
}

String plainText(String? value) {
  if (value == null || value.isEmpty) {
    return '';
  }

  return value
      .replaceAll(RegExp(r'<[^>]*>'), ' ')
      .replaceAll('&nbsp;', ' ')
      .replaceAll(RegExp(r'\s+'), ' ')
      .trim();
}
