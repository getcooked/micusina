# Mi Cusina Android app

This native Android project opens the Mi Cusina customer website in a mobile WebView.

## Configure the live website

In `app/build.gradle`, replace:

`https://your-mi-cusina-domain.com`

with your deployed HTTPS website address, for example `https://micusina.com`.

## Build the APK

From this folder, run:

`gradlew.bat assembleDebug`

The APK is created at `app/build/outputs/apk/debug/app-debug.apk`.
