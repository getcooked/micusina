# Project-specific R8 rules. Android framework entry points declared in the
# manifest and BuildConfig fields are handled automatically by the Android
# Gradle plugin.

# Keep line numbers for useful de-obfuscated production crash reports while
# replacing source file names in the shipped bytecode.
-keepattributes SourceFile,LineNumberTable
-renamesourcefileattribute SourceFile

# Remove verbose application logging from optimized release builds.
-assumenosideeffects class android.util.Log {
    public static int v(...);
    public static int d(...);
    public static int i(...);
}
