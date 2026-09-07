package com.micusina.app

import android.app.Activity
import android.content.Intent
import android.graphics.Color
import android.graphics.Bitmap
import android.net.Uri
import android.os.Bundle
import android.view.Gravity
import android.view.View
import android.webkit.CookieManager
import android.webkit.WebSettings
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebResourceRequest
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.FrameLayout
import android.widget.ProgressBar
import android.widget.Toast

/** Secure Kotlin shell that presents the live Mi Cusina website as an Android app. */
class WebsiteActivity : Activity() {
    private lateinit var website: WebView
    private lateinit var loader: ProgressBar
    private var files: ValueCallback<Array<Uri>>? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        @Suppress("DEPRECATION")
        window.decorView.systemUiVisibility = View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR or
            View.SYSTEM_UI_FLAG_LIGHT_NAVIGATION_BAR
        window.statusBarColor = Color.WHITE
        window.navigationBarColor = Color.WHITE
        website = WebView(this)
        website.setBackgroundColor(Color.WHITE)
        loader = ProgressBar(this).apply { isIndeterminate = true }
        setContentView(website)
        addContentView(loader, FrameLayout.LayoutParams(100, 100).apply { gravity = Gravity.CENTER })
        CookieManager.getInstance().setAcceptCookie(true)
        website.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            databaseEnabled = true
            loadWithOverviewMode = true
            useWideViewPort = true
            cacheMode = WebSettings.LOAD_NO_CACHE
            allowFileAccess = false
            allowContentAccess = true
        }
        website.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView, request: WebResourceRequest): Boolean {
                val url = request.url
                if (url.host == SITE_HOST || url.host?.endsWith(".$SITE_HOST") == true) return false
                return try { startActivity(Intent(Intent.ACTION_VIEW, url)); true } catch (_: Exception) { true }
            }
            override fun onPageStarted(view: WebView, url: String?, favicon: Bitmap?) { loader.visibility = View.VISIBLE }
            override fun onPageFinished(view: WebView, url: String?) {
                super.onPageFinished(view, url)
                view.evaluateJavascript(MOBILE_LAYOUT_SCRIPT, null)
            }
        }
        website.webChromeClient = object : WebChromeClient() {
            override fun onProgressChanged(view: WebView, progress: Int) { loader.visibility = if (progress < 100) View.VISIBLE else View.GONE }
            override fun onShowFileChooser(view: WebView, callback: ValueCallback<Array<Uri>>, params: FileChooserParams): Boolean {
                files?.onReceiveValue(null); files = callback
                return try { startActivityForResult(params.createIntent(), FILE_PICKER); true }
                catch (_: Exception) { files = null; Toast.makeText(this@WebsiteActivity, "No file picker is available", Toast.LENGTH_LONG).show(); false }
            }
        }
        website.clearCache(true)
        website.loadUrl(BuildConfig.WEB_BASE_URL)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == FILE_PICKER) { files?.onReceiveValue(WebChromeClient.FileChooserParams.parseResult(resultCode, data)); files = null }
    }

    @Deprecated("Deprecated in Java")
    override fun onBackPressed() { if (website.canGoBack()) website.goBack() else super.onBackPressed() }
    override fun onDestroy() { files?.onReceiveValue(null); website.destroy(); super.onDestroy() }

    private companion object {
        const val FILE_PICKER = 1001
        const val SITE_HOST = "micusina-pos.com"
        private const val MOBILE_LAYOUT_SCRIPT = """
            (function () {
              var id = 'mi-cusina-android-mobile-layout';
              if (document.getElementById(id)) return;
              var viewport = document.querySelector('meta[name="viewport"]');
              if (!viewport) { viewport = document.createElement('meta'); viewport.name = 'viewport'; document.head.appendChild(viewport); }
              viewport.setAttribute('content', 'width=1280, maximum-scale=2');
              if (location.pathname.endsWith('/riders')) {
                document.documentElement.style.overflow = 'hidden';
                document.body.style.transform = 'scale(0.40)';
                document.body.style.transformOrigin = 'top left';
                document.body.style.width = '250%';
              }
              var style = document.createElement('style'); style.id = id;
              style.textContent = `
                @media (max-width: 999.98px) {
                  html, body.front-only { width:100%; min-height:100%; overflow-x:hidden !important; }
                  html body .burger-panel { background-size:cover !important; background-position:center !important; min-height:100svh !important; }
                  html body .burger-topbar { grid-template-columns:minmax(0,1fr) auto !important; min-height:68px !important; padding:9px 12px !important; }
                  html body .burger-mark { gap:5px !important; min-width:0 !important; }
                  html body .burger-mark::after { font-size:20px !important; overflow:hidden !important; text-overflow:ellipsis !important; }
                  html body .burger-mark img { height:32px !important; width:36px !important; }
                  html body .burger-login { gap:6px !important; }
                  html body .burger-login > a:first-child, html body .burger-login > a:last-child { font-size:11px !important; padding:8px 10px !important; }
                  html body .burger-copy { min-height:calc(100svh - 68px) !important; padding:36px 18px 60px !important; }
                  html body .burger-copy h1, html body .burger-copy h1 span { font-size:36px !important; letter-spacing:-.04em !important; line-height:1.02 !important; margin-bottom:14px !important; }
                  html body .burger-copy p { font-size:15px !important; line-height:1.45 !important; max-width:300px !important; }
                  html body .burger-actions { gap:10px !important; width:100% !important; }
                  html body .burger-primary, html body .burger-secondary { flex:1 1 0 !important; font-size:13px !important; justify-content:center !important; min-height:48px !important; padding:0 10px !important; }
                  /* Keep the website's desktop card design, scaled into a readable phone card. */
                  html body .mic-marketplace { background:#f6f8fb !important; padding:18px 0 32px !important; }
                  html body .mic-marketplace-inner { background:#fff !important; border:1px solid #e4e7ec !important; border-radius:18px !important; padding:14px !important; }
                  html body .mic-sortbar { display:flex !important; margin-bottom:14px !important; }
                  html body .mic-product-grid { display:grid !important; grid-template-columns:1fr !important; gap:14px !important; }
                  html body .mic-product-card { align-items:start !important; background:#fff !important; border:1px solid #e4e7ec !important; border-radius:14px !important; box-shadow:0 6px 16px rgba(15,23,42,.08) !important; display:grid !important; grid-template-columns:120px minmax(0,1fr) !important; min-height:0 !important; overflow:hidden !important; padding:12px !important; }
                  html body .mic-product-open { display:contents !important; pointer-events:auto !important; }
                  html body .mic-product-image-wrap { align-items:center !important; aspect-ratio:auto !important; background:#fff !important; display:flex !important; grid-row:1 / span 2 !important; height:205px !important; justify-content:center !important; overflow:hidden !important; padding:0 !important; width:120px !important; }
                  html body .mic-product-image-wrap img { border-radius:10px !important; display:block !important; height:100% !important; max-height:none !important; object-fit:contain !important; width:100% !important; }
                  html body .mic-product-body { align-items:flex-start !important; display:flex !important; flex-direction:column !important; padding:0 0 0 12px !important; }
                  html body .mic-product-title { color:#172033 !important; display:block !important; font-size:19px !important; line-height:1.1 !important; min-height:0 !important; }
                  html body .mic-product-price { color:#ef5d5d !important; display:block !important; font-size:18px !important; }
                  html body .mic-product-description { -webkit-box-orient:vertical !important; -webkit-line-clamp:2 !important; color:#475569 !important; display:-webkit-box !important; font-size:13px !important; line-height:1.35 !important; overflow:hidden !important; }
                  html body .mic-stock, html body .mic-product-meta { display:flex !important; font-size:12px !important; }
                  html body .mic-card-cart { align-items:center !important; display:flex !important; gap:8px !important; grid-column:2 !important; padding:7px 0 0 12px !important; }
                  html body .mic-card-cart input { height:34px !important; width:42px !important; }
                  html body .mic-card-cart > button { font-size:12px !important; height:36px !important; padding:0 9px !important; }
                  html body .inner-navbar .navbar-collapse:not(.show) { display:none !important; }
                  html body .inner-navbar .navbar-collapse.show { display:flex !important; flex-direction:column !important; }
                }`;
              style.textContent += `
                /* Desktop-style About: photo and copy remain aligned in two columns. */
                #about > .row { align-items:stretch !important; display:flex !important; flex-wrap:nowrap !important; margin:0 !important; }
                #about > .row > .col-lg-6 { flex:0 0 50% !important; max-width:50% !important; width:50% !important; }
                #about .has-img-bg { background-position:center !important; background-size:cover !important; min-height:520px !important; }
                #about .col-sm-8 { margin:0 auto !important; max-width:80% !important; padding:58px 0 !important; }
              `;
              document.head.appendChild(style);
            })();
        """
    }
}
