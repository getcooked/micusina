package com.micusina.app

object AppNavigation {
    fun destinations(role: String, staffRole: String): List<String> = when (role) {
        "admin" -> listOf("dashboard", "staff_orders", "inventory", "more")
        "staff" -> when (staffRole) {
            "cashier" -> listOf("dashboard", "staff_orders", "inventory", "more")
            "rider" -> listOf("dashboard", "staff_orders", "more")
            else -> listOf("dashboard", "inventory", "more")
        }
        else -> listOf("menu", "reserve", "orders", "help", "more")
    }

    fun home(role: String): String = if (isStaff(role)) "dashboard" else "menu"

    fun resolve(destination: String?, role: String, staffRole: String): String {
        if (destination != null && authorizedTopLevel(destination, role, staffRole) != null) {
            return destination
        }
        return home(role)
    }

    fun topLevel(destination: String, role: String, staffRole: String): String =
        authorizedTopLevel(destination, role, staffRole) ?: home(role)

    private fun authorizedTopLevel(destination: String, role: String, staffRole: String): String? {
        val allowed = destinations(role, staffRole)
        val visited = mutableSetOf<String>()
        var current: String? = destination
        while (current != null && visited.add(current)) {
            if (current in allowed) return current
            current = parent(current, role)
        }
        return null
    }

    fun parent(destination: String, role: String): String? = when (destination) {
        "gallery" -> "more"
        "cart" -> if (isStaff(role)) null else "menu"
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
