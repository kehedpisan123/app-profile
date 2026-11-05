/// Base URL of the Laravel application, without a trailing slash.
///
/// Override it at run time with `--dart-define=API_BASE_URL=http://10.0.2.2:8000`
/// when the app runs on an Android emulator.
const String apiBaseUrl = String.fromEnvironment(
  'API_BASE_URL',
  defaultValue: 'http://127.0.0.1:8000',
);
