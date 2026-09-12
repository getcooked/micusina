# Mi Cusina system flowchart

This diagram reflects the implemented Laravel web application, its Flutter mobile client, the shared database, and the external services it calls. Paste the Mermaid blocks into a Mermaid-compatible Markdown preview (GitHub, GitLab, Mermaid Live Editor, or VS Code Mermaid extension) to render them.

## Overall architecture

```mermaid
flowchart TB
    Visitor[Customer / visitor]
    Web[Web browser\nBlade views]
    Mobile[Flutter mobile app\nSanctum bearer token]
    Staff[Admin, cashier, or rider]

    Visitor --> Web
    Staff --> Web
    Mobile -->|REST /api/mobile| API[Laravel MobileApiController]
    Web -->|Web routes| App[Laravel application\nControllers + middleware]
    API --> App

    App --> Auth[Auth: Laravel session / Sanctum\nroles: user, admin, staff]
    App --> Core[Business functions\nmenu, cart, orders, reservations\ninventory, reporting, chatbot]
    Core <--> DB[(Database\nusers, foods, carts, orders, books\npersonal_access_tokens)]

    Core --> Observer[FoodObserver]
    Observer -->|stock at/below threshold| Alerts[LowStockNotifier]
    Alerts --> Email[Mail service]
    Alerts --> SMS[Infobip or Twilio SMS]

    Core -->|reservation checkout| PayMongo[PayMongo]
    PayMongo -->|signed return URL or signed webhook| Payment[PayMongoWebhookController]
    Payment --> DB

    Auth --> OTP[RegistrationOtpSender]
    OTP --> Email
```

## Customer ordering flow

```mermaid
flowchart TD
    Start([Customer opens menu]) --> Menu[Load available foods]
    Menu --> Add[Add item / quantity to cart]
    Add --> Cart[(Cart record)]
    Cart --> Checkout[Enter delivery and payment details]
    Checkout --> Valid{Cart stock\nstill sufficient?}
    Valid -- No --> Fix[Show validation error\nand adjust cart]
    Fix --> Cart
    Valid -- Yes --> Tx[Database transaction]
    Tx --> Order[Create one Order per cart item\nwith one checkout_group_id]
    Order --> Stock[Decrease Food.stock]
    Stock --> Empty[Clear customer cart]
    Empty --> Track[Customer views order receipt,\nmy orders, or tracking]

    Order --> Status[In Progress]
    Status --> Assign[Admin/cashier assigns available rider]
    Assign --> Way[On The Way]
    Way --> Delivered[Delivered]
    Status --> Cancelled[Canceled]
    Delivered --> Payment{Payment method}
    Payment -->|Cash on Delivery| MarkPaid[Staff marks payment Paid]
    Payment -->|GCash / bank transfer| Verify[Staff verifies reference\nand marks Paid]
```

## Reservation and online-payment flow

```mermaid
sequenceDiagram
    participant C as Customer (web or mobile)
    participant L as Laravel
    participant D as Database
    participant P as PayMongo
    participant A as Admin / staff

    C->>L: Submit reservation details and payment method
    L->>D: Create Book: Awaiting Payment + 50% deposit
    L->>P: Create GCash or QRPH checkout session
    P-->>L: Checkout URL and checkout ID
    L->>D: Save checkout ID / URL
    L-->>C: Redirect or return checkout URL
    C->>P: Complete payment
    P->>L: Signed webhook (or customer returns via signed URL)
    L->>P: Verify checkout/payment when needed
    L->>D: Mark Book Paid and Pending
    A->>L: Approve reservation
    L->>D: Set reservation Approved / approved_at
```

## Authentication and role routing

```mermaid
flowchart LR
    Register[Register form] --> Validate[Validate identity, contact details\nand password]
    Validate --> Code[Email a six-digit OTP\nvalid for 10 minutes]
    Code --> Verify{OTP correct\nand unexpired?}
    Verify -- No --> Register
    Verify -- Yes --> User[(Create user\nrole: user)]
    User --> Login[Session login]
    Login --> Role{User role}
    Role -->|user| Home[Customer menu / cart / orders / reservation]
    Role -->|admin| Admin[Admin dashboard, inventory, reports,\nusers, staff, riders, orders, reservations]
    Role -->|staff| Staff[Staff dashboard and assigned duties]

    MobileLogin[Mobile login] --> Token[Sanctum API token]
    Token --> MobileRole{Role-protected mobile endpoints}
    MobileRole --> Home
    MobileRole --> Staff
    MobileRole --> Admin
```

## Inventory alert flow

```mermaid
flowchart TD
    Change[Food is created or stock changes\nby order checkout or inventory update] --> Save[Save Food model]
    Save --> Threshold{Stock <= configured\nlow-stock threshold?}
    Threshold -- No --> Reset[Clear previous low-stock\nnotification timestamps]
    Threshold -- Yes --> EmailNeeded{Email alert\nalready sent?}
    EmailNeeded -- No --> SendEmail[Send low-stock email]
    EmailNeeded -- Yes --> SmsNeeded
    SendEmail --> SmsNeeded{SMS alert\nalready sent?}
    SmsNeeded -- No --> SendSMS[Send through Infobip\nor Twilio]
    SmsNeeded -- Yes --> Record
    SendSMS --> Record[Persist per-channel notification\ntimestamps; mark fully notified\nwhen both succeeded]
```

## Administrative reporting and operations

```mermaid
flowchart LR
    Admin[Admin dashboard] --> Inventory[Food catalog and stock management]
    Admin --> Fulfillment[Order queue: rider assignment,\nstatus, payment status]
    Admin --> Reservations[Reservation approval]
    Admin --> StaffMgmt[Create staff / manage rider availability]
    Admin --> Reports[Sales report and transaction history\nwith PDF exports]

    Inventory --> Foods[(foods)]
    Fulfillment --> Orders[(orders)]
    Reservations --> Books[(books)]
    StaffMgmt --> Users[(users)]
    Reports --> Orders
    Reports --> Books
```

## Mobile API surface

```mermaid
flowchart LR
    App[Flutter app] --> Login[/POST mobile/login/]
    Login --> Token[Sanctum token stored\nin secure storage]
    Token --> Customer[/foods, /cart, /checkout,\n/orders, /reservations/]
    Token --> Staff[/staff/dashboard, /staff/orders,\n/staff/inventory/]
    Customer --> DB[(Shared Laravel database)]
    Staff --> DB
```

### Main state values

- Orders: `In Progress` -> `On The Way` -> `Delivered`, or `Canceled`; payment is tracked independently (`Unpaid`, `Pending Verification`, or `Paid`).
- Reservations: `Awaiting Payment` -> `Paid` + `Pending` -> `Approved` after staff review.
- Food alerts: alerts are sent once per low-stock episode and reset after stock rises above the configured threshold.
