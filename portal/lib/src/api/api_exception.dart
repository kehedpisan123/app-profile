class ApiException implements Exception {
  const ApiException({
    required this.message,
    this.statusCode,
    this.errors = const {},
  });

  final String message;
  final int? statusCode;
  final Map<String, List<String>> errors;

  bool get isNotFound => statusCode == 404;

  String? fieldError(String field) {
    final messages = errors[field];
    if (messages == null || messages.isEmpty) {
      return null;
    }

    return messages.first;
  }

  @override
  String toString() => message;
}
