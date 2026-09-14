package com.micusina.app

object AppNavigation {
    fun destinations(role: String, staffRole: String): List<String> = when (role) {
        "admin" -> listOf("dashboard", "staff_orders", "inventory", "more")
        "staff" -> when (staffRole) {
            "cashier" -> listOf("dashboard", "staff_orders", "inventory", "more")
            "rider" -> listOf("dashboard", "staff_orders", "more")
            else -> listOf("dashboard", "inventory", "more")
        }
        else -> listOf("menu", "cart", "orders", "reserve", "more")
    }

    fun home(role: String): String = if (isStaff(role)) "dashboard" else "menu"

    fun resolve(destination: String?, role: String, staffRole: String): String {
        val allowed = destinations(role, staffRole)
        if (destination != null && (destination in allowed || parent(destination, role) in allowed)) {
            return destination
        }
        return home(role)
    }

    fun parent(destination: String, role: String): String? = when (destination) {
        "gallery" -> "more"
        "checkout" -> if (isStaff(role)) null else "cart"
        "booking" -> if (isStaff(role)) null else "reserve"
        "success" -> if (isStaff(role)) null else "orders"
        else -> null
    }

    fun restore(
        destination: String?,
        role: String,
        staffRole: String,
        checkoutPending: Boolean = false,
        reservationPending: Boolean = false
    ): String = resolve(
        when {
            checkoutPending -> "orders"
            reservationPending -> "reserve"
            else -> destination
        },
        role,
        staffRole
    )

    private fun isStaff(role: String): Boolean = role == "admin" || role == "staff"
}
