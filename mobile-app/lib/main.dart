import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:http/http.dart' as http;

const _apiBaseUrl = String.fromEnvironment('API_BASE_URL');

void main() => runApp(const MiCusinaApp());

class ApiClient {
  ApiClient(this.storage);
  final FlutterSecureStorage storage;

  String get baseUrl => _apiBaseUrl.replaceFirst(RegExp(r'/$'), '');
  Uri uri(String path) => Uri.parse('$baseUrl/api/mobile$path');

  Future<Map<String, String>> headers({bool json = true}) async {
    final token = await storage.read(key: 'api_token');
    return {
      'Accept': 'application/json',
      if (json) 'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  Future<dynamic> request(String method, String path, {Map<String, dynamic>? body}) async {
    if (baseUrl.isEmpty) throw Exception('Set API_BASE_URL when running the app.');
    final request = http.Request(method, uri(path))..headers.addAll(await headers());
    if (body != null) request.body = jsonEncode(body);
    final response = await http.Response.fromStream(await request.send());
    final data = response.body.isEmpty ? <String, dynamic>{} : jsonDecode(response.body);
    if (response.statusCode >= 400) {
      throw Exception(data is Map ? (data['message'] ?? data['errors']?.toString() ?? 'Request failed.') : 'Request failed.');
    }
    return data;
  }
}

class MiCusinaApp extends StatefulWidget {
  const MiCusinaApp({super.key});
  @override State<MiCusinaApp> createState() => _MiCusinaAppState();
}

class _MiCusinaAppState extends State<MiCusinaApp> {
  final storage = const FlutterSecureStorage();
  late final ApiClient api = ApiClient(storage);
  bool loading = true;
  bool signedIn = false;

  @override void initState() { super.initState(); _restoreSession(); }
  Future<void> _restoreSession() async {
    final token = await storage.read(key: 'api_token');
    if (mounted) setState(() { signedIn = token != null; loading = false; });
  }

  @override Widget build(BuildContext context) => MaterialApp(
    title: 'Mi Cusina',
    theme: ThemeData(colorSchemeSeed: const Color(0xffa72822), useMaterial3: true),
    home: loading ? const Scaffold(body: Center(child: CircularProgressIndicator())) : signedIn
      ? HomeScreen(api: api, onLogout: () => setState(() => signedIn = false))
      : LoginScreen(api: api, onLogin: () => setState(() => signedIn = true)),
  );
}

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key, required this.api, required this.onLogin});
  final ApiClient api; final VoidCallback onLogin;
  @override State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final email = TextEditingController(); final password = TextEditingController(); bool busy = false;
  Future<void> submit() async {
    setState(() => busy = true);
    try {
      final data = await widget.api.request('POST', '/login', body: {'email': email.text.trim(), 'password': password.text});
      await widget.api.storage.write(key: 'api_token', value: data['token']);
      widget.onLogin();
    } catch (e) { if (mounted) showError(context, e); }
    if (mounted) setState(() => busy = false);
  }
  @override Widget build(BuildContext context) => Scaffold(body: SafeArea(child: Center(child: SingleChildScrollView(child: Padding(
    padding: const EdgeInsets.all(24), child: Column(mainAxisSize: MainAxisSize.min, children: [
      const Icon(Icons.restaurant, size: 72, color: Color(0xffa72822)), const SizedBox(height: 12),
      Text('Mi Cusina', style: Theme.of(context).textTheme.headlineMedium), const SizedBox(height: 28),
      TextField(controller: email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Email')),
      TextField(controller: password, obscureText: true, onSubmitted: (_) => submit(), decoration: const InputDecoration(labelText: 'Password')),
      const SizedBox(height: 24), FilledButton(onPressed: busy ? null : submit, child: Text(busy ? 'Signing in…' : 'Sign in')),
    ]),
  ))));
}

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key, required this.api, required this.onLogout});
  final ApiClient api; final VoidCallback onLogout;
  @override State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int tab = 0;
  @override Widget build(BuildContext context) {
    final pages = [MenuScreen(api: widget.api), CartScreen(api: widget.api), OrdersScreen(api: widget.api)];
    return Scaffold(
      appBar: AppBar(title: const Text('Mi Cusina'), actions: [IconButton(icon: const Icon(Icons.logout), onPressed: () async { await widget.api.request('POST', '/logout'); await widget.api.storage.delete(key: 'api_token'); widget.onLogout(); })]),
      body: pages[tab],
      bottomNavigationBar: NavigationBar(selectedIndex: tab, onDestinationSelected: (value) => setState(() => tab = value), destinations: const [
        NavigationDestination(icon: Icon(Icons.restaurant_menu), label: 'Menu'), NavigationDestination(icon: Icon(Icons.shopping_cart), label: 'Cart'), NavigationDestination(icon: Icon(Icons.receipt_long), label: 'Orders'),
      ]),
    );
  }
}

