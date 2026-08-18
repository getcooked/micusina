package com.micusina.app;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.graphics.Color;
import android.os.Bundle;
import android.view.ViewGroup;
import android.view.Gravity;
import android.graphics.drawable.GradientDrawable;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

public class MainActivity extends Activity {
    private WebView webView;

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (BuildConfig.WEBSITE_URL.contains("your-mi-cusina-domain.com")) {
            TextView notice = new TextView(this);
            notice.setBackgroundColor(Color.WHITE);
            notice.setTextColor(Color.BLACK);
            notice.setGravity(android.view.Gravity.CENTER);
            notice.setPadding(48, 48, 48, 48);
            notice.setTextSize(18);
            notice.setText("Mi Cusina mobile app\n\nSet your live website address in android-app/app/build.gradle, then build again.");
            setContentView(notice);
            return;
        }

        showRoleSelection();
    }

    private void showRoleSelection() {
        LinearLayout layout = new LinearLayout(this);
        layout.setOrientation(LinearLayout.VERTICAL);
        layout.setGravity(Gravity.CENTER);
        layout.setPadding(48, 48, 48, 48);
        layout.setBackgroundColor(Color.rgb(250, 244, 249));

        TextView title = new TextView(this);
        title.setText("Mi Cusina");
        title.setTextSize(32);
        title.setTextColor(Color.rgb(37, 25, 37));
        title.setGravity(Gravity.CENTER);
        title.setTypeface(null, android.graphics.Typeface.BOLD);
        layout.addView(title, new LinearLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT));

        TextView subtitle = new TextView(this);
        subtitle.setText("Choose how you want to continue");
        subtitle.setTextSize(16);
        subtitle.setTextColor(Color.DKGRAY);
        subtitle.setGravity(Gravity.CENTER);
        LinearLayout.LayoutParams subtitleParams = new LinearLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);
        subtitleParams.setMargins(0, 16, 0, 40);
        layout.addView(subtitle, subtitleParams);

        Button customerButton = roleButton("Customer\nBrowse the menu and place orders");
        customerButton.setOnClickListener(view -> openWebsite("/?section=food"));
        layout.addView(customerButton, buttonParams());

        Button staffButton = roleButton("Staff\nManage orders and deliveries");
        staffButton.setOnClickListener(view -> openWebsite("/home"));
        layout.addView(staffButton, buttonParams());

        setContentView(layout);
    }

    private Button roleButton(String label) {
        Button button = new Button(this);
        button.setAllCaps(false);
        button.setText(label);
        button.setTextSize(17);
        button.setTextColor(Color.WHITE);
        button.setGravity(Gravity.CENTER);
        GradientDrawable background = new GradientDrawable();
        background.setColor(Color.rgb(205, 45, 180));
        background.setCornerRadius(24);
        button.setBackground(background);
        return button;
    }

    private LinearLayout.LayoutParams buttonParams() {
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT, 112);
        params.setMargins(0, 0, 0, 20);
        return params;
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void openWebsite(String path) {
        webView = new WebView(this);
        webView.setLayoutParams(new ViewGroup.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.MATCH_PARENT));
        webView.getSettings().setJavaScriptEnabled(true);
        webView.getSettings().setDomStorageEnabled(true);
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                view.loadUrl(request.getUrl().toString());
                return true;
            }
        });
        webView.loadUrl(BuildConfig.WEBSITE_URL + path);
        setContentView(webView);
    }

    @Override
    public void onBackPressed() {
        if (webView != null && webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}
