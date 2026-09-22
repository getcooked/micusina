# Mi Cusina Expo mobile app

This React Native/Expo client uses Laravel Sanctum bearer tokens; it never embeds or scrapes the website.

## Run it

1. Copy `.env.example` to `.env`. The provided value targets the live API.
2. Run `npm install`, then `npx expo start`.
3. Scan the QR code with Expo Go, or press `a` for an Android emulator.

For local Laravel testing, set `EXPO_PUBLIC_API_BASE_URL` to `http://YOUR_COMPUTER_LAN_IP:8000/api/mobile` (not `localhost`, which means the phone itself). Start Laravel with `php artisan serve --host=0.0.0.0` and make sure the phone and computer share Wi-Fi.

The API accepts `Authorization: Bearer <Sanctum token>`. Native apps are not browser-based, so Laravel's browser CORS middleware is generally not involved. If you also run Expo web, add its origin to `config/cors.php`, clear config cache, and redeploy.

The current Laravel API permits staff order changes only to `Delivered` or `Canceled`; the interface reflects that server rule. Low stock is derived from `/staff/inventory` at a stock threshold of 5 because `/inventory/low-stock` is not defined in this backend.

## Android APK for the website download

Do not reuse the legacy `public/downloads/Mi-Cusina.apk`: it was built from the removed Android app. Build the Expo application as an installable APK with an Expo account:

```powershell
npx eas-cli login
npx eas-cli build --platform android --profile preview
```

When the EAS build finishes, download its APK and replace `public/downloads/Mi-Cusina.apk` with that exact file. Then run `php artisan test --filter=MobileAppDownloadTest`, commit the new APK, and deploy Laravel. `preview` deliberately produces an APK for direct installation; the `production` profile produces an AAB for Google Play.
