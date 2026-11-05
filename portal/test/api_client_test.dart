import 'dart:convert';

import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:portal/src/api/api_client.dart';
import 'package:portal/src/api/api_exception.dart';

void main() {
  test('requests send the active locale', () async {
    late Uri uri;
    late Map<String, String> headers;
    final client = MockClient((request) async {
      uri = request.url;
      headers = request.headers;
      return http.Response(jsonEncode({'data': []}), 200);
    });
    final api = ApiClient(
      baseUrl: 'http://127.0.0.1:8000/',
      locale: 'en',
      httpClient: client,
    );

    await api.get('/posts', {'search': 'libur', 'category': ''});

    expect(uri.path, '/api/v1/posts');
    expect(uri.queryParameters['lang'], 'en');
    expect(uri.queryParameters['search'], 'libur');
    expect(uri.queryParameters.containsKey('category'), isFalse);
    expect(headers['accept-language'], 'en');
  });

  test('validation errors keep the field messages', () async {
    final client = MockClient((request) async {
      return http.Response(
        jsonEncode({
          'message': 'Data tidak valid.',
          'errors': {
            'name': ['Nama wajib diisi.'],
          },
        }),
        422,
      );
    });
    final api = ApiClient(
      baseUrl: 'http://127.0.0.1:8000',
      locale: 'id',
      httpClient: client,
    );

    expect(
      () => api.post('/questions', {'name': ''}),
      throwsA(
        isA<ApiException>()
            .having((error) => error.statusCode, 'statusCode', 422)
            .having((error) => error.fieldError('name'), 'name', 'Nama wajib diisi.'),
      ),
    );
  });
}