class MenuScreen extends StatelessWidget {
  const MenuScreen({super.key, required this.api}); final ApiClient api;
  String imageUrl(dynamic food) => '${api.baseUrl}/food_img/${food['image']}';
  @override Widget build(BuildContext context) => FutureBuilder<dynamic>(future: api.request('GET', '/foods'), builder: (context, snap) {
    if (snap.hasError) return ErrorView(error: snap.error);
    if (!snap.hasData) return const Center(child: CircularProgressIndicator());
    final foods = snap.data['foods'] as List;
    return ListView.separated(padding: const EdgeInsets.all(16), itemCount: foods.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) {
      final food = foods[i]; return Card(child: ListTile(
        leading: food['image'] == null ? const Icon(Icons.fastfood) : Image.network(imageUrl(food), width: 56, fit: BoxFit.cover, errorBuilder: (_, __, ___) => const Icon(Icons.fastfood)),
        title: Text(food['title']), subtitle: Text('₱${food['price']} · ${food['stock']} available'),
        trailing: IconButton(icon: const Icon(Icons.add_shopping_cart), onPressed: food['stock'] > 0 ? () async { try { await api.request('POST', '/cart/${food['id']}', body: {'quantity': 1}); if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Added to cart.'))); } catch (e) { if (context.mounted) showError(context, e); } } : null),
      ));
    });
  });
}

class CartScreen extends StatelessWidget {
  const CartScreen({super.key, required this.api}); final ApiClient api;
  @override Widget build(BuildContext context) => FutureBuilder<dynamic>(future: api.request('GET', '/cart'), builder: (context, snap) {
    if (snap.hasError) return ErrorView(error: snap.error); if (!snap.hasData) return const Center(child: CircularProgressIndicator());
    final items = snap.data['items'] as List; if (items.isEmpty) return const Center(child: Text('Your cart is empty.'));
    final total = items.fold<double>(0, (sum, item) => sum + double.parse(item['price'].toString()));
    return ListView(padding: const EdgeInsets.all(16), children: [
      ...items.map((item) => Card(child: ListTile(title: Text(item['title']), subtitle: Text('Quantity: ${item['quantity']}'), trailing: Text('₱${item['price']}')))),
      const SizedBox(height: 12), Text('Total: ₱${total.toStringAsFixed(2)}', style: Theme.of(context).textTheme.titleLarge), const SizedBox(height: 12),
      FilledButton(onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => CheckoutScreen(api: api))), child: const Text('Checkout')),
    ]);
  });
}

class CheckoutScreen extends StatefulWidget { const CheckoutScreen({super.key, required this.api}); final ApiClient api; @override State<CheckoutScreen> createState() => _CheckoutScreenState(); }
class _CheckoutScreenState extends State<CheckoutScreen> {
  final name = TextEditingController(); final phone = TextEditingController(); final barangay = TextEditingController(); final purok = TextEditingController(); final details = TextEditingController(); final reference = TextEditingController(); String municipality = 'Bantayan'; String payment = 'Cash on Delivery'; bool busy = false;
  Future<void> submit() async { setState(() => busy = true); try { await widget.api.request('POST', '/checkout', body: {'name': name.text, 'phone': phone.text, 'municipality': municipality, 'barangay': barangay.text, 'purok': purok.text, 'address_details': details.text, 'payment_method': payment, 'payment_reference': reference.text}); if (mounted) { ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Order placed successfully.'))); Navigator.pop(context); } } catch (e) { if (mounted) showError(context, e); } if (mounted) setState(() => busy = false); }
  @override Widget build(BuildContext context) => Scaffold(appBar: AppBar(title: const Text('Checkout')), body: ListView(padding: const EdgeInsets.all(16), children: [
    TextField(controller: name, decoration: const InputDecoration(labelText: 'Full name')), TextField(controller: phone, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'Phone')),
    DropdownButtonFormField(value: municipality, items: const ['Bantayan', 'Madridejos', 'Santa Fe'].map((v) => DropdownMenuItem(value: v, child: Text(v))).toList(), onChanged: (v) => setState(() => municipality = v!), decoration: const InputDecoration(labelText: 'Municipality')),
    TextField(controller: barangay, decoration: const InputDecoration(labelText: 'Barangay')), TextField(controller: purok, decoration: const InputDecoration(labelText: 'Purok')), TextField(controller: details, decoration: const InputDecoration(labelText: 'Address details (optional)')),
    DropdownButtonFormField(value: payment, items: const ['Cash on Delivery', 'GCash', 'Bank Transfer'].map((v) => DropdownMenuItem(value: v, child: Text(v))).toList(), onChanged: (v) => setState(() => payment = v!), decoration: const InputDecoration(labelText: 'Payment method')),
    if (payment != 'Cash on Delivery') TextField(controller: reference, decoration: const InputDecoration(labelText: 'Payment reference')), const SizedBox(height: 24), FilledButton(onPressed: busy ? null : submit, child: Text(busy ? 'Placing order…' : 'Place order')),
  ]));
}

class OrdersScreen extends StatelessWidget { const OrdersScreen({super.key, required this.api}); final ApiClient api; @override Widget build(BuildContext context) => FutureBuilder<dynamic>(future: api.request('GET', '/orders'), builder: (context, snap) { if (snap.hasError) return ErrorView(error: snap.error); if (!snap.hasData) return const Center(child: CircularProgressIndicator()); final orders = snap.data['orders'] as List; return orders.isEmpty ? const Center(child: Text('No orders yet.')) : ListView.builder(padding: const EdgeInsets.all(16), itemCount: orders.length, itemBuilder: (_, i) { final order = orders[i]; return Card(child: ListTile(title: Text(order['title']), subtitle: Text('${order['delivery_status']} · ${order['payment_status']}'), trailing: Text('₱${order['price']}'))); }); }); }
class ErrorView extends StatelessWidget { const ErrorView({super.key, required this.error}); final Object? error; @override Widget build(BuildContext context) => Center(child: Padding(padding: const EdgeInsets.all(24), child: Text('Could not load data.\n$error', textAlign: TextAlign.center))); }
void showError(BuildContext context, Object error) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(error.toString().replaceFirst('Exception: ', ''))));
