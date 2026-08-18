# Mi Cusina Android app

This native Android project opens Mi Cusina in a mobile WebView. On launch, users
choose either Customer (menu and ordering) or Staff (staff dashboard). Website
permissions continue to be enforced by the Mi Cusina account they sign in with.

## Configure the live website

The app is configured to use `https://micusina-pos.com`. Update `WEBSITE_URL` in
`app/build.gradle` if the production address changes.

## Build the APK

From this folder, run:

`gradlew.bat assembleDebug`

The APK is created at `app/build/outputs/apk/debug/app-debug.apk`.
