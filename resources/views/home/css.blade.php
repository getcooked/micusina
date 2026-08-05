<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Mi Cusina restaurant ordering system.">
    <meta name="author" content="Devcrud">
    <title>Mi Cusina</title>
   
    <!-- font icons -->
    <link rel="stylesheet" href="assets/vendors/themify-icons/css/themify-icons.css">

    <link rel="stylesheet" href="assets/vendors/animate/animate.css">

    <!-- Bootstrap + Mi Cusina main styles -->
	<link rel="stylesheet" href="assets/css/foodhut.css">
    <style>
        /* Requested monochrome theme: every surface white, every text element black. */
        html,
        html body,
        html body [class],
        html body [id] {
            background-color: #fff !important;
            background-image: none !important;
            color: #000 !important;
        }

        html body *,
        html body *::before,
        html body *::after {
            color: #000 !important;
        }

        /* Keep every customer-facing page on the same clean white canvas. */
        html,
        body,
        body.content-page,
        .content-page,
        main,
        section,
        .section,
        .container-fluid.bg-dark,
        #about,
        #blog,
        #book,
        #contact,
        #testmonial,
        .gallery-section,
        .menu-section {
            background-color: #fff !important;
            background-image: none !important;
        }

        body,
        body.content-page,
        .container-fluid.bg-dark,
        #about,
        #blog,
        #book,
        #contact,
        #testmonial {
            color: #202124 !important;
        }

        .container-fluid.bg-dark h1,
        .container-fluid.bg-dark h2,
        .container-fluid.bg-dark h3,
        .container-fluid.bg-dark h4,
        .container-fluid.bg-dark h5,
        .container-fluid.bg-dark h6,
        .container-fluid.bg-dark p,
        .container-fluid.bg-dark label {
            color: #202124 !important;
        }

        /* Higher-specificity rules for pages that still contain older inline dark styles. */
        html body,
        html body.content-page,
        html body .content-page,
        html body .cart-page,
        html body .orders-page,
        html body .receipt-page,
        html body .tracking-page,
        html body .booking-page,
        html body .blog-section,
        html body .gallery-section {
            background: #fff !important;
            background-image: none !important;
        }

        .site-flash {
            animation: siteFlashOut .2s ease 1.8s forwards;
            background: #111;
            border: 1px solid #F88379;
            border-radius: 999px;
            box-shadow: 0 14px 36px rgba(0, 0, 0, .32);
            color: #fff;
            font-size: 17px;
            font-weight: 800;
            left: 50%;
            max-width: calc(100% - 32px);
            padding: 14px 24px;
            position: fixed;
            text-align: center;
            top: 28px;
            transform: translateX(-50%);
            z-index: 3000;
        }

        @keyframes siteFlashOut {
            to {
                opacity: 0;
                transform: translate(-50%, -8px);
                visibility: hidden;
            }
        }

        /* Mi Cusina accent system */
        :root { --mi-primary:#f25f5c; --mi-primary-dark:#d94845; --mi-soft:#fff1f0; --mi-border:#eadfdd; }
        html body a { color:var(--mi-primary-dark) !important; }
        html body h1, html body h2, html body h3, html body h4, html body h5, html body h6 { color:#1f2937 !important; }
        html body .btn-primary, html body .btn-danger, html body .btn-custom, html body button[type="submit"] {
            background:var(--mi-primary) !important; border-color:var(--mi-primary) !important; color:#fff !important;
            box-shadow:0 6px 16px rgba(242,95,92,.22);
        }
        html body .btn-primary:hover, html body .btn-danger:hover, html body .btn-custom:hover, html body button[type="submit"]:hover {
            background:var(--mi-primary-dark) !important; border-color:var(--mi-primary-dark) !important;
        }
        html body .card, html body .menu-card, html body .food-card, html body .booking-card, html body .cart-card {
            border-color:var(--mi-border) !important; box-shadow:0 10px 28px rgba(31,41,55,.08) !important;
        }
        html body input:focus, html body textarea:focus, html body select:focus {
            border-color:var(--mi-primary) !important; box-shadow:0 0 0 3px rgba(242,95,92,.14) !important;
        }
    </style>
