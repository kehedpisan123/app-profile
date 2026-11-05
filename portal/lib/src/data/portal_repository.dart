import '../api/api_client.dart';
import '../api/api_exception.dart';
import '../models/models.dart';

class PortalRepository {
  PortalRepository({
    required String locale,
    String? baseUrl,
    ApiClient? client,
  }) : _client = client ?? ApiClient(baseUrl: baseUrl, locale: locale);

  final ApiClient _client;

  String get locale => _client.locale;

  Future<ShellData> shell() async {
    final results = await Future.wait([
      settings(),
      menuOrNull('header'),
      menuOrNull('footer'),
    ]);

    return ShellData(
      settings: results[0] as SiteSettings,
      header: results[1] as Menu?,
      footer: results[2] as Menu?,
    );
  }

  Future<SiteSettings> settings() async {
    return SiteSettings.fromJson(await _client.get('/settings'));
  }

  Future<Menu> menu(String location) async {
    return Menu.fromJson(await _client.get('/menus/$location'));
  }

  Future<Menu?> menuOrNull(String location) async {
    try {
      return await menu(location);
    } on ApiException catch (error) {
      if (error.isNotFound) {
        return null;
      }
      rethrow;
    }
  }

  Future<List<Slide>> slides() async {
    final json = await _client.get('/slides');
    return _list(json, Slide.fromJson);
  }

  Future<List<Category>> categories() async {
    final json = await _client.get('/categories');
    return _list(json, Category.fromJson);
  }

  Future<PageResult<Post>> posts({
    String? category,
    String? search,
    bool? featured,
    int page = 1,
    int perPage = 9,
  }) async {
    final json = await _client.get('/posts', {
      'category': category,
      'search': search,
      'featured': featured == null ? null : (featured ? '1' : '0'),
      'page': '$page',
      'per_page': '$perPage',
    });

    return PageResult.fromJson(json, Post.fromJson);
  }

  Future<Post> post(String slug) async {
    final json = await _client.get('/posts/$slug');
    return Post.fromJson(_data(json));
  }

  Future<ContentPage> page(String slug) async {
    final json = await _client.get('/pages/$slug');
    return ContentPage.fromJson(_data(json));
  }

  Future<List<Faq>> faqs() async {
    final json = await _client.get('/faqs');
    return _list(json, Faq.fromJson);
  }

  Future<List<QuestionTopic>> topics() async {
    final json = await _client.get('/question-topics');
    return _list(json, QuestionTopic.fromJson);
  }

  Future<PageResult<Question>> questions({
    String? topic,
    String? search,
    int page = 1,
  }) async {
    final json = await _client.get('/questions', {
      'topic': topic,
      'search': search,
      'page': '$page',
    });

    return PageResult.fromJson(json, Question.fromJson);
  }

  Future<Question> question(int id) async {
    final json = await _client.get('/questions/$id');
    return Question.fromJson(_data(json));
  }

  Future<void> ask(Map<String, Object?> body) async {
    await _client.post('/questions', body);
  }

  void close() => _client.close();
}

List<T> _list<T>(
  Map<String, dynamic> json,
  T Function(Map<String, dynamic> json) parse,
) {
  final data = json['data'];
  if (data is! List) {
    return const [];
  }

  return data.whereType<Map>().map((row) {
    return parse(Map<String, dynamic>.from(row));
  }).toList();
}

Map<String, dynamic> _data(Map<String, dynamic> json) {
  final data = json['data'];
  if (data is Map<String, dynamic>) {
    return data;
  }
  if (data is Map) {
    return Map<String, dynamic>.from(data);
  }

  return json;
}
