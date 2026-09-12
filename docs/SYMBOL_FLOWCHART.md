# Mi Cusina traditional system flowchart

This is a symbol-based flowchart modeled on the supplied example.

**Symbol key:** oval = Start/End; parallelogram = input/output; rectangle = process; diamond = decision; cylinder = stored record; circle = connector to another part of the chart.

## 1. Entry, login, and registration

```mermaid
flowchart TD
    A([Start]) --> B[/Open Mi Cusina website or mobile app/]
    B --> C{Existing account?}
    C -- Yes --> D[/Enter email and password/]
    D --> E{Credentials valid?}
    E -- No --> F{Forgot password?}
    F -- Yes --> G[/Enter email and verification code/]
    G --> H[Reset password]
    H --> D
    F -- No --> Z([End])
    E -- Yes --> I[Create web session or Sanctum token]
    I --> J{Account role?}
    J -- Customer --> K((1))
    J -- Admin --> L((2))
    J -- Staff / rider --> M((3))
    C -- No --> N[/Enter name, email, mobile number, address, password/]
    N --> O{Details valid and unique?}
    O -- No --> N
    O -- Yes --> P[Send six-digit email OTP]
    P --> Q[/Enter OTP/]
    Q --> R{OTP correct and within 10 minutes?}
    R -- No --> Q
    R -- Yes --> S[(Create customer user record)]
    S --> T[/Registration successful/]
    T --> D
```

## 2. Customer menu, cart, checkout, and tracking

```mermaid
flowchart TD
    C1((1)) --> A[/Customer home page: view food menu/]
    A --> B{Add an item to cart?}
    B -- Yes --> C[/Select food and quantity/]
    C --> D{Item has enough stock?}
    D -- No --> E[/Show out-of-stock message/]
    E --> A
    D -- Yes --> F[(Create or update cart record)]
    F --> A
    B -- No --> G{Open cart?}
    G -- No --> H{Book a table?}
    H -- Yes --> R((4))
    H -- No --> Z([End])
    G -- Yes --> I[/Display cart and total/]
    I --> J{Change or remove an item?}
    J -- Yes --> K[Update or remove cart item]
    K --> F
    J -- No --> L{Proceed to checkout?}
    L -- No --> A
    L -- Yes --> M[/Enter delivery address, contact details, and payment method/]
    M --> N{All items still available?}
    N -- No --> O[/Show stock error; revise cart/]
    O --> I
    N -- Yes --> P[Create grouped order records; deduct food stock; clear cart]
    P --> Q[(Save Orders and Food stock)]
    Q --> S[/Show receipt and order status/]
    S --> T{Track order?}
    T -- Yes --> U[/Show In Progress, On The Way, Delivered, or Canceled/]
    U --> Z
    T -- No --> Z
```

## 3. Table reservation and PayMongo payment

```mermaid
flowchart TD
    C4((4)) --> A[/Enter reservation date, time, guests, and contact details/]
    A --> B{Reservation details valid?}
    B -- No --> A
    B -- Yes --> C[(Create booking: Awaiting Payment)]
    C --> D[Calculate 50% deposit]
    D --> E[Create PayMongo GCash or QRPH checkout session]
    E --> F[/Open PayMongo checkout page/]
    F --> G{Payment completed?}
    G -- No / canceled --> H[/Show payment not confirmed/]
    H --> Z([End])
    G -- Yes --> I[Receive signed PayMongo webhook or return link]
    I --> J{Payment signature, amount, and currency valid?}
    J -- No --> H
    J -- Yes --> K[(Update booking: Paid and Pending)]
    K --> L((2))
```

## 4. Admin and staff operations

```mermaid
flowchart TD
    C2((2)) --> A[/Open admin dashboard/]
    A --> B{Choose function}
    B -- Inventory --> C[/Add, edit, delete food, or update stock/]
    C --> D[(Save Food record)]
    D --> E{Stock at/below low-stock threshold?}
    E -- Yes --> F[FoodObserver sends email and SMS alert once per low-stock episode]
    E -- No --> A
    F --> A

    B -- Orders --> G[/View order queue/]
    G --> H{Assign available rider?}
    H -- Yes --> I[Assign rider and set rider unavailable]
    I --> J[(Update grouped order records)]
    J --> K((3))
    H -- No --> L{Mark payment paid, cancel, or update status?}
    L -- Yes --> J
    L -- No --> A

    B -- Reservations --> M[/View paid pending reservations/]
    M --> N{Approve reservation?}
    N -- Yes --> O[(Update booking: Approved and approved_at)]
    O --> A
    N -- No --> A

    B -- Users / staff --> P[/Create staff or set rider availability/]
    P --> Q[(Update User record)]
    Q --> A

    B -- Reports --> R[Calculate sales and transactions from Orders and approved Books]
    R --> S[/Display report or export PDF/]
    S --> A
```

## 5. Rider delivery completion

```mermaid
flowchart TD
    C3((3)) --> A[/Rider views assigned orders/]
    A --> B{Order status is On The Way?}
    B -- No --> C[/Wait for admin/cashier assignment and dispatch/]
    C --> A
    B -- Yes --> D[/Deliver order/]
    D --> E{Successfully delivered?}
    E -- Yes --> F[(Update grouped orders: Delivered)]
    F --> G[Set rider available]
    G --> H([End])
    E -- No --> I[/Report issue to admin/staff/]
    I --> A
```

## 6. Mobile-app route

```mermaid
flowchart TD
    A([Start mobile app]) --> B{Saved Sanctum token?}
    B -- No --> C[/Enter mobile login credentials/]
    C --> D{Valid login?}
    D -- No --> C
    D -- Yes --> E[(Store Sanctum token in secure storage)]
    B -- Yes --> F[/Open menu, cart, and orders tabs/]
    E --> F
    F --> G[Call Laravel /api/mobile endpoints]
    G --> H[(Use the same Foods, Carts, Orders, Books, and Users data)]
    H --> I([End])
```
