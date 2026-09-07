package com.micusina.app

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.graphics.Color
import android.net.Uri
import android.os.Bundle
import android.text.InputType
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.ScrollView
import android.widget.Spinner
import android.widget.ArrayAdapter
import android.widget.TextView
import android.widget.Toast
import org.json.JSONArray
import org.json.JSONObject
import java.net.HttpURLConnection
import java.net.URL

/** Native Kotlin client for Mi Cusina's customer and staff APIs. */
class MainActivity : Activity() {
    private lateinit var page: LinearLayout
    private lateinit var preferences: android.content.SharedPreferences
    private var token = ""
    private var role = "user"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        preferences = getSharedPreferences(PREFERENCES, Context.MODE_PRIVATE)
        token = preferences.getString(KEY_TOKEN, "").orEmpty()
        role = preferences.getString(KEY_ROLE, "user").orEmpty()
        if (token.isBlank()) showLogin() else showHome()
    }

    private fun text(value: String, size: Float = 16f) = TextView(this).apply {
        text = value
        textSize = size
        setTextColor(Color.DKGRAY)
        setPadding(0, 10, 0, 10)
    }

    private fun showScreen(title: String) {
        val scroll = ScrollView(this)
        page = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding(32, 24, 32, 32)
            setBackgroundColor(BACKGROUND)
        }
        scroll.addView(page)
        setContentView(scroll)
        page.addView(text(title, 27f))
    }

    private fun input(hint: String, password: Boolean = false) = EditText(this).apply {
        this.hint = hint
        if (password) inputType = InputType.TYPE_CLASS_TEXT or InputType.TYPE_TEXT_VARIATION_PASSWORD
        page.addView(this)
    }

    private fun button(label: String) = Button(this).apply {
        text = label
        isAllCaps = false
        setTextColor(Color.WHITE)
        setBackgroundColor(PINK)
        page.addView(this)
    }

    private fun showLogin() {
        showScreen("Mi Cusina")
        page.addView(text("Native customer and staff application"))
        val email = input("Email")
        val password = input("Password", password = true)
        button("Sign in").setOnClickListener {
            request("POST", "/login", JSONObject().put("email", email.text).put("password", password.text)) { response ->
                token = response.getString("token")
                role = response.getJSONObject("user").optString("usertype", "user")
                preferences.edit().putString(KEY_TOKEN, token).putString(KEY_ROLE, role).apply()
                showHome()
            }
        }
        page.addView(text("New customer? Create and verify your account before signing in.", 14f))
        button("Create account").apply {
            setTextColor(Color.DKGRAY)
            setBackgroundColor(Color.WHITE)
            setOnClickListener { openWebsite("/register") }
        }
    }

    private fun addNavigation(staff: Boolean) {
        val items = if (staff) arrayOf("Dashboard", "Orders", "Inventory", "Sign out")
        else arrayOf("Menu", "Cart", "Orders", "Reserve", "Gallery", "Sign out")
        val row = LinearLayout(this)
        items.forEach { label ->
            row.addView(Button(this).apply {
                text = label
                isAllCaps = false
                setOnClickListener {
                    when (label) {
                        "Menu" -> showMenu()
                        "Cart" -> showCart()
                        "Orders" -> if (staff) showStaffOrders() else showOrders()
                        "Reserve" -> showReservations()
                        "Gallery" -> showGallery()
                        "Dashboard" -> showDashboard()
                        "Inventory" -> showInventory()
                        else -> logout()
                    }
                }
            }, LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1f))
        }
        page.addView(row)
    }

    private fun showHome() {
        if (role == "admin" || role == "staff") showDashboard() else showMenu()
    }

    private fun showMenu() {
        showScreen("Menu")
        addNavigation(false)
        val loading = text("Loading...")
        page.addView(loading)
        request("GET", "/foods") { response ->
            page.removeView(loading)
            response.getJSONArray("foods").forEachObject { food ->
                val id = food.getInt("id")
                val card = LinearLayout(this).apply {
                    orientation = LinearLayout.VERTICAL
                    setBackgroundColor(Color.WHITE)
                    addView(text("${food.getString("title")}  ₱${food.getDouble("price")}\n${food.optString("detail")}\nStock: ${food.getInt("stock")}", 17f))
                    addView(Button(this@MainActivity).apply {
                        text = "Add to cart"
                        isEnabled = food.getInt("stock") > 0
                        setOnClickListener { request("POST", "/cart/$id", JSONObject().put("quantity", 1)) { toast("Added to cart") } }
                    })
                }
                page.addView(card)
            }
        }
    }

    private fun showCart() {
        showScreen("Cart")
        addNavigation(false)
        val loading = text("Loading...")
        page.addView(loading)
        request("GET", "/cart") { response ->
            page.removeView(loading)
            val items = response.getJSONArray("items")
            var total = 0.0
            items.forEachObject { item ->
                total += item.getDouble("price")
                val cartId = item.getInt("id")
                val quantity = item.getInt("quantity")
                page.addView(text("${item.getString("title")} × $quantity — ₱${item.getDouble("price")}", 17f))
                val actions = LinearLayout(this)
                actions.addView(cartAction("−") { if (quantity > 1) updateCart(cartId, quantity - 1) })
                actions.addView(cartAction("+") { updateCart(cartId, quantity + 1) })
                actions.addView(cartAction("Remove") { request("DELETE", "/cart/$cartId") { showCart() } })
                page.addView(actions)
            }
            page.addView(text("Total: ₱$total", 20f))
            if (items.length() > 0) addCheckout()
        }
    }

    private fun addCheckout() {
        page.addView(text("Delivery details", 20f))
        val name = input("Full name")
        val phone = input("Phone")
        val municipality = select("Municipality", arrayOf("Bantayan", "Madridejos", "Santa Fe"))
        val barangay = input("Barangay")
        val purok = input("Purok")
        val addressDetails = input("Landmark or additional address details (optional)")
        val payment = select("Payment method", arrayOf("Cash on Delivery", "GCash", "Bank Transfer"))
        val reference = input("GCash/Bank reference number (if applicable)")
        button("Place order").setOnClickListener {
            val details = JSONObject()
            .put("name", name.text).put("phone", phone.text)
                .put("municipality", municipality.selectedItem.toString()).put("barangay", barangay.text)
                .put("purok", purok.text).put("address_details", addressDetails.text)
                .put("payment_method", payment.selectedItem.toString()).put("payment_reference", reference.text)
            request("POST", "/checkout", details) { toast("Order placed"); showOrders() }
        }
    }

    private fun showOrders() = showOrderList("My orders", false)

    private fun showOrderList(title: String, staff: Boolean) {
        showScreen(title)
        addNavigation(staff)
        val loading = text("Loading...")
        page.addView(loading)
        request("GET", if (staff) "/staff/orders" else "/orders") { response ->
            page.removeView(loading)
            response.getJSONArray("orders").forEachObject { order ->
                val id = order.getInt("id")
                page.addView(text("#$id ${order.getString("title")} × ${order.getInt("quantity")}\n${order.getString("delivery_status")}", 17f))
                if (staff) addStaffOrderActions(id, order.getString("delivery_status"))
            }
        }
    }

    private fun showDashboard() {
        showScreen("Staff dashboard")
        addNavigation(true)
        val loading = text("Loading...")
        page.addView(loading)
        request("GET", "/staff/dashboard") { response ->
            page.removeView(loading)
            page.addView(text("Pending: ${response.getInt("pending_orders")}\nOn the way: ${response.getInt("on_the_way_orders")}\nDelivered: ${response.getInt("delivered_orders")}\nLow stock: ${response.getInt("low_stock")}", 20f))
        }
    }

    private fun showStaffOrders() = showOrderList("Manage orders", true)

    private fun showReservations() {
        showScreen("Table reservations")
        addNavigation(false)
        page.addView(text("Reserve a table for ₱250. A 50% (₱125) deposit is paid securely through PayMongo.", 16f))
        val firstName = input("First name")
        val lastName = input("Last name")
        val phone = input("Mobile number (09XXXXXXXXX)")
        val guests = input("Number of guests")
        val date = input("Reservation date (YYYY-MM-DD)")
        val time = input("Reservation time")
        val payment = select("Payment method", arrayOf("GCash", "Bank Transfer"))
        button("Continue to secure payment").setOnClickListener {
            val count = guests.text.toString().toIntOrNull()
            if (count == null || count < 1) {
                toast("Enter a valid number of guests")
            } else {
                val reservation = JSONObject()
                    .put("first_name", firstName.text).put("last_name", lastName.text).put("phone", phone.text)
                    .put("guest", count).put("date", date.text).put("time", time.text)
                    .put("payment_method", payment.selectedItem.toString())
                request("POST", "/reservations", reservation) { response ->
                    val checkoutUrl = response.optString("checkout_url")
                    if (checkoutUrl.isBlank()) toast("Payment link was not returned")
                    else startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(checkoutUrl)))
                }
            }
        }
        page.addView(text("Your reservations", 20f))
        request("GET", "/reservations") { response ->
            response.getJSONArray("reservations").forEachObject { booking ->
                page.addView(text("${booking.optString("date")} ${booking.optString("time")} — ${booking.optInt("guest")} guest(s)\n${booking.optString("status")} • ${booking.optString("payment_status")}", 16f))
            }
        }
    }

    /** Bundled menu photography keeps this section available even without a connection. */
    private fun showGallery() {
        showScreen("Mi Cusina food gallery")
        addNavigation(false)
        page.addView(text("Explore our signature meals. These photos are included in the app for offline viewing.", 16f))
        val dishes = listOf(
            "Chicken Burger" to R.drawable.hero_chicken_burger_transparent,
            "Burger Spaghetti" to R.drawable.hero_burger_spaghetti_transparent,
            "Hotdog Sandwich" to R.drawable.hero_hotdog_sandwich_transparent,
            "Chicken Teriyaki" to R.drawable.hero_chicken_teriyaki_transparent,
            "Adobo Bunwich" to R.drawable.hero_adobo_bunwich_transparent,
            "Mi Cusina favourites" to R.drawable.mi_cusina_hero_food_edge,
            "Mi Cusina meal combo" to R.drawable.auth_food_combo_cutout,
        )
        dishes.forEach { (name, image) ->
            page.addView(text(name, 20f))
            page.addView(ImageView(this).apply {
                setImageResource(image)
                adjustViewBounds = true
                scaleType = ImageView.ScaleType.CENTER_INSIDE
                contentDescription = name
                layoutParams = LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, 420)
            })
        }
    }

    private fun showInventory() {
        showScreen("Inventory")
        addNavigation(true)
        val loading = text("Loading...")
        page.addView(loading)
        request("GET", "/staff/inventory") { response ->
            page.removeView(loading)
            response.getJSONArray("foods").forEachObject { food ->
                page.addView(text("${food.getString("title")} — stock: ${food.getInt("stock")}", 17f))
                if (role == "admin") {
                    val stock = input("New stock for ${food.getString("title")}")
                    button("Update stock").setOnClickListener {
                        val value = stock.text.toString().toIntOrNull()
                        if (value == null || value < 0) toast("Enter a valid stock quantity")
                        else request("PATCH", "/staff/inventory/${food.getInt("id")}", JSONObject().put("stock", value)) { showInventory() }
                    }
                }
            }
        }
    }

    private fun logout() = request("POST", "/logout") {
        preferences.edit().clear().apply()
        token = ""; role = "user"
        showLogin()
    }

    private fun select(label: String, choices: Array<String>) = Spinner(this).apply {
        adapter = ArrayAdapter(this@MainActivity, android.R.layout.simple_spinner_dropdown_item, choices)
        contentDescription = label
        page.addView(this)
    }

    private fun cartAction(label: String, onClick: () -> Unit) = Button(this).apply {
        text = label
        isAllCaps = false
        setOnClickListener { onClick() }
    }

    private fun updateCart(cartId: Int, quantity: Int) {
        request("PATCH", "/cart/$cartId", JSONObject().put("quantity", quantity)) { showCart() }
    }

    private fun addStaffOrderActions(orderId: Int, status: String) {
        val actions = LinearLayout(this)
        if (status == "In Progress") actions.addView(cartAction("Set on the way") {
            updateOrder(orderId, "On The Way")
        })
        if (status != "Delivered" && status != "Canceled") actions.addView(cartAction("Mark delivered") {
            updateOrder(orderId, "Delivered")
        })
        if (status == "In Progress") actions.addView(cartAction("Cancel") {
            updateOrder(orderId, "Canceled")
        })
        page.addView(actions)
    }

    private fun updateOrder(orderId: Int, status: String) {
        request("PATCH", "/staff/orders/$orderId", JSONObject().put("delivery_status", status)) { showStaffOrders() }
    }

    private fun openWebsite(path: String) {
        startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(BuildConfig.WEB_BASE_URL + path)))
    }

    private fun request(method: String, path: String, body: JSONObject? = null, onSuccess: (JSONObject) -> Unit) {
        Thread {
            try {
                val connection = (URL(BuildConfig.API_BASE_URL + path).openConnection() as HttpURLConnection).apply {
                    requestMethod = method
                    setRequestProperty("Accept", "application/json")
                    setRequestProperty("Content-Type", "application/json")
                    if (token.isNotBlank()) setRequestProperty("Authorization", "Bearer $token")
                    if (body != null) {
                        doOutput = true
                        outputStream.use { it.write(body.toString().toByteArray(Charsets.UTF_8)) }
                    }
                }
                val code = connection.responseCode
                val stream = if (code < 400) connection.inputStream else connection.errorStream
                    ?: throw IllegalStateException("The server did not return a response")
                val response = stream.bufferedReader().use { it.readText() }.let(::JSONObject)
                runOnUiThread {
                    if (code >= 400) toast(response.optString("message", "Request failed")) else onSuccess(response)
                }
                connection.disconnect()
            } catch (error: Exception) {
                runOnUiThread { toast(error.message ?: "Could not reach server") }
            }
        }.start()
    }

    private fun JSONArray.forEachObject(action: (JSONObject) -> Unit) {
        for (index in 0 until length()) action(getJSONObject(index))
    }

    private fun toast(message: String) = Toast.makeText(this, message, Toast.LENGTH_LONG).show()

    private companion object {
        const val PREFERENCES = "mi_cusina"
        const val KEY_TOKEN = "token"
        const val KEY_ROLE = "role"
        val PINK = Color.rgb(205, 45, 180)
        val BACKGROUND = Color.rgb(250, 244, 249)
    }
}
