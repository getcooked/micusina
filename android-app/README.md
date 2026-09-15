# Mi Cusina Android app

Mi Cusina is a native Kotlin client for the Laravel mobile API. `MainActivity`
is the launcher and presents role-aware customer, staff, and administrator
destinations. The former injected-WebView shell has been retired.

## Current capabilities

- Customer sign-in, menu, cart, checkout, orders, and table reservations
- Staff dashboard and order fulfillment actions
- Administrator inventory updates
- Role-aware bottom navigation and in-app loading, error, empty, and retry states
- Scrollable navigation rail on wide screens, accessible controls, and a cart count badge
- Customer Help tab with common questions, order/reservation shortcuts, and call/email handoff
- Restored navigation, menu search, scroll position, and checkout/reservation drafts
- External browser handoff for registration, website support, and payment pages

The server-provided account role determines the available navigation. The app
does not ask users to choose or impersonate a role.

## Navigation

| Account | Main destinations |
| --- | --- |
| Customer | Menu, Reserve, Orders, Help, Account |
| Administrator / cashier | Home, Orders, Inventory, Account |
| Rider | Home, Orders, Account |
| Other staff | Home, Inventory, Account |

The customer bar follows the reference's five-tab order while preserving
Mi Cusina's original pink, purple, and white palette. The selected icon has a
rounded indicator, with its label below. Cart is always available from the
top-right toolbar on customer tabs, including its live item-count badge; it
does not take up a sixth bottom tab. Cart keeps Menu selected, and tapping Menu
from Cart returns to the menu. Help uses the contact details already published
in `resources/views/home/footer.blade.php`; call/email buttons open the phone's
dialer/mail app and never place a call or send a message automatically.

Only administrators can edit inventory. Back returns from checkout to Cart,
then Menu, from a reservation form to Reserve, and from the gallery to Account. Reselecting
the active tab scrolls to the top; the toolbar refresh action reloads current
data without clearing the menu search. Tabs move into a scrollable side rail at
600dp screen width; the content remains scrollable when the keyboard is open.

Search text, scroll position, and delivery/reservation drafts survive Activity
recreation. Passwords and authentication codes are not saved in instance state.
If checkout or reservation creation is interrupted, the next launch opens the
corresponding history list and asks the customer to review it before submitting
again. This is recovery guidance, not server-side request idempotency; never
automatically retry a payment or reservation creation request.

The Android build in this directory is the release target. The separate
`mobile-app` Flutter prototype is not included in this release or its checks.

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

The website APK at `public/downloads/Mi-Cusina.apk` is the native v3 release.
The previous v2.7.0 download was a debug-signed website wrapper that forced a
1280px desktop viewport. It is not fixed by changing the website's phone CSS;
customers must install the replacement APK. The new app opens a native sign-in
screen and shows the installed version below it. After sign-in, customers get
Menu, Reserve, Orders, Help, and Account tabs sized for the phone.

Direct-install users must uninstall the old debug-signed app once before
installing the first production-signed build. This clears on-device sessions
and drafts; server-side accounts and orders remain on the server. Future
releases must use this same production key and a higher version code so they
can update in place. Store-managed installs should follow the store's
signing-key migration process. The separate debug build is labeled
`Mi Cusina Preview` and must never be used for the customer download.

### Local Windows signing helper

`build-release.ps1` keeps the production keystore in the ignored `.signing/`
directory outside the web root. It restricts access to the creating Windows
account and SYSTEM, and encrypts the generated password using Windows DPAPI.
No key or password is checked in, printed, or passed as a command-line value.
Set `JAVA_HOME` to your JDK 17 installation and `ANDROID_HOME` to the SDK first.

One-time creation of a **new** signing identity (never run this to update an
already signed app):

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\build-release.ps1 -InitializeSigning
```

Build, test, and stage subsequent releases with the existing protected key:

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\build-release.ps1 -StageWebsiteApk
```

The execution-policy setting applies only to this helper process. The helper
refuses to initialize over an existing `.signing/` directory. It decrypts the
password only for the build process, then restores previous environment
settings. This helper does not push or deploy anything.

**Key backup is essential before public distribution.** The DPAPI password
file is tied to the current Windows account and computer; copying `.signing/`
alone is not a portable recovery plan. Securely store both the PKCS12 keystore
and its decrypted password in your organization's protected backup/password
manager while you still have access to this account. Never commit these files,
put them in `public/`, or send the password in chat. Losing this identity means
existing direct installs cannot accept future updates signed with a new key.

### Updating the website download

With signing configured locally or through protected CI settings, run:

```powershell
.\gradlew.bat --no-daemon :app:stageWebsiteApk
```

This task runs release lint, navigation tests, and release assembly, verifies
the APK's signature and native launcher, rejects debug certificates and
debuggable/wrong-package builds, and atomically replaces only
`public/downloads/Mi-Cusina.apk`. A failed check preserves the previous APK.
Keep the generated AAB and R8 mapping with your release archive.

Commit/push the verified public APK and deploy the matching Laravel revision
separately (see `../HOSTINGER.md`). Local builds do not change the live phone
download or an already installed app. After deployment, download
`https://micusina-pos.com/download-app`, confirm the downloaded file's SHA-256
matches the staged artifact, and install it on a physical phone. Do not copy
the private `.signing/` directory to the website host.

The current native customer release is version `3.1.0` (code `20`), 8,635,217
bytes. Its APK SHA-256 is
`C656EDA4E6F6AE59CFB3E4178F13BC3D14EA7124EFA271629FE3E4DB23FFA5D2`.
The production certificate SHA-256 fingerprint is
`F694D12923069530FE9540CF6C3852D6F1BBAB6A4B6192B0D7BA3B065A7562D5`.
These are public verification values, not signing secrets. Update the APK
checksum for each release; keep the signing certificate unchanged for updates.
Version 3.1.0 uses the same certificate as native v3.0.0, so those installations
can update without uninstalling. Only the old debug-signed v2.7.0 wrapper needs
the one-time uninstall described above.

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
The JVM navigation tests verify role restrictions, detail-screen parents, and
restoration after interrupted submissions. Laravel regression tests cover
mobile authentication, cart ownership, checkout, delivery updates, and signed
payment callbacks/webhooks.

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
8. Deploy the matching Laravel API and apply pending migrations after taking
   a database backup. Confirm order identity, unique cart rows, and stored
   reservation checkout URLs exist before enabling the rebuilt app.

### Device smoke checks

- Sign in as a customer, a cashier, and a rider; confirm each gets only its tabs.
- Confirm the customer bar reads Menu, Reserve, Orders, Help, Account, with the
  original colors, a visible selected indicator, and no clipping at 320dp width.
- Open Cart from each customer tab; check its badge and Back to Menu. Checkout
  must still open and Back must return to Cart. Tapping Menu from Cart returns
  to the menu rather than only scrolling Cart.
- After signing in, turn off connectivity and open Help; then test order/reservation shortcuts and
  call/email handoff (also on a device without a dialer/mail app).
- Complete the authenticator challenge for a two-factor-enabled account.
- Add, increment, remove, and checkout items; confirm the badge and totals update.
- Rotate during menu search and during an unfinished form; check the draft and tab.
- Rotate or interrupt connectivity during submission; check history before retrying.
- Complete/cancel a sandbox reservation payment; return and resume a pending booking.
- Verify gesture Back, keyboard visibility, TalkBack labels, large text, and
  landscape navigation on a phone and a wide-screen device.
