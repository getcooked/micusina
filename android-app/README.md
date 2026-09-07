# Mi Cusina Kotlin Android app

This native Android project is implemented in Kotlin and provides a Mi Cusina app for both customers and staff. At launch,
the user chooses a role:

- **Customer:** opens the menu, cart, checkout, order tracking, and reservations.
- **Staff:** opens the existing staff/admin dashboard after sign-in, including orders,
  deliveries, inventory, and reservations (according to the user's existing role).

The app keeps each user's website login session and supports image selection for staff uploads.
Payment-provider links open in the phone's browser, where they are safer and more reliable.

## Included features

- Customer sign-in and secure persisted session
- Website registration link for new customers
- Menu browsing and stock-aware add-to-cart actions
- Cart quantity changes, removal, and running totals
- Checkout with validated Bantayan Island municipality and payment choices
- Customer order history and status tracking
- Staff dashboard, delivery status management, and cancellation
- Administrator inventory stock updates

## Configure the live website

In `app/build.gradle`, replace:

`https://your-mi-cusina-domain.com`

with your deployed HTTPS website address, for example `https://micusina.com`.

## Build the APK

From this folder, run:

`gradlew.bat assembleDebug`

The APK is created at `app/build/outputs/apk/debug/app-debug.apk`.

## Before releasing

- Use a public **HTTPS** URL for the Laravel site. The app intentionally blocks unencrypted HTTP.
- Test customer checkout, staff login, image uploads, and the external payment return flow on a phone.
- Create and protect your own release signing key before publishing. Do not share a signing key or its password in source control.
