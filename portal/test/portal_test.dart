import 'package:flutter_test/flutter_test.dart';
import 'package:portal/src/models/models.dart';
import 'package:portal/src/routing/paths.dart';

void main() {
  test('menu items resolve to portal routes', () {
    const page = MenuItem(
      id: 1,
      label: 'Tentang Kami',
      type: 'page',
      slug: 'tentang-kami',
    );
    const category = MenuItem(
      id: 2,
      label: 'Berita',
      type: 'category',
      slug: 'berita',
    );
    const home = MenuItem(id: 3, label: 'Beranda', type: 'custom', url: '/');
    const faq = MenuItem(id: 4, label: 'FAQ', type: 'custom', url: '/faq');
    const parent = MenuItem(id: 5, label: 'Profil', type: 'custom', url: '#');
    const external = MenuItem(
      id: 6,
      label: 'Situs',
      type: 'custom',
      url: 'https://example.com',
    );

    expect(
      destinationForMenuItem(page, 'en'),
      const InAppDestination('/en/pages/tentang-kami'),
    );
    expect(
      destinationForMenuItem(category, 'id'),
      const InAppDestination('/id/posts?category=berita'),
    );
    expect(destinationForMenuItem(home, 'id'), const InAppDestination('/id'));
    expect(destinationForMenuItem(faq, 'id'), const InAppDestination('/id/faq'));
    expect(destinationForMenuItem(parent, 'id'), isNull);
    expect(
      destinationForMenuItem(external, 'id'),
      ExternalDestination(Uri.parse('https://example.com')),
    );
  });

  test('locale stays in the path when the language changes', () {
    final next = replaceLocale(
      Uri.parse('/id/posts?category=berita'),
      'en',
    );

    expect(next, '/en/posts?category=berita');
    expect(localeFromPath(Uri.parse(next)), 'en');
  });

  test('settings and posts parse the api payload', () {
    final settings = SiteSettings.fromJson({
      'data': [
        {'key': 'site_name', 'value': 'Profil Instansi'},
        {'key': 'questions_enabled', 'value': true},
        {
          'key': 'office_hours',
          'value': [
            {'day': 'Senin - Kamis', 'hours': '08.00 - 16.00'},
          ],
        },
      ],
    });
    final posts = PageResult.fromJson({
      'data': [
        {
          'id': 1,
          'title': 'Pengumuman',
          'slug': 'pengumuman',
          'summary': '<p>Ringkasan</p>',
          'is_featured': false,
          'view_count': 4,
          'category': {'id': 2, 'name': 'Berita', 'slug': 'berita'},
          'author': {'name': 'Admin'},
        },
      ],
      'meta': {'current_page': 1, 'last_page': 3, 'total': 21},
    }, Post.fromJson);

    expect(settings.name, 'Profil Instansi');
    expect(settings.questionsEnabled, isTrue);
    expect(settings.officeHours.single.day, 'Senin - Kamis');
    expect(posts.total, 21);
    expect(posts.items.single.category?.slug, 'berita');
    expect(posts.items.single.authorName, 'Admin');
  });
}
