package com.micusina.app

import org.junit.Assert.assertEquals
import org.junit.Assert.assertNull
import org.junit.Test

class AppNavigationTest {
    @Test
    fun customersHaveShoppingDestinations() {
        assertEquals(
            listOf("menu", "cart", "orders", "reserve", "more"),
            AppNavigation.destinations("user", "")
        )
        assertEquals("menu", AppNavigation.home("user"))
    }

    @Test
    fun administratorsAndCashiersHaveManagementDestinations() {
        val expected = listOf("dashboard", "staff_orders", "inventory", "more")
        assertEquals(expected, AppNavigation.destinations("admin", ""))
        assertEquals(expected, AppNavigation.destinations("staff", "cashier"))
        assertEquals("dashboard", AppNavigation.home("admin"))
        assertEquals("dashboard", AppNavigation.home("staff"))
    }

    @Test
    fun ridersDoNotHaveInventoryNavigation() {
        assertEquals(
            listOf("dashboard", "staff_orders", "more"),
            AppNavigation.destinations("staff", "rider")
        )
        assertEquals("dashboard", AppNavigation.resolve("inventory", "staff", "rider"))
    }

    @Test
    fun otherStaffDoNotHaveOrderNavigation() {
        listOf("kitchen", "", "unknown").forEach { staffRole ->
            assertEquals(
                listOf("dashboard", "inventory", "more"),
                AppNavigation.destinations("staff", staffRole)
            )
            assertEquals("dashboard", AppNavigation.resolve("staff_orders", "staff", staffRole))
        }
    }

    @Test
    fun unknownRolesNeverReceiveStaffDestinations() {
        listOf("", "unknown", "ADMIN").forEach { role ->
            assertEquals("menu", AppNavigation.home(role))
            assertEquals("menu", AppNavigation.resolve("dashboard", role, "cashier"))
            assertEquals("menu", AppNavigation.resolve("staff_orders", role, "cashier"))
            assertEquals("menu", AppNavigation.resolve("inventory", role, "cashier"))
        }
    }

    @Test
    fun allAuthorizedTabsResolveToThemselves() {
        listOf("user" to "", "admin" to "", "staff" to "cashier", "staff" to "rider", "staff" to "kitchen")
            .forEach { (role, staffRole) ->
                AppNavigation.destinations(role, staffRole).forEach { destination ->
                    assertEquals(destination, AppNavigation.resolve(destination, role, staffRole))
                }
            }
    }

    @Test
    fun customerDetailsResolveWithExpectedParents() {
        mapOf("checkout" to "cart", "booking" to "reserve", "success" to "orders", "gallery" to "more")
            .forEach { (destination, parent) ->
                assertEquals(destination, AppNavigation.resolve(destination, "user", ""))
                assertEquals(parent, AppNavigation.parent(destination, "user"))
            }
    }

    @Test
    fun staffCannotResolveCustomerTabsOrDetails() {
        listOf("admin", "staff").forEach { role ->
            listOf("menu", "cart", "orders", "reserve", "checkout", "booking", "success").forEach { destination ->
                assertEquals("dashboard", AppNavigation.resolve(destination, role, "cashier"))
                assertNull(AppNavigation.parent(destination, role))
            }
            assertEquals("gallery", AppNavigation.resolve("gallery", role, "cashier"))
            assertEquals("more", AppNavigation.parent("gallery", role))
        }
    }

    @Test
    fun invalidDestinationsFallBackToRoleHome() {
        listOf(null, "", "unknown", "login", "launch", "staff_orders").forEach { destination ->
            assertEquals("menu", AppNavigation.resolve(destination, "user", ""))
        }
        listOf(null, "", "unknown", "login", "launch").forEach { destination ->
            assertEquals("dashboard", AppNavigation.resolve(destination, "staff", "rider"))
        }
        assertNull(AppNavigation.parent("menu", "user"))
        assertNull(AppNavigation.parent("unknown", "user"))
    }

    @Test
    fun restoreKeepsAnAuthorizedDestinationWithoutPendingMutations() {
        assertEquals("cart", AppNavigation.restore("cart", "user", ""))
        assertEquals("checkout", AppNavigation.restore("checkout", "user", ""))
        assertEquals("staff_orders", AppNavigation.restore("staff_orders", "staff", "rider"))
        assertEquals("menu", AppNavigation.restore(null, "user", ""))
        assertEquals("dashboard", AppNavigation.restore("staff_orders", "staff", "kitchen"))
    }

    @Test
    fun restoreChecksOrderOrReservationResultsInsteadOfResubmittingForms() {
        assertEquals("orders", AppNavigation.restore("checkout", "user", "", checkoutPending = true))
        assertEquals("reserve", AppNavigation.restore("booking", "user", "", reservationPending = true))
        assertEquals(
            "orders",
            AppNavigation.restore("booking", "user", "", checkoutPending = true, reservationPending = true)
        )
    }

    @Test
    fun pendingCustomerMutationsNeverRestoreIntoStaffScreens() {
        listOf("admin", "staff").forEach { role ->
            assertEquals("dashboard", AppNavigation.restore("checkout", role, "cashier", checkoutPending = true))
            assertEquals("dashboard", AppNavigation.restore("booking", role, "rider", reservationPending = true))
        }
    }
}
