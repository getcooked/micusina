# Mi Cusina Flutter app

This is the legacy cross-platform Mi Cusina customer prototype. The current
Android release target and production build instructions are in
[`android-app`](../android-app/README.md). This Flutter prototype is preserved,
but is not part of the native Android release verification or an iOS release.

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
