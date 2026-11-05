class PageResult<T> {
  const PageResult({
    required this.items,
    required this.page,
    required this.lastPage,
    required this.total,
  });

  final List<T> items;
  final int page;
  final int lastPage;
  final int total;

  bool get hasPrevious => page > 1;
  bool get hasNext => page < lastPage;

  factory PageResult.fromJson(
    Map<String, dynamic> json,
    T Function(Map<String, dynamic> json) parse,
  ) {
    final meta = _map(json['meta']);

    return PageResult(
      items: _list(json['data'], parse),
      page: _int(meta['current_page'], 1),
      lastPage: _int(meta['last_page'], 1),
      total: _int(meta['total'], 0),
    );
  }
}

class Category {
  const Category({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
    this.icon,
    this.postsCount,
  });

  final int id;
  final String name;
  final String slug;
  final String? description;
  final String? icon;
  final int? postsCount;

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: _int(json['id']),
      name: _string(json['name']),
      slug: _string(json['slug']),
      description: _optional(json['description']),
      icon: _optional(json['icon']),
      postsCount: json['posts_count'] == null ? null : _int(json['posts_count']),
    );
  }
}

class Post {
  const Post({
    required this.id,
    required this.title,
    required this.slug,
    this.summary,
    this.content,
    this.imageUrl,
    this.isFeatured = false,
    this.viewCount = 0,
    this.publishedAt,
    this.category,
    this.authorName,
  });

  final int id;
  final String title;
  final String slug;
  final String? summary;
  final String? content;
  final String? imageUrl;
  final bool isFeatured;
  final int viewCount;
  final DateTime? publishedAt;
  final Category? category;
  final String? authorName;

  factory Post.fromJson(Map<String, dynamic> json) {
    final author = _map(json['author']);

    return Post(
      id: _int(json['id']),
      title: _string(json['title']),
      slug: _string(json['slug']),
      summary: _optional(json['summary']),
      content: _optional(json['content']),
      imageUrl: _optional(json['image_url']),
      isFeatured: json['is_featured'] == true,
      viewCount: _int(json['view_count']),
      publishedAt: DateTime.tryParse(_string(json['published_at'])),
      category: json['category'] is Map
          ? Category.fromJson(_map(json['category']))
          : null,
      authorName: _optional(author['name']),
    );
  }
}

class ContentPage {
  const ContentPage({
    required this.id,
    required this.title,
    required this.slug,
    this.content,
    this.imageUrl,
  });

  final int id;
  final String title;
  final String slug;
  final String? content;
  final String? imageUrl;

  factory ContentPage.fromJson(Map<String, dynamic> json) {
    return ContentPage(
      id: _int(json['id']),
      title: _string(json['title']),
      slug: _string(json['slug']),
      content: _optional(json['content']),
      imageUrl: _optional(json['image_url']),
    );
  }
}

class Slide {
  const Slide({
    required this.id,
    required this.imageUrl,
    this.title,
    this.description,
    this.linkUrl,
  });

  final int id;
  final String imageUrl;
  final String? title;
  final String? description;
  final String? linkUrl;

  factory Slide.fromJson(Map<String, dynamic> json) {
    return Slide(
      id: _int(json['id']),
      imageUrl: _string(json['image_url']),
      title: _optional(json['title']),
      description: _optional(json['description']),
      linkUrl: _optional(json['link_url']),
    );
  }
}

class MenuItem {
  const MenuItem({
    required this.id,
    required this.label,
    required this.type,
    this.url,
    this.slug,
    this.children = const [],
  });

  final int id;
  final String label;
  final String type;
  final String? url;
  final String? slug;
  final List<MenuItem> children;

  factory MenuItem.fromJson(Map<String, dynamic> json) {
    return MenuItem(
      id: _int(json['id']),
      label: _string(json['label']),
      type: _string(json['type']),
      url: _optional(json['url']),
      slug: _optional(json['slug']),
      children: _list(json['children'], MenuItem.fromJson),
    );
  }
}

class Menu {
  const Menu({required this.name, required this.location, required this.items});

  final String name;
  final String location;
  final List<MenuItem> items;

  factory Menu.fromJson(Map<String, dynamic> json) {
    final data = json['data'] is Map ? _map(json['data']) : json;

    return Menu(
      name: _string(data['name']),
      location: _string(data['location']),
      items: _list(data['items'], MenuItem.fromJson),
    );
  }
}

class OfficeHour {
  const OfficeHour({required this.day, required this.hours});

  final String day;
  final String hours;
}

class SettingEntry {
  const SettingEntry({required this.key, required this.value});

  final String key;
  final Object? value;
}

class SiteSettings {
  const SiteSettings(this.entries);

  final Map<String, Object?> entries;

