package com.micusina.app

import android.app.Activity
import android.app.AlertDialog
import android.app.DatePickerDialog
import android.app.TimePickerDialog
import android.content.Context
import android.content.Intent
import android.content.res.ColorStateList
import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.graphics.Color
import android.graphics.Typeface
import android.graphics.drawable.GradientDrawable
import android.graphics.drawable.RippleDrawable
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.security.keystore.KeyGenParameterSpec
import android.security.keystore.KeyProperties
import android.text.Editable
import android.text.InputType
import android.text.TextWatcher
import android.util.Base64
import android.util.LruCache
import android.view.Gravity
import android.view.View
import android.view.ViewGroup
import android.view.WindowInsets
import android.view.inputmethod.EditorInfo
import android.widget.AdapterView
import android.widget.ArrayAdapter
import android.widget.EditText
import android.widget.FrameLayout
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.ProgressBar
import android.widget.ScrollView
import android.widget.Spinner
import android.widget.TextView
import android.widget.Toast
import org.json.JSONArray
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL
import java.lang.ref.WeakReference
import java.security.KeyStore
import java.text.NumberFormat
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale
import java.util.TimeZone
import java.util.UUID
import java.util.concurrent.ConcurrentHashMap
import java.util.concurrent.Executors
import javax.crypto.Cipher
import javax.crypto.KeyGenerator
import javax.crypto.SecretKey
import javax.crypto.spec.GCMParameterSpec

/** Native, API-driven Mi Cusina application for customers, staff, and administrators. */
class MainActivity : Activity() {
    private lateinit var preferences: android.content.SharedPreferences
    private lateinit var tokenStore: SecureTokenStore
    private lateinit var page: LinearLayout

    private var token = ""
    private var role = "user"
    private var userName = "Guest"
    private var userEmail = ""
    private var userPhone = ""
    private var staffRole = ""
    private var currentDestination = ""
    private var detailReturnDestination: String? = null
    private var pageGeneration = 0
    private var sessionGeneration = 0
    private var loginInFlight = false
    private var cartMutationInFlight = false
    private var orderMutationInFlight = false
    private var checkoutMutationInFlight = false
    private var reservationMutationInFlight = false
    private var paymentBrowserOpen = false

    private val requestExecutor = Executors.newFixedThreadPool(3)
    private val imageExecutor = Executors.newFixedThreadPool(2)
    private val pendingImages = ConcurrentHashMap<String, MutableList<WeakReference<ImageView>>>()

    private val imageCache = object : LruCache<String, Bitmap>(12 * 1024) {
        override fun sizeOf(key: String, value: Bitmap): Int = value.byteCount / 1024
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        configureWindow()
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            onBackInvokedDispatcher.registerOnBackInvokedCallback(android.window.OnBackInvokedDispatcher.PRIORITY_DEFAULT) { handleBack() }
        }

        preferences = getSharedPreferences(PREFERENCES, Context.MODE_PRIVATE)
        tokenStore = SecureTokenStore(this)
        token = tokenStore.read().orEmpty()
        role = preferences.getString(KEY_ROLE, "user").orEmpty()
        userName = preferences.getString(KEY_NAME, "Guest").orEmpty()
        userEmail = preferences.getString(KEY_EMAIL, "").orEmpty()
        userPhone = preferences.getString(KEY_PHONE, "").orEmpty()
        staffRole = preferences.getString(KEY_STAFF_ROLE, "").orEmpty()

