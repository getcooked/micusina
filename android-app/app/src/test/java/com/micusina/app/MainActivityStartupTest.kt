package com.micusina.app

import android.content.Context
import android.os.Looper
import android.view.View
import android.view.ViewGroup
import android.widget.EditText
import android.widget.TextView
import org.junit.Assert.assertEquals
import org.junit.Assert.assertFalse
import org.junit.Assert.assertTrue
import org.junit.Before
import org.junit.Test
import org.junit.runner.RunWith
import org.robolectric.Robolectric
import org.robolectric.RobolectricTestRunner
import org.robolectric.RuntimeEnvironment
import org.robolectric.Shadows.shadowOf
import org.robolectric.annotation.Config
import org.robolectric.annotation.GraphicsMode

/** Exercises the real startup view tree without signing in or contacting the API. */
@RunWith(RobolectricTestRunner::class)
@Config(sdk = [28, 35], qualifiers = "w390dp-h844dp-port-mdpi")
@GraphicsMode(GraphicsMode.Mode.LEGACY)
class MainActivityStartupTest {
    @Before
    fun clearLocalSession() {
        val application = RuntimeEnvironment.getApplication()
        listOf("mi_cusina_profile", "mi_cusina_secure", "mi_cusina_device").forEach { name ->
            application.getSharedPreferences(name, Context.MODE_PRIVATE).edit().clear().commit()
        }
    }

    @Test
    fun freshInstallDisplaysTheNativeSignInScreenAtPhoneSize() {
        Robolectric.buildActivity(MainActivity::class.java).use { controller ->
            val activity = controller.setup().visible().get()
            assertSignInScreen(activity)
            assertEquals(390, activity.resources.configuration.screenWidthDp)
            assertFalse(input(activity, "Password").isSaveEnabled)
            assertEquals(View.GONE, input(activity, "6-digit authentication code").visibility)
        }
    }

    @Test
    fun recreatingTheLoginScreenDoesNotRestoreTheEnteredPassword() {
        Robolectric.buildActivity(MainActivity::class.java).use { controller ->
            val activity = controller.setup().visible().get()
            assertSignInScreen(activity)
            input(activity, "Password").setText("unsent-test-only-password")

            controller.recreate()

            val recreated = controller.get()
            assertSignInScreen(recreated)
            assertTrue(input(recreated, "Password").text.isEmpty())
        }
    }

    @Test
    fun submittingEmptyCredentialsShowsValidationWithoutLeavingLogin() {
        Robolectric.buildActivity(MainActivity::class.java).use { controller ->
            val activity = controller.setup().visible().get()
            assertSignInScreen(activity)
            val signIn = textViews(activity).single { it.text.toString() == "Sign in" && it.isClickable }

            signIn.performClick()

            assertSignInScreen(activity)
            assertTrue(signIn.isEnabled)
            assertTrue(textViews(activity).any {
                it.text.toString() == activity.getString(R.string.login_validation_error) && it.isShown
            })
        }
    }

    private fun assertSignInScreen(activity: MainActivity) {
        shadowOf(Looper.getMainLooper()).idle()
        val content = activity.findViewById<ViewGroup>(android.R.id.content)
        val density = activity.resources.displayMetrics.density
        val width = (390 * density).toInt()
        val height = (844 * density).toInt()
        content.measure(
            View.MeasureSpec.makeMeasureSpec(width, View.MeasureSpec.EXACTLY),
            View.MeasureSpec.makeMeasureSpec(height, View.MeasureSpec.EXACTLY),
        )
        content.layout(0, 0, width, height)

        assertFalse(activity.isFinishing)
        assertFalse(activity.isDestroyed)
        assertEquals(width, content.measuredWidth)
        listOf("Welcome to Mi Cusina", "Sign in", "Create customer account").forEach { text ->
            assertTrue("Missing startup control: $text", textViews(activity).any {
                it.text.toString() == text && it.isShown
            })
        }
        assertTrue(input(activity, "Email address").isShown)
        assertTrue(input(activity, "Password").isShown)
    }

    private fun input(activity: MainActivity, hint: String): EditText =
        textViews(activity).filterIsInstance<EditText>().single { it.hint.toString() == hint }

    private fun textViews(activity: MainActivity): List<TextView> =
        descendants(activity.findViewById<View>(android.R.id.content)).filterIsInstance<TextView>().toList()

    private fun descendants(view: View): Sequence<View> = sequence {
        yield(view)
        if (view is ViewGroup) {
            for (index in 0 until view.childCount) yieldAll(descendants(view.getChildAt(index)))
        }
    }
}