  factory SiteSettings.fromJson(Map<String, dynamic> json) {
    final entries = <String, Object?>{};
    for (final row in _list(json['data'], (item) => item)) {
      entries[_string(row['key'])] = row['value'];
    }

    return SiteSettings(entries);
  }

  String text(String key, {String fallback = ''}) {
    final value = entries[key];
    if (value == null) {
      return fallback;
    }

    final text = value.toString().trim();
    return text.isEmpty ? fallback : text;
  }

  String? optionalText(String key) {
    final value = text(key);
    return value.isEmpty ? null : value;
  }

  bool flag(String key, {bool fallback = false}) {
    final value = entries[key];
    if (value is bool) {
      return value;
    }
    if (value is num) {
      return value != 0;
    }
    if (value is String) {
      return value == '1' || value.toLowerCase() == 'true';
    }

    return fallback;
  }

  List<OfficeHour> get officeHours {
    final value = entries['office_hours'];
    if (value is! List) {
      return const [];
    }

    return value.whereType<Map>().map((row) {
      final item = Map<String, dynamic>.from(row);
      return OfficeHour(
        day: _string(item['day']),
        hours: _string(item['hours']),
      );
    }).where((hour) => hour.day.isNotEmpty).toList();
  }

  String get name => text('site_name', fallback: 'Portal');

  String? get tagline => optionalText('site_tagline');

  String? get description => optionalText('site_description');

  String? get logoUrl => optionalText('site_logo');

  bool get questionsEnabled => flag('questions_enabled', fallback: true);
}

class QuestionTopic {
  const QuestionTopic({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
  });

  final int id;
  final String name;
  final String slug;
  final String? description;

  factory QuestionTopic.fromJson(Map<String, dynamic> json) {
    return QuestionTopic(
      id: _int(json['id']),
      name: _string(json['name']),
      slug: _string(json['slug']),
      description: _optional(json['description']),
    );
  }
}

class Faq {
  const Faq({
    required this.id,
    required this.question,
    required this.answer,
    this.topic,
  });

  final int id;
  final String question;
  final String answer;
  final QuestionTopic? topic;

  factory Faq.fromJson(Map<String, dynamic> json) {
    return Faq(
      id: _int(json['id']),
      question: _string(json['question']),
      answer: _string(json['answer']),
      topic: json['topic'] is Map
          ? QuestionTopic.fromJson(_map(json['topic']))
          : null,
    );
  }
}

class Answer {
  const Answer({required this.id, required this.body, this.answeredAt});

  final int id;
  final String body;
  final DateTime? answeredAt;

  factory Answer.fromJson(Map<String, dynamic> json) {
    return Answer(
      id: _int(json['id']),
      body: _string(json['body']),
      answeredAt: DateTime.tryParse(_string(json['answered_at'])),
    );
  }
}

class Question {
  const Question({
    required this.id,
    required this.name,
    required this.subject,
    required this.body,
    required this.status,
    this.createdAt,
    this.topic,
    this.answers = const [],
  });

  final int id;
  final String name;
  final String subject;
  final String body;
  final String status;
  final DateTime? createdAt;
  final QuestionTopic? topic;
  final List<Answer> answers;

  factory Question.fromJson(Map<String, dynamic> json) {
    return Question(
      id: _int(json['id']),
      name: _string(json['name']),
      subject: _string(json['subject']),
      body: _string(json['body']),
      status: _string(json['status']),
      createdAt: DateTime.tryParse(_string(json['created_at'])),
      topic: json['topic'] is Map
          ? QuestionTopic.fromJson(_map(json['topic']))
          : null,
      answers: _list(json['answers'], Answer.fromJson),
    );
  }
}

class ShellData {
  const ShellData({
    required this.settings,
    this.header,
    this.footer,
  });

  final SiteSettings settings;
  final Menu? header;
  final Menu? footer;
}

List<T> _list<T>(Object? value, T Function(Map<String, dynamic> json) parse) {
  if (value is! List) {
    return const [];
  }

  return value.whereType<Map>().map((row) {
    return parse(Map<String, dynamic>.from(row));
  }).toList();
}

Map<String, dynamic> _map(Object? value) {
  if (value is Map<String, dynamic>) {
    return value;
  }
  if (value is Map) {
    return Map<String, dynamic>.from(value);
  }

  return const {};
}

String _string(Object? value) => value?.toString() ?? '';

String? _optional(Object? value) {
  final text = value?.toString().trim();
  if (text == null || text.isEmpty || text == 'null') {
    return null;
  }

  return text;
}

int _int(Object? value, [int fallback = 0]) {
  if (value is int) {
    return value;
  }
  if (value is num) {
    return value.toInt();
  }

  return int.tryParse(value?.toString() ?? '') ?? fallback;
}