        if (token.isBlank()) showLogin() else verifySession()
    }

    private fun verifySession() {
        showLaunchState("Preparing your kitchen…")
        request("GET", "/me", onSuccess = { response ->
            saveUser(response.getJSONObject("user"))
            showHome()
        }, onError = { message, code ->
            if (code == 401) expireSession() else showOfflineLaunch(message)
        }, handleUnauthorized = false)
    }

    private fun showLaunchState(message: String) {
        pageGeneration++
        currentDestination = "launch"
        detailReturnDestination = null
        val root = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            gravity = Gravity.CENTER
            setPadding(dp(32), dp(32), dp(32), dp(32))
            setBackgroundColor(BACKGROUND)
            addView(ImageView(this@MainActivity).apply {
                setImageResource(R.drawable.auth_food_combo_cutout)
                scaleType = ImageView.ScaleType.CENTER_INSIDE
                contentDescription = "Mi Cusina"
            }, LinearLayout.LayoutParams(dp(190), dp(150)))
            addView(title("Mi Cusina", 34f).apply { gravity = Gravity.CENTER })
            addView(ProgressBar(this@MainActivity).apply { indeterminateTintList = ColorStateList.valueOf(PRIMARY) }, marginParams(dp(34), dp(34)).apply { gravity = Gravity.CENTER_HORIZONTAL })
            addView(bodyText(message).apply { gravity = Gravity.CENTER })
        }
        setContentView(root)
    }

    private fun showOfflineLaunch(message: String) {
        showLaunchState("We couldn't connect to Mi Cusina.")
        findContentRoot().apply {
            addView(caption(message).apply { gravity = Gravity.CENTER })
            addView(primaryButton("Try again") { verifySession() }, topMargin(dp(22)))
            addView(secondaryButton("Sign in again") { clearSession(); showLogin() }, topMargin(dp(10)))
        }
    }

    private fun findContentRoot(): LinearLayout = (findViewById<ViewGroup>(android.R.id.content).getChildAt(0) as LinearLayout)

    private fun showLogin() {
        loginInFlight = false
        pageGeneration++
        currentDestination = "login"
        detailReturnDestination = null
        val scroll = ScrollView(this).apply { isFillViewport = true; setBackgroundColor(BACKGROUND) }
        val root = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            gravity = Gravity.CENTER_HORIZONTAL
            setPadding(dp(24), dp(24), dp(24), dp(32))
        }
        scroll.addView(root, ViewGroup.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT))
        root.addView(ImageView(this).apply {
            setImageResource(R.drawable.auth_food_combo_cutout)
            scaleType = ImageView.ScaleType.CENTER_INSIDE
            contentDescription = "Mi Cusina food"
        }, LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, dp(210)))
        root.addView(title("Welcome to Mi Cusina", 30f).apply { gravity = Gravity.CENTER })
        root.addView(caption("Fresh local favorites, simple ordering, and live delivery updates.").apply { gravity = Gravity.CENTER; textAlignment = View.TEXT_ALIGNMENT_CENTER }, topMargin(dp(8)))

        val card = card().apply { orientation = LinearLayout.VERTICAL }
        val email = field("Email address", InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_EMAIL_ADDRESS).apply {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) setAutofillHints(View.AUTOFILL_HINT_EMAIL_ADDRESS)
        }
        val password = field("Password", InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD).apply {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) setAutofillHints(View.AUTOFILL_HINT_PASSWORD)
            imeOptions = EditorInfo.IME_ACTION_DONE
        }
        val twoFactor = field("6-digit authentication code", InputType.TYPE_CLASS_NUMBER or InputType.TYPE_NUMBER_VARIATION_PASSWORD).apply {
            filters = arrayOf(android.text.InputFilter.LengthFilter(6))
            imeOptions = EditorInfo.IME_ACTION_DONE
            visibility = View.GONE
        }
        val error = caption("").apply { setTextColor(ERROR); visibility = View.GONE }
        val signIn = primaryButton("Sign in") { }
        card.addView(sectionTitle("Sign in"))
        card.addView(email, topMargin(dp(14)))
        card.addView(password, topMargin(dp(12)))
        card.addView(twoFactor, topMargin(dp(12)))
        card.addView(error, topMargin(dp(8)))
        card.addView(signIn, topMargin(dp(18)))
        root.addView(card, topMargin(dp(26)))

        fun submit() {
            if (loginInFlight) return
            val emailValue = email.text.toString().trim()
            val passwordValue = password.text.toString()
            if (!android.util.Patterns.EMAIL_ADDRESS.matcher(emailValue).matches() || passwordValue.isBlank()) {
                error.text = getString(R.string.login_validation_error)
                error.visibility = View.VISIBLE
                return
            }
            val needsTwoFactor = twoFactor.visibility == View.VISIBLE
            val twoFactorCode = twoFactor.text.toString().trim()
            if (needsTwoFactor && !Regex("^[0-9]{6}$").matches(twoFactorCode)) {
                error.text = "Enter the 6-digit code from your authenticator app."
                error.visibility = View.VISIBLE
                return
            }
            loginInFlight = true
            setButtonBusy(signIn, true, "Signing in…")
            error.visibility = View.GONE
            val payload = JSONObject()
                .put("email", emailValue)
                .put("password", passwordValue)
                .put("device_name", deviceName())
            if (needsTwoFactor) payload.put("two_factor_code", twoFactorCode)
            request("POST", "/login", payload, onSuccess = { response ->
                if (response.optBoolean("two_factor_required", false)) {
                    loginInFlight = false
                    twoFactor.visibility = View.VISIBLE
                    password.imeOptions = EditorInfo.IME_ACTION_NEXT
                    setButtonBusy(signIn, false, "Verify & sign in")
                    error.text = response.optString("message").ifBlank { "Enter the code from your authenticator app." }
                    error.visibility = View.VISIBLE
                    twoFactor.requestFocus()
                } else {
                    val issuedToken = response.optString("token")
                    val user = response.optJSONObject("user")
                    if (issuedToken.isBlank() || user == null) {
                        loginInFlight = false
                        error.text = response.optString("message").ifBlank { "Sign-in could not be completed. Please try again." }
                        error.visibility = View.VISIBLE
                        setButtonBusy(signIn, false, if (needsTwoFactor) "Verify & sign in" else "Sign in")
                    } else {
                        loginInFlight = false
                        token = issuedToken
                        sessionGeneration++
                        tokenStore.write(token)
                        saveUser(user)
                        showHome()
                    }
                }
            }, onError = { message, _ ->
                loginInFlight = false
                error.text = message
                error.visibility = View.VISIBLE
                setButtonBusy(signIn, false, if (twoFactor.visibility == View.VISIBLE) "Verify & sign in" else "Sign in")
            }, handleUnauthorized = false, acceptedErrorCodes = setOf(409))
        }
        signIn.setOnClickListener { submit() }
        password.setOnEditorActionListener { _, action, _ ->
            when (action) {
                EditorInfo.IME_ACTION_NEXT -> { twoFactor.requestFocus(); true }
                EditorInfo.IME_ACTION_DONE -> { submit(); true }
                else -> false
            }
        }
        twoFactor.setOnEditorActionListener { _, action, _ -> if (action == EditorInfo.IME_ACTION_DONE) { submit(); true } else false }
        root.addView(secondaryButton("Create customer account") { openWebsite("/register") }, topMargin(dp(14)))
        root.addView(textButton("Forgot your password?") { openWebsite("/forgot-password") }, topMargin(dp(10)))
        root.addView(caption("By continuing, you agree to use Mi Cusina's secure ordering service.").apply { gravity = Gravity.CENTER; textAlignment = View.TEXT_ALIGNMENT_CENTER }, topMargin(dp(18)))
        setContentView(scroll)
    }

    private fun saveUser(user: JSONObject) {
        role = user.optString("usertype", "user")
        userName = user.optString("name", "Customer")
        userEmail = user.optString("email", "")
        userPhone = user.optString("phone", "")
        staffRole = user.optString("staff_role", "")
        preferences.edit().putString(KEY_ROLE, role).putString(KEY_NAME, userName).putString(KEY_EMAIL, userEmail).putString(KEY_PHONE, userPhone).putString(KEY_STAFF_ROLE, staffRole).apply()
    }

    private fun showHome() { if (isStaff()) showDashboard() else showMenu() }
    private fun isStaff(): Boolean = role == "admin" || role == "staff"

    private fun showShell(titleText: String, destination: String, detail: Boolean = false, returnTo: String? = null) {
        pageGeneration++
        currentDestination = destination
        detailReturnDestination = if (detail) returnTo else null
        val root = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; setBackgroundColor(BACKGROUND) }
        root.addView(appBar(titleText, detail), LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, dp(64)))
        val scroll = ScrollView(this).apply { isFillViewport = true; overScrollMode = View.OVER_SCROLL_IF_CONTENT_SCROLLS }
        page = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; setPadding(dp(18), dp(20), dp(18), dp(28)) }
        scroll.addView(page, ViewGroup.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT))
        root.addView(scroll, LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, 0, 1f))
        if (!detail) root.addView(bottomNavigation(destination), LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, dp(74)))
        setContentView(root)
    }

    private fun appBar(titleText: String, detail: Boolean): View = LinearLayout(this).apply {
        orientation = LinearLayout.HORIZONTAL
        gravity = Gravity.CENTER_VERTICAL
        setPadding(dp(14), dp(6), dp(14), dp(6))
        background = rounded(SURFACE, 0f)
        elevation = dp(3).toFloat()
        if (detail) {
            addView(iconAction("‹", "Go back") { navigate(detailReturnDestination ?: if (isStaff()) "dashboard" else "menu") }, LinearLayout.LayoutParams(dp(46), dp(46)))
        } else {
            addView(TextView(this@MainActivity).apply {
                text = getString(R.string.logo_initials); textSize = 13f; typeface = Typeface.DEFAULT_BOLD; gravity = Gravity.CENTER; setTextColor(Color.WHITE)
                background = rounded(PRIMARY, dp(14).toFloat()); contentDescription = "Mi Cusina"
            }, LinearLayout.LayoutParams(dp(42), dp(42)))
        }
        addView(title(titleText, 21f).apply { setPadding(dp(12), 0, dp(8), 0) }, LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f))
        if (!detail) {
            val initial = userName.trim().firstOrNull()?.uppercase() ?: "U"
            addView(TextView(this@MainActivity).apply {
                text = initial; textSize = 15f; gravity = Gravity.CENTER; typeface = Typeface.DEFAULT_BOLD; setTextColor(PRIMARY_DARK)
                background = ripple(PRIMARY_SOFT, dp(21).toFloat()); contentDescription = "Open account"; setOnClickListener { navigate("more") }
            }, LinearLayout.LayoutParams(dp(42), dp(42)))
        }
    }

    private data class Destination(val id: String, val label: String, val icon: Int)

    private fun bottomNavigation(selected: String): View {
        val destinations = if (isStaff()) listOf(
            Destination("dashboard", "Home", R.drawable.ic_nav_home),
            Destination("staff_orders", "Orders", R.drawable.ic_nav_orders),
            Destination("inventory", "Inventory", R.drawable.ic_nav_inventory),
            Destination("more", "Account", R.drawable.ic_nav_account),
        ) else listOf(
            Destination("menu", "Menu", R.drawable.ic_nav_menu),
            Destination("cart", "Cart", R.drawable.ic_nav_cart),
            Destination("orders", "Orders", R.drawable.ic_nav_orders),
            Destination("reserve", "Reserve", R.drawable.ic_nav_reserve),
            Destination("more", "Account", R.drawable.ic_nav_account),
        )
        return LinearLayout(this).apply {
            orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER; setPadding(dp(6), dp(5), dp(6), dp(5)); setBackgroundColor(SURFACE); elevation = dp(8).toFloat()
            destinations.forEach { item ->
                val active = selected == item.id
                addView(LinearLayout(this@MainActivity).apply {
                    orientation = LinearLayout.VERTICAL; gravity = Gravity.CENTER; isClickable = true; isFocusable = true
                    background = ripple(if (active) PRIMARY_SOFT else Color.TRANSPARENT, dp(18).toFloat()); contentDescription = item.label
                    addView(ImageView(this@MainActivity).apply { setImageResource(item.icon); imageTintList = ColorStateList.valueOf(if (active) PRIMARY else TEXT_MUTED) }, LinearLayout.LayoutParams(dp(23), dp(23)))
                    addView(TextView(this@MainActivity).apply {
                        text = item.label; textSize = 11f; gravity = Gravity.CENTER; typeface = if (active) Typeface.DEFAULT_BOLD else Typeface.DEFAULT; setTextColor(if (active) PRIMARY_DARK else TEXT_MUTED)
                    }, topMargin(dp(2)))
                    setOnClickListener { if (!active) navigate(item.id) }
                }, LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.MATCH_PARENT, 1f).apply { setMargins(dp(2), 0, dp(2), 0) })
            }
        }
    }

    private fun navigate(destination: String) {
        if (blockNavigationDuringMutation()) return
        when (destination) {
            "menu" -> showMenu(); "cart" -> showCart(); "orders" -> showOrders(); "reserve" -> showReservations()
            "dashboard" -> showDashboard(); "staff_orders" -> showStaffOrders(); "inventory" -> showInventory()
            "gallery" -> showGallery(); "more" -> showMore(); else -> showHome()
        }
    }

    private fun blockNavigationDuringMutation(): Boolean {
        val message = when {
            checkoutMutationInFlight -> "Please wait while your order is being placed."
            reservationMutationInFlight -> "Please wait while your secure payment is being created."
            orderMutationInFlight -> "Please wait while the order status is being updated."
            else -> return false
        }
        toast(message)
        return true
    }

    private fun showMenu() {
        showShell("Menu", "menu")
        val generation = pageGeneration
        page.addView(title("What are you craving?", 27f))
        page.addView(caption("Explore today's freshly prepared Mi Cusina favorites."), topMargin(dp(4)))
        val search = field("Search the menu", InputType.TYPE_CLASS_TEXT).apply {
            setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_search, 0, 0, 0); compoundDrawablePadding = dp(10); imeOptions = EditorInfo.IME_ACTION_SEARCH
        }
        page.addView(search, topMargin(dp(18)))
        val list = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL }
        page.addView(list, topMargin(dp(16)))
        showLoading(list, "Loading today's menu…")
        request("GET", "/foods", onSuccess = { response ->
            if (generation != pageGeneration) return@request
            val foods = response.getJSONArray("foods").objects()
            fun render(query: String = "") {
                list.removeAllViews()
                val filtered = foods.filter {
                    val haystack = "${it.optString("title")} ${it.optString("detail")}".lowercase(Locale.getDefault())
                    haystack.contains(query.trim().lowercase(Locale.getDefault()))
                }
                if (filtered.isEmpty()) showEmpty(list, "No dishes found", "Try a different search.")
                else filtered.forEach { list.addView(foodCard(it), bottomMargin(dp(12))) }
            }
            render(); search.addTextChangedListener(SimpleTextWatcher { render(it) })
        }, onError = { message, _ -> if (generation == pageGeneration) showRetry(list, "Menu unavailable", message) { showMenu() } })
    }

    private fun foodCard(food: JSONObject): View {
        val stock = food.optInt("stock")
        val row = card().apply { orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER_VERTICAL }
        row.addView(foodImage(food.optString("image_url", food.optString("image")), dp(96)), LinearLayout.LayoutParams(dp(96), dp(96)))
        val details = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; setPadding(dp(14), 0, 0, 0) }
        details.addView(sectionTitle(food.optString("title", "Dish")))
        details.addView(bodyText(money(food.optDouble("price"))).apply { setTextColor(PRIMARY_DARK); typeface = Typeface.DEFAULT_BOLD }, topMargin(dp(3)))
        details.addView(caption(if (stock > 0) "$stock available" else "Currently unavailable").apply { setTextColor(if (stock > 0) SUCCESS else ERROR) }, topMargin(dp(3)))
        val add = compactButton(if (stock > 0) "Add to cart" else "Sold out", stock > 0) { }
        if (stock > 0) add.setOnClickListener {
            setButtonBusy(add, true, "Adding…")
            request("POST", "/cart/${food.getInt("id")}", JSONObject().put("quantity", 1), onSuccess = {
                setButtonBusy(add, false, "Add to cart"); toast("${food.optString("title")} added to cart")
            }, onError = { message, _ -> setButtonBusy(add, false, "Add to cart"); toast(message) })
        }
        details.addView(add, topMargin(dp(10)))
        row.addView(details, LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f))
        return row
    }

    private fun showCart() {
        cartMutationInFlight = false
        showShell("Your cart", "cart")
        val generation = pageGeneration
        val host = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL }
        page.addView(host); showLoading(host, "Loading your cart…")
        request("GET", "/cart", onSuccess = { response ->
            if (generation != pageGeneration) return@request
            host.removeAllViews()
            val items = response.getJSONArray("items").objects()
            if (items.isEmpty()) {
                showEmpty(host, "Your cart is empty", "Add a favorite from the menu to get started.")
                host.addView(primaryButton("Browse menu") { showMenu() }, topMargin(dp(20)))
                return@request
            }
            host.addView(caption("Review quantities before checkout."), bottomMargin(dp(14)))
            var total = 0.0
            items.forEach { item -> total += item.optDouble("price"); host.addView(cartItemCard(item), bottomMargin(dp(12))) }
            val summary = card().apply { orientation = LinearLayout.VERTICAL }
            summary.addView(sectionTitle("Order summary"))
            summary.addView(summaryRow("Items", items.sumOf { it.optInt("quantity") }.toString()), topMargin(dp(14)))
            summary.addView(summaryRow("Subtotal", money(total)), topMargin(dp(8)))
            summary.addView(caption("Delivery fee is confirmed during order processing."), topMargin(dp(10)))
            summary.addView(primaryButton("Continue to checkout") { showCheckout() }, topMargin(dp(18)))
            host.addView(summary, topMargin(dp(4)))
        }, onError = { message, _ -> if (generation == pageGeneration) showRetry(host, "Cart unavailable", message) { showCart() } })
    }

    private fun cartItemCard(item: JSONObject): View {
        val card = card().apply { orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER_VERTICAL }
        card.addView(foodImage(item.optString("image"), dp(72)), LinearLayout.LayoutParams(dp(72), dp(72)))
        val center = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; setPadding(dp(12), 0, dp(8), 0) }
        center.addView(sectionTitle(item.optString("title")))
        center.addView(bodyText(money(item.optDouble("price"))).apply { typeface = Typeface.DEFAULT_BOLD; setTextColor(PRIMARY_DARK) }, topMargin(dp(3)))
        val quantity = item.optInt("quantity", 1)
        val controls = LinearLayout(this).apply { orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER_VERTICAL }
        controls.addView(quantityButton("−", quantity > 1) { updateCart(item.getInt("id"), quantity - 1) })
        controls.addView(bodyText(quantity.toString()).apply { gravity = Gravity.CENTER; typeface = Typeface.DEFAULT_BOLD }, LinearLayout.LayoutParams(dp(42), dp(38)))
        controls.addView(quantityButton("+", true) { updateCart(item.getInt("id"), quantity + 1) })
        controls.addView(TextView(this).apply {
            text = getString(R.string.remove); textSize = 13f; gravity = Gravity.CENTER; setTextColor(ERROR); setPadding(dp(14), 0, dp(8), 0); background = ripple(Color.TRANSPARENT, dp(16).toFloat())
            setOnClickListener { confirm("Remove item?", "Remove ${item.optString("title")} from your cart?") { removeCart(item.getInt("id")) } }
        }, LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, dp(38)))
        center.addView(controls, topMargin(dp(10)))
        card.addView(center, LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f))
        return card
    }

    private fun updateCart(cartId: Int, quantity: Int) {
        if (cartMutationInFlight) return
        cartMutationInFlight = true
        request("PATCH", "/cart/$cartId", JSONObject().put("quantity", quantity), onSuccess = {
            cartMutationInFlight = false
            if (currentDestination == "cart") showCart() else toast("Cart updated.")
        }, onError = { message, _ ->
            cartMutationInFlight = false
            toast(message)
        }, pageScoped = false)
    }

    private fun removeCart(cartId: Int) {
        if (cartMutationInFlight) return
        cartMutationInFlight = true
        request("DELETE", "/cart/$cartId", onSuccess = {
            cartMutationInFlight = false
            if (currentDestination == "cart") showCart() else toast("Item removed from cart.")
        }, onError = { message, _ ->
            cartMutationInFlight = false
            toast(message)
        }, pageScoped = false)
    }

    private fun showCheckout() {
        showShell("Checkout", "checkout", detail = true, returnTo = "cart")
        page.addView(title("Delivery details", 26f)); page.addView(caption("We only use these details to fulfill your order."), topMargin(dp(4)))
        val name = labeledField("Full name", userName, InputType.TYPE_CLASS_TEXT)
        val phone = labeledField("Mobile number", userPhone, InputType.TYPE_CLASS_PHONE)
        val municipalities = arrayOf("Bantayan", "Madridejos", "Santa Fe")
        val municipality = labeledSpinner("Municipality", municipalities)
        val barangayLabel = label("Barangay")
        val barangay = Spinner(this); styleSpinner(barangay)
        val purok = labeledField("Purok", "", InputType.TYPE_CLASS_TEXT)
        val details = labeledField("Landmark or delivery notes (optional)", "", InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_FLAG_CAP_SENTENCES)
        val payment = labeledSpinner("Payment method", arrayOf("Cash on Delivery", "GCash", "Bank Transfer"))
        val referenceLabel = label("Payment reference")
        val reference = field("Reference number", InputType.TYPE_CLASS_TEXT)
        referenceLabel.visibility = View.GONE; reference.visibility = View.GONE
        page.addView(name.first, topMargin(dp(18))); page.addView(phone.first, topMargin(dp(12))); page.addView(municipality.first, topMargin(dp(12)))
        page.addView(barangayLabel, topMargin(dp(12))); page.addView(barangay); page.addView(purok.first, topMargin(dp(12))); page.addView(details.first, topMargin(dp(12)))
        page.addView(payment.first, topMargin(dp(12))); page.addView(referenceLabel, topMargin(dp(12))); page.addView(reference)

        fun refreshBarangays(position: Int) { barangay.adapter = ArrayAdapter(this, android.R.layout.simple_spinner_dropdown_item, BARANGAYS[municipalities[position]].orEmpty()) }
        municipality.second.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) = refreshBarangays(position)
            override fun onNothingSelected(parent: AdapterView<*>?) = Unit
        }
        refreshBarangays(0)
        payment.second.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                val visible = position != 0
                referenceLabel.visibility = if (visible) View.VISIBLE else View.GONE
                reference.visibility = if (visible) View.VISIBLE else View.GONE
                if (!visible) reference.text.clear()
            }
            override fun onNothingSelected(parent: AdapterView<*>?) = Unit
        }
        val placeOrder = primaryButton("Place order") { }; page.addView(placeOrder, topMargin(dp(24)))
        placeOrder.setOnClickListener {
            if (checkoutMutationInFlight) return@setOnClickListener
            val phoneValue = phone.second.text.toString().trim()
            if (name.second.text.isBlank() || !PHONE_REGEX.matches(phoneValue) || purok.second.text.isBlank()) { toast("Enter your name, a valid Philippine mobile number, and purok."); return@setOnClickListener }
            if (payment.second.selectedItemPosition != 0 && reference.text.isBlank()) { toast("Enter the payment reference number."); return@setOnClickListener }
            val body = JSONObject().put("name", name.second.text.toString().trim()).put("phone", phoneValue)
                .put("municipality", municipality.second.selectedItem.toString()).put("barangay", barangay.selectedItem.toString())
                .put("purok", purok.second.text.toString().trim()).put("address_details", details.second.text.toString().trim())
                .put("payment_method", payment.second.selectedItem.toString())
            if (payment.second.selectedItemPosition != 0) body.put("payment_reference", reference.text.toString().trim())
            val requestPage = pageGeneration
            checkoutMutationInFlight = true
            setButtonBusy(placeOrder, true, "Placing order…")
            request("POST", "/checkout", body, onSuccess = {
                checkoutMutationInFlight = false
                if (requestPage == pageGeneration && currentDestination == "checkout") showOrderSuccess()
                else toast("Order confirmed. You can track it from My orders.")
            }, onError = { message, _ ->
                checkoutMutationInFlight = false
                if (requestPage == pageGeneration && currentDestination == "checkout") setButtonBusy(placeOrder, false, "Place order")
                toast(message)
            }, pageScoped = false)
        }
    }

    private fun showOrderSuccess() {
        showShell("Order confirmed", "success", detail = true, returnTo = "orders")
        page.gravity = Gravity.CENTER_HORIZONTAL
        page.addView(TextView(this).apply { text = "✓"; textSize = 50f; gravity = Gravity.CENTER; setTextColor(SUCCESS); background = rounded(SUCCESS_SOFT, dp(42).toFloat()) }, LinearLayout.LayoutParams(dp(84), dp(84)))
        page.addView(title("Thank you!", 28f).apply { gravity = Gravity.CENTER }, topMargin(dp(22)))
        page.addView(caption("Your order has been received. Track its progress from My orders.").apply { gravity = Gravity.CENTER; textAlignment = View.TEXT_ALIGNMENT_CENTER }, topMargin(dp(8)))
        page.addView(primaryButton("Track my order") { showOrders() }, topMargin(dp(24))); page.addView(secondaryButton("Back to menu") { showMenu() }, topMargin(dp(10)))
    }

    private fun showOrders() = showOrderList("My orders", false)
    private fun showStaffOrders() = showOrderList("Manage orders", true)

    private fun showOrderList(titleText: String, staff: Boolean) {
        showShell(titleText, if (staff) "staff_orders" else "orders")
        val generation = pageGeneration
        val header = LinearLayout(this).apply { orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER_VERTICAL }
        header.addView(caption(if (staff) "Review and update fulfillment status." else "Follow the latest status of your orders."), LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f))
        header.addView(textButton("Refresh") { navigate(if (staff) "staff_orders" else "orders") }); page.addView(header, bottomMargin(dp(16)))
        val host = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL }; page.addView(host); showLoading(host, "Loading orders…")
        request("GET", if (staff) "/staff/orders" else "/orders", onSuccess = { response ->
            if (generation != pageGeneration) return@request
            host.removeAllViews(); val orders = response.getJSONArray("orders").objects()
            if (orders.isEmpty()) showEmpty(host, "No orders yet", if (staff) "New orders will appear here." else "Your completed checkouts will appear here.")
            orders.forEach { order -> host.addView(orderCard(order, staff), bottomMargin(dp(12))) }
        }, onError = { message, _ -> if (generation == pageGeneration) showRetry(host, "Orders unavailable", message) { if (staff) showStaffOrders() else showOrders() } })
    }

    private fun orderCard(order: JSONObject, staff: Boolean): View {
        val card = card().apply { orientation = LinearLayout.VERTICAL }
        val top = LinearLayout(this).apply { orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER_VERTICAL }
        top.addView(sectionTitle("#${order.optInt("id")} · ${order.optString("title")}"), LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f)); top.addView(statusChip(order.optString("delivery_status")))
        card.addView(top); card.addView(bodyText("${order.optInt("quantity")} item(s)  •  ${money(order.optDouble("price"))}"), topMargin(dp(10)))
        card.addView(caption("Payment: ${order.optString("payment_status", "Pending")}"), topMargin(dp(4)))
        if (staff) {
            val status = order.optString("delivery_status"); val actions = LinearLayout(this).apply { orientation = LinearLayout.HORIZONTAL; gravity = Gravity.END }
            val manager = role == "admin" || staffRole == "cashier"
            if (status == "On The Way") actions.addView(smallOutlineButton("Delivered") { updateOrder(order.getInt("id"), "Delivered") })
            if (manager && status == "In Progress") actions.addView(smallDangerButton("Cancel") { confirm("Cancel order?", "This will cancel every item in this order.") { updateOrder(order.getInt("id"), "Canceled") } }, leftMargin(dp(8)))
            if (actions.childCount > 0) card.addView(actions, topMargin(dp(14)))
        }
        return card
    }

    private fun updateOrder(orderId: Int, status: String) {
        if (orderMutationInFlight) return
        orderMutationInFlight = true
        request("PATCH", "/staff/orders/$orderId", JSONObject().put("delivery_status", status), onSuccess = {
            orderMutationInFlight = false
            if (currentDestination == "staff_orders") showStaffOrders() else toast("Order status updated.")
        }, onError = { message, _ ->
            orderMutationInFlight = false
            toast(message)
        }, pageScoped = false)
    }

    private fun showReservations() {
        showShell("Reservations", "reserve")
        page.addView(title("Dine with us", 27f)); page.addView(caption("Reserve a table with a secure 50% deposit."), topMargin(dp(4)))
        val savedPaymentUrl = preferences.getString(KEY_PENDING_PAYMENT_URL, null)?.takeIf { it.isNotBlank() }
        val savedBookingId = preferences.getLong(KEY_PENDING_PAYMENT_BOOKING_ID, -1L)
        val resumeHost = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL }
        savedPaymentUrl?.let { pendingUrl -> resumeHost.addView(paymentResumeCard(pendingUrl), topMargin(dp(16))) }
        page.addView(resumeHost)
        page.addView(primaryButton("Book a table") { showReservationForm() }, topMargin(dp(18))); page.addView(sectionTitle("Your reservations"), topMargin(dp(26)))
        val host = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL }; page.addView(host, topMargin(dp(12)))
        val generation = pageGeneration; showLoading(host, "Loading reservations…")
        request("GET", "/reservations", onSuccess = { response ->
            if (generation != pageGeneration) return@request
            host.removeAllViews(); val bookings = response.getJSONArray("reservations").objects()
            val pendingBookings = bookings.filter(::isPendingReservation)
            resumeHost.removeAllViews()
            var firstServerResume: Pair<Long, String>? = null
            if (bookings.isEmpty()) showEmpty(host, "No reservations", "Your upcoming table bookings will appear here.")
            bookings.forEach { booking ->
                val item = card().apply { orientation = LinearLayout.VERTICAL }
                item.addView(sectionTitle("${booking.optString("date")} · ${booking.optString("time")}")); item.addView(bodyText("${booking.optInt("guest")} guest(s)"), topMargin(dp(7)))
                item.addView(caption("${booking.optString("status")}  •  ${booking.optString("payment_status")}"), topMargin(dp(4)))
                if (isPendingReservation(booking)) {
                    val bookingId = booking.optLong("id", -1L)
                    val serverUrl = reservationPaymentUrl(booking)
                    if (serverUrl != null && firstServerResume == null) firstServerResume = bookingId to serverUrl
                    val fallbackUrl = savedPaymentUrl?.takeIf {
                        savedBookingId == bookingId || (savedBookingId < 0 && pendingBookings.size == 1)
                    }
                    (serverUrl ?: fallbackUrl)?.let { checkoutUrl ->
                        item.addView(smallOutlineButton("Resume secure payment") {
                            paymentBrowserOpen = openPayment(checkoutUrl)
                        }, topMargin(dp(12)))
                    }
                }
                host.addView(item, bottomMargin(dp(12)))
            }
            when {
                firstServerResume != null -> preferences.edit()
                    .putLong(KEY_PENDING_PAYMENT_BOOKING_ID, firstServerResume!!.first)
                    .putString(KEY_PENDING_PAYMENT_URL, firstServerResume!!.second)
                    .apply()
                pendingBookings.isEmpty() -> preferences.edit()
                    .remove(KEY_PENDING_PAYMENT_URL)
                    .remove(KEY_PENDING_PAYMENT_BOOKING_ID)
                    .apply()
                savedPaymentUrl != null && savedBookingId < 0 && pendingBookings.size > 1 -> resumeHost.addView(paymentResumeCard(savedPaymentUrl), topMargin(dp(16)))
            }
        }, onError = { message, _ -> if (generation == pageGeneration) showRetry(host, "Reservations unavailable", message) { showReservations() } })
    }

    private fun isPendingReservation(booking: JSONObject): Boolean {
        val paymentStatus = booking.optString("payment_status")
        if (paymentStatus.equals("Paid", ignoreCase = true)) return false
        return paymentStatus.equals("Pending", ignoreCase = true) || booking.optString("status").equals("Awaiting Payment", ignoreCase = true)
    }

    private fun reservationPaymentUrl(booking: JSONObject): String? = sequenceOf("checkout_url", "payment_url")
        .map { booking.optString(it) }
        .firstOrNull { it.startsWith("https://", ignoreCase = true) }

    private fun paymentResumeCard(checkoutUrl: String): View = card().apply {
        orientation = LinearLayout.VERTICAL
        addView(sectionTitle("Payment still pending"))
        addView(caption("Return to the secure payment page or refresh your reservations after paying."), topMargin(dp(5)))
        addView(smallOutlineButton("Resume payment") { paymentBrowserOpen = openPayment(checkoutUrl) }, topMargin(dp(12)))
    }

    private fun showReservationForm() {
        showShell("Book a table", "booking", detail = true, returnTo = "reserve")
        page.addView(title("Reservation details", 26f)); page.addView(caption("A ₱125 deposit confirms your ₱250 reservation."), topMargin(dp(4)))
        val first = labeledField("First name", userName.substringBefore(" "), InputType.TYPE_CLASS_TEXT)
        val last = labeledField("Last name", userName.substringAfter(" ", ""), InputType.TYPE_CLASS_TEXT)
        val phone = labeledField("Mobile number", userPhone, InputType.TYPE_CLASS_PHONE); val guests = labeledField("Number of guests", "2", InputType.TYPE_CLASS_NUMBER)
        val date = labeledField("Reservation date", "", InputType.TYPE_CLASS_DATETIME).also { it.second.isFocusable = false; it.second.isClickable = true }
        val time = labeledField("Reservation time", "", InputType.TYPE_CLASS_DATETIME).also { it.second.isFocusable = false; it.second.isClickable = true }
        val payment = labeledSpinner("Payment method", arrayOf("GCash", "Bank Transfer"))
        listOf(first.first, last.first, phone.first, guests.first, date.first, time.first, payment.first).forEachIndexed { index, view -> page.addView(view, topMargin(if (index == 0) dp(18) else dp(12))) }
        date.second.setOnClickListener {
            val now = Calendar.getInstance(); DatePickerDialog(this, { _, year, month, day -> date.second.setText(String.format(Locale.US, "%04d-%02d-%02d", year, month + 1, day)) }, now.get(Calendar.YEAR), now.get(Calendar.MONTH), now.get(Calendar.DAY_OF_MONTH)).apply { datePicker.minDate = System.currentTimeMillis() - 1000 }.show()
        }
        time.second.setOnClickListener {
            val now = Calendar.getInstance(); TimePickerDialog(this, { _, hour, minute ->
                val selected = Calendar.getInstance().apply { set(Calendar.HOUR_OF_DAY, hour); set(Calendar.MINUTE, minute) }
                time.second.setText(SimpleDateFormat("h:mm a", Locale.US).format(selected.time))
            }, now.get(Calendar.HOUR_OF_DAY), now.get(Calendar.MINUTE), false).show()
        }
        val submit = primaryButton("Continue to secure payment") { }; page.addView(submit, topMargin(dp(24)))
        submit.setOnClickListener {
            if (reservationMutationInFlight) return@setOnClickListener
            val guestCount = guests.second.text.toString().toIntOrNull()
            val phoneValue = phone.second.text.toString().trim()
            if (first.second.text.isBlank() || last.second.text.isBlank() || !PHONE_REGEX.matches(phoneValue) || guestCount == null || guestCount !in 1..20 || date.second.text.isBlank() || time.second.text.isBlank()) { toast("Complete all details. Reservations support 1–20 guests."); return@setOnClickListener }
            if (!isFutureReservation(date.second.text.toString(), time.second.text.toString())) { toast("Choose a future reservation time."); return@setOnClickListener }
            val body = JSONObject().put("first_name", first.second.text.toString().trim()).put("last_name", last.second.text.toString().trim())
                .put("phone", phoneValue).put("guest", guestCount).put("date", date.second.text.toString()).put("time", time.second.text.toString()).put("payment_method", payment.second.selectedItem.toString())
            val requestPage = pageGeneration
            reservationMutationInFlight = true
            setButtonBusy(submit, true, "Creating payment…")
            request("POST", "/reservations", body, onSuccess = { response ->
                reservationMutationInFlight = false
                val reservation = response.optJSONObject("reservation")
                val checkoutUrl = response.optString("checkout_url").takeUnless { it.isBlank() || it == "null" }
                    ?: reservation?.optString("checkout_url")?.takeUnless { it.isBlank() || it == "null" }
                if (checkoutUrl.isNullOrBlank()) {
                    if (requestPage == pageGeneration && currentDestination == "booking") showReservations()
                    toast("Reservation created, but its payment link is not available yet. Refresh Reservations to retry.")
                } else {
                    val editor = preferences.edit().putString(KEY_PENDING_PAYMENT_URL, checkoutUrl)
                    reservation?.optLong("id")?.takeIf { it > 0 }?.let { editor.putLong(KEY_PENDING_PAYMENT_BOOKING_ID, it) }
                    editor.apply()
                    if (requestPage == pageGeneration && currentDestination == "booking") {
                        paymentBrowserOpen = openPayment(checkoutUrl)
                        if (!paymentBrowserOpen) setButtonBusy(submit, false, "Continue to secure payment")
                    } else {
                        toast("Reservation created. Resume its payment from Reservations.")
                    }
                }
            }, onError = { message, _ ->
                reservationMutationInFlight = false
                if (requestPage == pageGeneration && currentDestination == "booking") setButtonBusy(submit, false, "Continue to secure payment")
                toast(message)
            }, pageScoped = false)
        }
    }

    private fun showDashboard() {
        showShell("Operations", "dashboard")
        val generation = pageGeneration
        page.addView(title("Welcome back, ${userName.substringBefore(" ")}", 27f)); page.addView(caption(if (role == "admin") "Administrator overview" else "Staff fulfillment overview"), topMargin(dp(4)))
        val host = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL }; page.addView(host, topMargin(dp(18))); showLoading(host, "Loading dashboard…")
        request("GET", "/staff/dashboard", onSuccess = { response ->
            if (generation != pageGeneration) return@request
            host.removeAllViews()
            val rows = listOf("Pending orders" to response.optInt("pending_orders"), "On the way" to response.optInt("on_the_way_orders"), "Delivered" to response.optInt("delivered_orders"), "Low stock items" to response.optInt("low_stock"))
            rows.chunked(2).forEach { chunk ->
                val row = LinearLayout(this).apply { orientation = LinearLayout.HORIZONTAL }
                chunk.forEach { (label, value) -> row.addView(metricCard(label, value), LinearLayout.LayoutParams(0, dp(126), 1f).apply { setMargins(dp(5), dp(5), dp(5), dp(5)) }) }; host.addView(row)
            }
            host.addView(primaryButton("Manage orders") { showStaffOrders() }, topMargin(dp(18)))
        }, onError = { message, _ -> if (generation == pageGeneration) showRetry(host, "Dashboard unavailable", message) { showDashboard() } })
    }

    private fun metricCard(labelText: String, value: Int): View = card().apply {
        orientation = LinearLayout.VERTICAL; gravity = Gravity.CENTER; addView(title(value.toString(), 30f).apply { gravity = Gravity.CENTER; setTextColor(PRIMARY_DARK) }); addView(caption(labelText).apply { gravity = Gravity.CENTER; textAlignment = View.TEXT_ALIGNMENT_CENTER }, topMargin(dp(5)))
    }

    private fun showInventory() {
        showShell("Inventory", "inventory")
        val generation = pageGeneration
        page.addView(caption(if (role == "admin") "Review stock and tap an item to update it." else "Current stock levels. Only administrators can make changes."))
        val host = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL }; page.addView(host, topMargin(dp(16))); showLoading(host, "Loading inventory…")
        request("GET", "/staff/inventory", onSuccess = { response ->
            if (generation != pageGeneration) return@request
            host.removeAllViews(); response.getJSONArray("foods").objects().forEach { food ->
                val item = card().apply {
                    orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER_VERTICAL
                    addView(foodImage(food.optString("image"), dp(58)), LinearLayout.LayoutParams(dp(58), dp(58)))
                    addView(LinearLayout(this@MainActivity).apply {
                        orientation = LinearLayout.VERTICAL; setPadding(dp(12), 0, dp(8), 0); addView(sectionTitle(food.optString("title")))
                        addView(caption("${food.optInt("stock")} units available").apply { setTextColor(if (food.optInt("stock") <= 5) ERROR else SUCCESS) }, topMargin(dp(4)))
                    }, LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f))
                    if (role == "admin") addView(textButton("Update") { showStockDialog(food) })
                }; host.addView(item, bottomMargin(dp(10)))
            }
        }, onError = { message, _ -> if (generation == pageGeneration) showRetry(host, "Inventory unavailable", message) { showInventory() } })
    }

    private fun showStockDialog(food: JSONObject) {
        val stock = field("Stock quantity", InputType.TYPE_CLASS_NUMBER).apply { setText(String.format(Locale.getDefault(), "%d", food.optInt("stock"))); selectAll() }
        val wrapper = LinearLayout(this).apply { setPadding(dp(22), dp(8), dp(22), 0); addView(stock) }
        val dialog = AlertDialog.Builder(this)
            .setTitle("Update ${food.optString("title")}")
            .setView(wrapper)
            .setNegativeButton("Cancel", null)
            .setPositiveButton("Save", null)
            .create()
        dialog.setOnShowListener {
            val save = dialog.getButton(AlertDialog.BUTTON_POSITIVE)
            save.setOnClickListener {
                val value = stock.text.toString().toIntOrNull()
                if (value == null || value < 0) {
                    toast("Enter a valid stock quantity.")
                    return@setOnClickListener
                }
                save.isEnabled = false
                request("PATCH", "/staff/inventory/${food.getInt("id")}", JSONObject().put("stock", value), onSuccess = {
                    dialog.dismiss()
                    showInventory()
                }, onError = { message, _ ->
                    save.isEnabled = true
                    toast(message)
                })
            }
        }
        dialog.show()
    }

    private fun showMore() {
        showShell("Account", "more")
        val profile = card().apply { orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER_VERTICAL }
        profile.addView(TextView(this).apply {
            text = userName.firstOrNull()?.uppercase() ?: "U"; textSize = 22f; typeface = Typeface.DEFAULT_BOLD; gravity = Gravity.CENTER; setTextColor(PRIMARY_DARK); background = rounded(PRIMARY_SOFT, dp(32).toFloat())
        }, LinearLayout.LayoutParams(dp(64), dp(64)))
        profile.addView(LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL; setPadding(dp(14), 0, 0, 0); addView(sectionTitle(userName)); addView(caption(userEmail), topMargin(dp(3))); addView(caption(if (isStaff()) role.replaceFirstChar { it.uppercase() } else "Customer"), topMargin(dp(3)))
        }, LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f)); page.addView(profile)
        page.addView(sectionTitle("Explore"), topMargin(dp(24)))
        page.addView(settingsRow("Food gallery", "Bundled Mi Cusina favorites", android.R.drawable.ic_menu_gallery) { showGallery() }, topMargin(dp(10)))
        page.addView(settingsRow("Visit Mi Cusina online", "Open our secure website", android.R.drawable.ic_menu_compass) { openWebsite("/") }, topMargin(dp(8)))
        page.addView(settingsRow("Help & contact", "Get support from the restaurant", android.R.drawable.ic_menu_help) { openWebsite("/?section=contact") }, topMargin(dp(8)))
        page.addView(secondaryButton("Sign out") { confirm("Sign out?", "You'll need your password to sign in again.") { logout() } }, topMargin(dp(24)))
        page.addView(caption("Mi Cusina ${BuildConfig.VERSION_NAME} · Secure native app").apply { gravity = Gravity.CENTER }, topMargin(dp(20)))
    }

    private fun showGallery() {
        showShell("Food gallery", "gallery", detail = true, returnTo = "more")
        page.addView(caption("A taste of Mi Cusina, available even while you're offline."), bottomMargin(dp(16)))
        listOf(
            "Chicken Burger" to R.drawable.hero_chicken_burger_transparent, "Burger Spaghetti" to R.drawable.hero_burger_spaghetti_transparent,
            "Hotdog Sandwich" to R.drawable.hero_hotdog_sandwich_transparent, "Chicken Teriyaki" to R.drawable.hero_chicken_teriyaki_transparent,
            "Adobo Bunwich" to R.drawable.hero_adobo_bunwich_transparent,
            "Mi Cusina Kitchen" to R.drawable.mi_cusina_hero_food_edge,
        ).forEach { (name, imageResource) ->
            val item = card().apply { orientation = LinearLayout.VERTICAL }
            item.addView(ImageView(this).apply { setImageResource(imageResource); adjustViewBounds = true; scaleType = ImageView.ScaleType.CENTER_INSIDE; contentDescription = name }, LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, dp(220)))
            item.addView(sectionTitle(name), topMargin(dp(10))); page.addView(item, bottomMargin(dp(12)))
        }
    }

    private fun logout() = request("POST", "/logout", onSuccess = { clearSession(); showLogin() }, onError = { _, _ -> clearSession(); showLogin() }, pageScoped = false)
    private fun expireSession() { clearSession(); showLogin(); toast("Your session expired. Please sign in again.") }
    private fun clearSession() {
        sessionGeneration++
        loginInFlight = false
        cartMutationInFlight = false
        orderMutationInFlight = false
        checkoutMutationInFlight = false
        reservationMutationInFlight = false
        paymentBrowserOpen = false
        tokenStore.clear()
        preferences.edit().clear().apply()
        token = ""
        role = "user"
        userName = "Guest"
        userEmail = ""
        userPhone = ""
        staffRole = ""
    }

    @Deprecated("Handled by OnBackInvokedDispatcher on Android 13 and newer")
    override fun onBackPressed() = handleBack()

    private fun handleBack() {
        if (currentDestination == "launch") return
        if (blockNavigationDuringMutation()) return
        detailReturnDestination?.let { navigate(it); return }
        val home = if (isStaff()) "dashboard" else "menu"
        if (currentDestination != home && currentDestination != "login") navigate(home) else finishAfterTransition()
    }

    private fun request(
        method: String,
        path: String,
        body: JSONObject? = null,
        onSuccess: (JSONObject) -> Unit,
        onError: (String, Int?) -> Unit = { message, _ -> toast(message) },
        handleUnauthorized: Boolean = true,
        pageScoped: Boolean = true,
        acceptedErrorCodes: Set<Int> = emptySet(),
    ) {
        val requestPage = pageGeneration
        val requestSession = sessionGeneration
        requestExecutor.execute {
            var connection: HttpURLConnection? = null
            try {
                connection = (URL(BuildConfig.API_BASE_URL.trimEnd('/') + path).openConnection() as HttpURLConnection).apply {
                    requestMethod = method; connectTimeout = 15_000; readTimeout = 20_000; useCaches = false
                    setRequestProperty("Accept", "application/json"); setRequestProperty("Content-Type", "application/json; charset=utf-8"); setRequestProperty("X-Requested-With", "MiCusinaAndroid")
                    if (token.isNotBlank()) setRequestProperty("Authorization", "Bearer $token")
                    if (body != null) { doOutput = true; outputStream.use { it.write(body.toString().toByteArray(Charsets.UTF_8)) } }
                }
                val code = connection.responseCode
                val stream = if (code < 400) connection.inputStream else connection.errorStream
                val raw = stream?.bufferedReader(Charsets.UTF_8)?.use { it.readText() }.orEmpty()
                val response = if (raw.isBlank()) JSONObject() else JSONObject(raw)
                runOnUiThread {
                    if (isFinishing || isDestroyed || requestSession != sessionGeneration || (pageScoped && requestPage != pageGeneration)) return@runOnUiThread
                    try {
                        when {
                            code == 401 && handleUnauthorized -> expireSession()
                            code >= 400 && code !in acceptedErrorCodes -> onError(apiMessage(response, code), code)
                            else -> onSuccess(response)
                        }
                    } catch (_: Exception) {
                        onError("Mi Cusina returned an unexpected response. Please try again.", code)
                    }
                }
            } catch (error: Exception) {
                runOnUiThread {
                    if (!isFinishing && !isDestroyed && requestSession == sessionGeneration && (!pageScoped || requestPage == pageGeneration)) onError(friendlyNetworkMessage(error.message), null)
                }
            } finally { connection?.disconnect() }
        }
    }

    private fun apiMessage(response: JSONObject, code: Int): String {
        val direct = response.optString("message"); if (direct.isNotBlank()) return direct
        val errors = response.optJSONObject("errors")
        errors?.keys()?.asSequence()?.firstOrNull()?.let { key -> val messages = errors.optJSONArray(key); if (messages != null && messages.length() > 0) return messages.optString(0) }
        return "Request failed ($code). Please try again."
    }

    private fun friendlyNetworkMessage(message: String?): String {
        val value = message.orEmpty().lowercase(Locale.getDefault())
        return when { "timed out" in value || "timeout" in value -> "The connection timed out. Check your signal and try again."; "unable to resolve" in value || "unknownhost" in value -> "You're offline. Connect to the internet and try again."; else -> "We couldn't reach Mi Cusina. Check your connection and try again." }
    }

    private fun foodImage(filename: String, size: Int): View {
        val frame = FrameLayout(this).apply { background = rounded(PRIMARY_SOFT, dp(14).toFloat()) }
        frame.addView(TextView(this).apply { text = getString(R.string.logo_initials); textSize = 16f; typeface = Typeface.DEFAULT_BOLD; gravity = Gravity.CENTER; setTextColor(PRIMARY_DARK) }, FrameLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.MATCH_PARENT))
        val image = ImageView(this).apply { scaleType = ImageView.ScaleType.CENTER_CROP; contentDescription = "Food image"; visibility = View.INVISIBLE }
        frame.addView(image, FrameLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.MATCH_PARENT))
        if (filename.isNotBlank() && filename != "null") {
            val imageUrl = if (filename.startsWith("https://") || filename.startsWith("http://")) filename
                else BuildConfig.WEB_BASE_URL.trimEnd('/') + "/food_img/" + Uri.encode(filename)
            loadImage(image, imageUrl, size)
        }
        return frame
    }

    private fun loadImage(view: ImageView, url: String, targetSize: Int) {
        view.tag = url
        imageCache.get(url)?.let { view.setImageBitmap(it); view.visibility = View.VISIBLE; return }
        synchronized(pendingImages) {
            pendingImages[url]?.let { it.add(WeakReference(view)); return }
            pendingImages[url] = mutableListOf(WeakReference(view))
        }
        imageExecutor.execute imageTask@{
            var connection: HttpURLConnection? = null
            try {
                connection = (URL(url).openConnection() as HttpURLConnection).apply { connectTimeout = 10_000; readTimeout = 12_000; useCaches = true }
                val bytes = connection.inputStream.use { input ->
                    val buffer = ByteArray(8 * 1024); val output = java.io.ByteArrayOutputStream(); var total = 0
                    while (true) { val count = input.read(buffer); if (count < 0) break; total += count; if (total > 8 * 1024 * 1024) throw IllegalStateException("Image too large"); output.write(buffer, 0, count) }; output.toByteArray()
                }
                val bounds = BitmapFactory.Options().apply { inJustDecodeBounds = true }; BitmapFactory.decodeByteArray(bytes, 0, bytes.size, bounds)
                var sample = 1; while (bounds.outWidth / sample > targetSize * 2 || bounds.outHeight / sample > targetSize * 2) sample *= 2
                val bitmap = BitmapFactory.decodeByteArray(bytes, 0, bytes.size, BitmapFactory.Options().apply { inSampleSize = sample }) ?: return@imageTask
                imageCache.put(url, bitmap)
                val waiters = synchronized(pendingImages) { pendingImages.remove(url).orEmpty() }
                runOnUiThread {
                    if (!isFinishing && !isDestroyed) waiters.forEach { reference -> reference.get()?.takeIf { it.tag == url }?.let { target -> target.setImageBitmap(bitmap); target.visibility = View.VISIBLE } }
                }
            } catch (_: Exception) {
                // Initials remain as an offline-safe fallback.
            } finally {
                connection?.disconnect()
                synchronized(pendingImages) { pendingImages.remove(url) }
            }
        }
    }

    override fun onDestroy() {
        requestExecutor.shutdownNow()
        imageExecutor.shutdownNow()
        super.onDestroy()
    }

    private fun showLoading(host: LinearLayout, message: String) {
        host.removeAllViews(); host.gravity = Gravity.CENTER_HORIZONTAL
        host.addView(ProgressBar(this).apply { indeterminateTintList = ColorStateList.valueOf(PRIMARY) }, LinearLayout.LayoutParams(dp(38), dp(38))); host.addView(caption(message).apply { gravity = Gravity.CENTER }, topMargin(dp(10)))
    }

    private fun showEmpty(host: LinearLayout, heading: String, message: String) {
        host.removeAllViews(); host.gravity = Gravity.CENTER_HORIZONTAL
        host.addView(TextView(this).apply { text = "○"; textSize = 42f; gravity = Gravity.CENTER; setTextColor(PRIMARY) }); host.addView(sectionTitle(heading).apply { gravity = Gravity.CENTER }, topMargin(dp(8)))
        host.addView(caption(message).apply { gravity = Gravity.CENTER; textAlignment = View.TEXT_ALIGNMENT_CENTER }, topMargin(dp(5)))
    }

    private fun showRetry(host: LinearLayout, heading: String, message: String, retry: () -> Unit) { showEmpty(host, heading, message); host.addView(primaryButton("Try again", retry), topMargin(dp(18))) }
    private fun title(value: String, size: Float): TextView = TextView(this).apply { text = value; textSize = size; typeface = Typeface.create("sans-serif", Typeface.BOLD); setTextColor(TEXT); includeFontPadding = false }
    private fun sectionTitle(value: String): TextView = title(value, 17f)
    private fun bodyText(value: String): TextView = TextView(this).apply { text = value; textSize = 15f; setTextColor(TEXT); includeFontPadding = false }
    private fun caption(value: String): TextView = TextView(this).apply { text = value; textSize = 13f; setTextColor(TEXT_MUTED); setLineSpacing(dp(2).toFloat(), 1f); includeFontPadding = false }
    private fun label(value: String): TextView = caption(value).apply { typeface = Typeface.DEFAULT_BOLD; setTextColor(TEXT) }

    private fun field(hintText: String, type: Int): EditText = EditText(this).apply {
        hint = hintText; inputType = type; textSize = 15f; setTextColor(TEXT); setHintTextColor(TEXT_LIGHT); setPadding(dp(14), dp(12), dp(14), dp(12)); background = rounded(SURFACE, dp(12).toFloat(), BORDER, dp(1)); minHeight = dp(50)
    }

    private fun labeledField(labelText: String, value: String, type: Int): Pair<LinearLayout, EditText> {
        val input = field(labelText, type).apply { setText(value) }
        return LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; addView(label(labelText)); addView(input, topMargin(dp(6))) } to input
    }

    private fun labeledSpinner(labelText: String, values: Array<String>): Pair<LinearLayout, Spinner> {
        val spinner = Spinner(this); spinner.adapter = ArrayAdapter(this, android.R.layout.simple_spinner_dropdown_item, values); styleSpinner(spinner)
        return LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; addView(label(labelText)); addView(spinner, topMargin(dp(6))) } to spinner
    }

    private fun styleSpinner(spinner: Spinner) { spinner.background = rounded(SURFACE, dp(12).toFloat(), BORDER, dp(1)); spinner.setPadding(dp(10), 0, dp(10), 0); spinner.minimumHeight = dp(50) }
    private fun card(): LinearLayout = LinearLayout(this).apply { setPadding(dp(16), dp(16), dp(16), dp(16)); background = rounded(SURFACE, dp(16).toFloat(), BORDER, dp(1)); elevation = dp(2).toFloat() }

    private fun primaryButton(value: String, action: () -> Unit): TextView = buttonView(value, Color.WHITE, PRIMARY, action)
    private fun secondaryButton(value: String, action: () -> Unit): TextView = buttonView(value, PRIMARY_DARK, SURFACE, action, BORDER)
    private fun compactButton(value: String, enabled: Boolean, action: () -> Unit): TextView = buttonView(value, Color.WHITE, if (enabled) PRIMARY else TEXT_LIGHT, action).apply { minHeight = dp(40); isEnabled = enabled }
    private fun smallOutlineButton(value: String, action: () -> Unit): TextView = buttonView(value, PRIMARY_DARK, SURFACE, action, PRIMARY_SOFT).apply { minHeight = dp(38); setPadding(dp(12), 0, dp(12), 0); textSize = 12f }
    private fun smallDangerButton(value: String, action: () -> Unit): TextView = buttonView(value, ERROR, ERROR_SOFT, action).apply { minHeight = dp(38); setPadding(dp(12), 0, dp(12), 0); textSize = 12f }

    private fun buttonView(value: String, color: Int, backgroundColor: Int, action: () -> Unit, strokeColor: Int? = null): TextView = TextView(this).apply {
        text = value; textSize = 14f; typeface = Typeface.DEFAULT_BOLD; gravity = Gravity.CENTER; setTextColor(color); minHeight = dp(50); setPadding(dp(18), 0, dp(18), 0)
        background = if (strokeColor == null) ripple(backgroundColor, dp(25).toFloat()) else RippleDrawable(ColorStateList.valueOf(PRIMARY_SOFT), rounded(backgroundColor, dp(25).toFloat(), strokeColor, dp(1)), null)
        isClickable = true; isFocusable = true; setOnClickListener { action() }
    }

    private fun textButton(value: String, action: () -> Unit): TextView = TextView(this).apply { text = value; textSize = 13f; typeface = Typeface.DEFAULT_BOLD; setTextColor(PRIMARY_DARK); gravity = Gravity.CENTER; setPadding(dp(12), dp(8), dp(12), dp(8)); background = ripple(PRIMARY_SOFT, dp(18).toFloat()); setOnClickListener { action() } }
    private fun quantityButton(value: String, enabled: Boolean, action: () -> Unit): TextView = TextView(this).apply { text = value; textSize = 20f; gravity = Gravity.CENTER; typeface = Typeface.DEFAULT_BOLD; setTextColor(if (enabled) PRIMARY_DARK else TEXT_LIGHT); background = ripple(PRIMARY_SOFT, dp(19).toFloat()); isEnabled = enabled; setOnClickListener { action() }; layoutParams = LinearLayout.LayoutParams(dp(38), dp(38)) }
    private fun iconAction(value: String, description: String, action: () -> Unit): TextView = TextView(this).apply { text = value; textSize = 34f; gravity = Gravity.CENTER; setTextColor(TEXT); background = ripple(Color.TRANSPARENT, dp(23).toFloat()); contentDescription = description; setOnClickListener { action() } }

    private fun summaryRow(labelText: String, value: String): View = LinearLayout(this).apply { orientation = LinearLayout.HORIZONTAL; addView(bodyText(labelText), LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f)); addView(bodyText(value).apply { typeface = Typeface.DEFAULT_BOLD }) }

    private fun statusChip(status: String): TextView {
        val colors = when (status) { "Delivered" -> SUCCESS to SUCCESS_SOFT; "Canceled" -> ERROR to ERROR_SOFT; "On The Way" -> INFO to INFO_SOFT; else -> WARNING to WARNING_SOFT }
        return caption(status).apply { setTextColor(colors.first); typeface = Typeface.DEFAULT_BOLD; setPadding(dp(10), dp(5), dp(10), dp(5)); background = rounded(colors.second, dp(14).toFloat()) }
    }

    private fun settingsRow(titleText: String, subtitle: String, icon: Int, action: () -> Unit): View = card().apply {
        orientation = LinearLayout.HORIZONTAL; gravity = Gravity.CENTER_VERTICAL; setOnClickListener { action() }; isClickable = true; isFocusable = true
        addView(ImageView(this@MainActivity).apply { setImageResource(icon); imageTintList = ColorStateList.valueOf(PRIMARY) }, LinearLayout.LayoutParams(dp(30), dp(30)))
        addView(LinearLayout(this@MainActivity).apply { orientation = LinearLayout.VERTICAL; setPadding(dp(14), 0, dp(6), 0); addView(sectionTitle(titleText)); addView(caption(subtitle), topMargin(dp(3))) }, LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f))
        addView(bodyText("›").apply { textSize = 28f; setTextColor(TEXT_LIGHT) })
    }

    private fun setButtonBusy(button: TextView, busy: Boolean, label: String) { button.text = label; button.isEnabled = !busy; button.alpha = if (busy) .65f else 1f }
    private fun confirm(titleText: String, message: String, action: () -> Unit) { AlertDialog.Builder(this).setTitle(titleText).setMessage(message).setNegativeButton("Keep", null).setPositiveButton("Continue") { _, _ -> action() }.show() }
    override fun onResume() {
        super.onResume()
        if (paymentBrowserOpen) {
            paymentBrowserOpen = false
            showReservations()
        }
    }

    private fun openWebsite(path: String) { openExternal(BuildConfig.WEB_BASE_URL.trimEnd('/') + path) }
    private fun openPayment(url: String): Boolean {
        if (!url.startsWith("https://", ignoreCase = true)) { toast("The secure payment link is invalid."); return false }
        return openExternal(url)
    }
    private fun openExternal(url: String): Boolean {
        val uri = Uri.parse(url)
        if (uri.host.isNullOrBlank() || uri.scheme !in listOf("https", "http")) { toast("This link is invalid."); return false }
        return try { startActivity(Intent(Intent.ACTION_VIEW, uri)); true } catch (_: Exception) { toast("No browser is available on this device."); false }
    }
    private fun isFutureReservation(date: String, time: String): Boolean = try {
        val formatter = SimpleDateFormat("yyyy-MM-dd h:mm a", Locale.US).apply {
            isLenient = false
            timeZone = TimeZone.getTimeZone("Asia/Manila")
        }
        (formatter.parse("$date $time")?.time ?: 0L) > System.currentTimeMillis()
    } catch (_: Exception) { false }
    @Suppress("DEPRECATION")
    private fun configureWindow() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            window.insetsController?.setSystemBarsAppearance(
                android.view.WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS or android.view.WindowInsetsController.APPEARANCE_LIGHT_NAVIGATION_BARS,
                android.view.WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS or android.view.WindowInsetsController.APPEARANCE_LIGHT_NAVIGATION_BARS,
            )
        } else {
            var systemUiFlags = View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) systemUiFlags = systemUiFlags or View.SYSTEM_UI_FLAG_LIGHT_NAVIGATION_BAR
            window.decorView.systemUiVisibility = systemUiFlags
        }

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.VANILLA_ICE_CREAM) {
            val content = findViewById<View>(android.R.id.content)
            content.setOnApplyWindowInsetsListener { view, insets ->
                val bars = insets.getInsets(WindowInsets.Type.systemBars())
                view.setPadding(bars.left, bars.top, bars.right, bars.bottom)
                insets
            }
            content.requestApplyInsets()
        } else {
            window.statusBarColor = SURFACE
            window.navigationBarColor = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) SURFACE else PRIMARY_DARK
        }
    }
    private fun money(value: Double): String = NumberFormat.getCurrencyInstance(Locale("en", "PH")).format(value)
    private fun deviceName(): String {
        val devicePreferences = getSharedPreferences(DEVICE_PREFERENCES, Context.MODE_PRIVATE)
        val installationId = devicePreferences.getString(KEY_INSTALLATION_ID, null) ?: UUID.randomUUID().toString().also {
            devicePreferences.edit().putString(KEY_INSTALLATION_ID, it).apply()
        }
        return "${Build.MANUFACTURER} ${Build.MODEL} · ${installationId.take(12)}".take(80)
    }
    private fun toast(message: String) = Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    private fun dp(value: Int): Int = (value * resources.displayMetrics.density).toInt()
    private fun rounded(color: Int, radius: Float, strokeColor: Int? = null, strokeWidth: Int = 0): GradientDrawable = GradientDrawable().apply { shape = GradientDrawable.RECTANGLE; setColor(color); cornerRadius = radius; if (strokeColor != null) setStroke(strokeWidth, strokeColor) }
    private fun ripple(color: Int, radius: Float): RippleDrawable = RippleDrawable(ColorStateList.valueOf(withAlpha(PRIMARY, 38)), rounded(color, radius), null)
    private fun withAlpha(color: Int, alpha: Int): Int = Color.argb(alpha, Color.red(color), Color.green(color), Color.blue(color))
    private fun marginParams(top: Int, bottom: Int): LinearLayout.LayoutParams = LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT).apply { setMargins(0, top, 0, bottom) }
    private fun topMargin(value: Int): LinearLayout.LayoutParams = LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT).apply { topMargin = value }
    private fun bottomMargin(value: Int): LinearLayout.LayoutParams = LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT).apply { bottomMargin = value }
    private fun leftMargin(value: Int): LinearLayout.LayoutParams = LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT).apply { leftMargin = value }
    private fun JSONArray.objects(): List<JSONObject> = (0 until length()).map { getJSONObject(it) }

    private class SimpleTextWatcher(private val after: (String) -> Unit) : TextWatcher {
        override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) = Unit
        override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) = Unit
        override fun afterTextChanged(s: Editable?) = after(s?.toString().orEmpty())
    }

    private companion object {
        const val PREFERENCES = "mi_cusina_profile"; const val DEVICE_PREFERENCES = "mi_cusina_device"; const val KEY_INSTALLATION_ID = "installation_id"; const val KEY_ROLE = "role"; const val KEY_NAME = "name"; const val KEY_EMAIL = "email"; const val KEY_PHONE = "phone"; const val KEY_STAFF_ROLE = "staff_role"; const val KEY_PENDING_PAYMENT_URL = "pending_payment_url"; const val KEY_PENDING_PAYMENT_BOOKING_ID = "pending_payment_booking_id"
        val PRIMARY = Color.rgb(237, 13, 168); val PRIMARY_DARK = Color.rgb(135, 28, 119); val PRIMARY_SOFT = Color.rgb(250, 230, 246)
        val BACKGROUND = Color.rgb(248, 249, 251); val SURFACE = Color.WHITE; val BORDER = Color.rgb(230, 232, 236)
        val TEXT = Color.rgb(31, 35, 42); val TEXT_MUTED = Color.rgb(101, 108, 119); val TEXT_LIGHT = Color.rgb(151, 158, 168)
        val SUCCESS = Color.rgb(22, 128, 74); val SUCCESS_SOFT = Color.rgb(220, 252, 231); val ERROR = Color.rgb(190, 24, 93); val ERROR_SOFT = Color.rgb(253, 226, 236)
        val INFO = Color.rgb(29, 78, 216); val INFO_SOFT = Color.rgb(219, 234, 254); val WARNING = Color.rgb(180, 83, 9); val WARNING_SOFT = Color.rgb(254, 243, 199)
        val PHONE_REGEX = Regex("^(09[0-9]{9}|\\+639[0-9]{9})$")
        val BARANGAYS = mapOf(
            "Bantayan" to arrayOf("Atop-atop", "Baigad", "Baod", "Binaobao", "Botigues", "Doong", "Guiwanon", "Hilotongan", "Kabac", "Kabangbang", "Kampingganon", "Kangkaibe", "Lipayran", "Luyongbaybay", "Mojon", "Obo-ob", "Patao", "Putian", "Sillon", "Suba", "Sulangan", "Sungko", "Tamiao", "Ticad"),
            "Madridejos" to arrayOf("Bunakan", "Kangwayan", "Kaongkod", "Kodia", "Maalat", "Malbago", "Mancilang", "Pili", "Poblacion", "San Agustin", "Tabagak", "Talangnan", "Tarong", "Tugas"),
            "Santa Fe" to arrayOf("Balidbid", "Hagdan", "Hilantagaan", "Kinatarkan", "Langub", "Maricaban", "Okoy", "Poblacion", "Pooc", "Talisay"),
        )
    }
}

