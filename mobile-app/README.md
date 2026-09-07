# Mi Cusina Flutter app

This is the cross-platform Mi Cusina customer app. It uses the Laravel Sanctum API in the project root.

## First setup

Install the [Flutter SDK](https://docs.flutter.dev/get-started/install/windows/mobile), then from this directory run:

```bash
flutter create .
flutter pub get
flutter run --dart-define=API_BASE_URL=https://your-domain.com
```

For release builds, always use the HTTPS domain that serves this Laravel project:

```bash
flutter build appbundle --dart-define=API_BASE_URL=https://your-domain.com
flutter build ipa --dart-define=API_BASE_URL=https://your-domain.com
```

`API_BASE_URL` must not include `/api`; the app adds it automatically.
