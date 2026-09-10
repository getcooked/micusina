# Mi Cusina Android app

Mi Cusina is a native Kotlin client for the Laravel mobile API. `MainActivity`
is the launcher and presents role-aware customer, staff, and administrator
destinations. The former injected-WebView shell has been retired.

## Current capabilities

- Customer sign-in, menu, cart, checkout, orders, and table reservations
- Staff dashboard and order fulfillment actions
- Administrator inventory updates
- Role-aware bottom navigation and in-app loading, error, empty, and retry states
- External browser handoff for registration, website support, and payment pages

The server-provided account role determines the available navigation. The app
does not ask users to choose or impersonate a role.

## Requirements

- Android Studio Meerkat Feature Drop (2024.3.2) or newer with Android SDK 36
- JDK 17 (required by the Android Gradle plugin)
- A reachable Laravel deployment with the routes under `/api/mobile`

## Build environments

Debug and release builds intentionally use separate application IDs and server
defaults:

| Build | Application ID | API default | Cleartext traffic |
| --- | --- | --- | --- |
| Debug | `com.micusina.app.debug` | `http://10.0.2.2:8000/api/mobile` | Allowed for local emulator development only |
| Release | `com.micusina.app` | `https://micusina-pos.com/api/mobile` | Blocked |

`10.0.2.2` is the Android emulator's route to a Laravel server running on the
development computer. Override any URL through a Gradle property or an
environment variable with the same name:

- `MICUSINA_DEBUG_API_BASE_URL`
- `MICUSINA_DEBUG_WEB_BASE_URL`
- `MICUSINA_RELEASE_API_BASE_URL`
- `MICUSINA_RELEASE_WEB_BASE_URL`

Base URLs must be absolute and should not end with `/`. Release URLs are
validated as HTTPS during Gradle configuration.

Example PowerShell debug build using a remote development API:

```powershell
.\gradlew.bat assembleDebug `
  -PMICUSINA_DEBUG_API_BASE_URL=https://dev.example.com/api/mobile `
  -PMICUSINA_DEBUG_WEB_BASE_URL=https://dev.example.com
```

## Release signing

Release signing has no checked-in key or password fallback. Supply all four
values through environment variables, command-line Gradle properties, or an
untracked user-level Gradle configuration:

- `MICUSINA_RELEASE_STORE_FILE` — absolute path, or a path relative to this project
- `MICUSINA_RELEASE_STORE_PASSWORD`
- `MICUSINA_RELEASE_KEY_ALIAS`
- `MICUSINA_RELEASE_KEY_PASSWORD`

If only some signing values are present, Gradle fails immediately. With none
present, it can create an unsigned release for verification; a publishable AAB
requires the complete signing configuration.

The legacy `micusina-release.keystore` and its former password were committed
to the repository. Treat that key as compromised: rotate it through the app
store process if it was ever used, remove it from the repository and history in
a separately reviewed cleanup, and store the replacement only in protected CI
secrets. `.gitignore` now prevents new keystores and Android build outputs from
being added accidentally.

The APK currently stored at `public/downloads/Mi-Cusina.apk` is the legacy
v2.7.0 debuggable build signed with Android's debug certificate. The v3 build
must not overwrite that download until it is signed with a protected production
key and verified. Direct-install users of the debug-signed package will need to
uninstall it before installing the first properly signed production build;
store-managed installs should follow the store's signing-key migration process.

## Build and verify

Run from `android-app`:

```powershell
.\gradlew.bat --no-daemon :app:lintDebug :app:lintRelease :app:testDebugUnitTest :app:assembleDebug
.\gradlew.bat --no-daemon :app:assembleRelease :app:bundleRelease
```

The debug APK is written below `app/build/outputs/apk/debug/`. The release app
bundle is written below `app/build/outputs/bundle/release/`. Optimized releases
enable R8 code shrinking and resource shrinking. Retain the generated mapping
file from `app/build/outputs/mapping/release/` for crash de-obfuscation.
The repository's `Tests` GitHub Actions workflow runs the same release lint,
test, R8, and assembly checks for every push and pull request.

## Release checklist

1. Point release URLs at the verified HTTPS production deployment.
2. Configure a protected upload key and enable app-store-managed signing.
3. Increment `versionCode` and update `versionName`.
4. Run Android lint/tests and the Laravel API test suite in CI.
5. Test customer checkout, role-based staff access, offline recovery, and the
   external payment return flow on physical phones.
6. Archive the signed AAB, mapping file, and release notes together.
7. Register the developer identity and `com.micusina.app` package in Play
   Console (or Android Developer Console for off-Play distribution) before the
   applicable Android developer-verification deadline.
