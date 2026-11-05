import 'dart:convert';

import 'package:http/http.dart' as http;

import '../config.dart';
import 'api_exception.dart';

class ApiClient {
  ApiClient({
    String? baseUrl,
    required this.locale,
    http.Client? httpClient,
  }) : baseUrl = _trim(baseUrl ?? apiBaseUrl),
       _http = httpClient ?? http.Client(),
       _ownsClient = httpClient == null;

  final String baseUrl;
  final String locale;
  final http.Client _http;
  final bool _ownsClient;

  Future<Map<String, dynamic>> get(
    String path, [
    Map<String, String?> query = const {},
  ]) {
    return _send('GET', path, query: query);
  }

  Future<Map<String, dynamic>> post(
    String path,
    Map<String, Object?> body,
  ) {
    return _send('POST', path, body: body);
  }

  void close() {
    if (_ownsClient) {
      _http.close();
    }
  }

  Future<Map<String, dynamic>> _send(
    String method,
    String path, {
    Map<String, String?> query = const {},
    Map<String, Object?>? body,
  }) async {
    final parameters = <String, String>{'lang': locale};
    for (final entry in query.entries) {
      final value = entry.value?.trim();
      if (value != null && value.isNotEmpty) {
        parameters[entry.key] = value;
      }
    }

    final uri = Uri.parse('$baseUrl/api/v1$path').replace(
      queryParameters: parameters,
    );
    final headers = {
      'Accept': 'application/json',
      'Accept-Language': locale,
      if (body != null) 'Content-Type': 'application/json',
    };

    final http.Response response;
    try {
      response = switch (method) {
        'POST' => await _http.post(
          uri,
          headers: headers,
          body: jsonEncode(body),
        ),
        _ => await _http.get(uri, headers: headers),
      };
    } on http.ClientException catch (error) {
      throw ApiException(message: error.message);
    }

    Map<String, dynamic> payload = {};
    if (response.body.isNotEmpty) {
      final decoded = jsonDecode(response.body);
      if (decoded is Map<String, dynamic>) {
        payload = decoded;
      } else if (decoded is Map) {
        payload = Map<String, dynamic>.from(decoded);
      }
    }

    if (response.statusCode >= 400) {
      throw ApiException(
        statusCode: response.statusCode,
        message: payload['message'] as String? ?? 'Request failed',
        errors: _fieldErrors(payload['errors']),
      );
    }

    return payload;
  }

  Map<String, List<String>> _fieldErrors(Object? errors) {
    if (errors is! Map) {
      return const {};
    }

    return errors.map((key, value) {
      final messages = value is List
          ? value.map((item) => item.toString()).toList()
          : [value.toString()];

      return MapEntry(key.toString(), messages);
    });
  }

  static String _trim(String value) {
    return value.endsWith('/') ? value.substring(0, value.length - 1) : value;
  }
}