/** Stores bearer tokens with an AES key that never leaves Android Keystore. */
private class SecureTokenStore(context: Context) {
    private val preferences = context.getSharedPreferences("mi_cusina_secure", Context.MODE_PRIVATE)

    fun write(value: String) {
        try {
            val cipher = Cipher.getInstance(TRANSFORMATION); cipher.init(Cipher.ENCRYPT_MODE, key()); val encrypted = cipher.doFinal(value.toByteArray(Charsets.UTF_8))
            preferences.edit().putString(KEY_VALUE, Base64.encodeToString(encrypted, Base64.NO_WRAP)).putString(KEY_IV, Base64.encodeToString(cipher.iv, Base64.NO_WRAP)).apply()
        } catch (_: Exception) { clear() }
    }

    fun read(): String? = try {
        val value = preferences.getString(KEY_VALUE, null) ?: return null; val iv = preferences.getString(KEY_IV, null) ?: return null
        val cipher = Cipher.getInstance(TRANSFORMATION); cipher.init(Cipher.DECRYPT_MODE, key(), GCMParameterSpec(128, Base64.decode(iv, Base64.NO_WRAP)))
        String(cipher.doFinal(Base64.decode(value, Base64.NO_WRAP)), Charsets.UTF_8)
    } catch (_: Exception) { clear(); null }

    fun clear() { preferences.edit().clear().apply() }

    private fun key(): SecretKey {
        val store = KeyStore.getInstance("AndroidKeyStore").apply { load(null) }; (store.getKey(KEY_ALIAS, null) as? SecretKey)?.let { return it }
        val generator = KeyGenerator.getInstance(KeyProperties.KEY_ALGORITHM_AES, "AndroidKeyStore")
        generator.init(KeyGenParameterSpec.Builder(KEY_ALIAS, KeyProperties.PURPOSE_ENCRYPT or KeyProperties.PURPOSE_DECRYPT).setBlockModes(KeyProperties.BLOCK_MODE_GCM).setEncryptionPaddings(KeyProperties.ENCRYPTION_PADDING_NONE).build())
        return generator.generateKey()
    }

    private companion object { const val KEY_ALIAS = "mi_cusina_mobile_token"; const val KEY_VALUE = "value"; const val KEY_IV = "iv"; const val TRANSFORMATION = "AES/GCM/NoPadding" }
}
