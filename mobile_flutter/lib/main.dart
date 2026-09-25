import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

const brand = Color(0xff9b167d);
const apiBase = String.fromEnvironment('API_URL', defaultValue: 'http://10.0.2.2:8000');

void main() => runApp(const MiCusinaApp());

class MiCusinaApp extends StatelessWidget {
  const MiCusinaApp({super.key});
  @override
  Widget build(BuildContext context) => MaterialApp(
    debugShowCheckedModeBanner: false,
    title: 'Mi Cusina',
    theme: ThemeData(colorSchemeSeed: brand, useMaterial3: true),
    home: const LoginPage(),
  );
}

class LoginPage extends StatefulWidget { const LoginPage({super.key}); @override State<LoginPage> createState() => _LoginPageState(); }
class _LoginPageState extends State<LoginPage> {
  final email = TextEditingController(); final password = TextEditingController(); bool loading = false; String? message;
  Future<void> login() async {
    setState(() => loading = true);
    try {
      final response = await http.post(Uri.parse('$apiBase/api/mobile/login'), headers: {'Accept': 'application/json'}, body: {'email': email.text.trim(), 'password': password.text, 'device_name': 'Mi Cusina Android'});
      final body = jsonDecode(response.body) as Map<String, dynamic>;
      if (response.statusCode < 300 && body['token'] != null && mounted) Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => MenuPage(token: body['token'] as String)));
      else setState(() => message = body['message'] as String? ?? 'Unable to sign in.');
    } catch (_) { setState(() => message = 'Cannot reach the Mi Cusina server.'); }
    if (mounted) setState(() => loading = false);
  }
  @override Widget build(BuildContext context) => Scaffold(backgroundColor: const Color(0xfff7f8fb), body: SafeArea(child: ListView(padding: const EdgeInsets.all(28), children: [
    Image.network('$apiBase/assets/imgs/burger-hero.png', height: 200, errorBuilder: (_, __, ___) => const Icon(Icons.restaurant, size: 120, color: brand)),
    const Text('Welcome to Mi Cusina', textAlign: TextAlign.center, style: TextStyle(fontSize: 30, fontWeight: FontWeight.bold)), const SizedBox(height: 8), const Text('Fresh local favorites, simple ordering, and delivery tracking.', textAlign: TextAlign.center), const SizedBox(height: 28),
    Card(child: Padding(padding: const EdgeInsets.all(22), child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [const Text('Sign in', style: TextStyle(fontSize: 25, fontWeight: FontWeight.bold)), TextField(controller: email, decoration: const InputDecoration(labelText: 'Email address')), TextField(controller: password, obscureText: true, decoration: const InputDecoration(labelText: 'Password')), if (message != null) Text(message!, style: const TextStyle(color: Colors.red)), const SizedBox(height: 18), FilledButton(onPressed: loading ? null : login, style: FilledButton.styleFrom(backgroundColor: brand), child: Text(loading ? 'Signing in...' : 'Sign in'))])),
    TextButton(onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const RegisterPage())), child: const Text('Create customer account')),
  ])));
}

class RegisterPage extends StatelessWidget { const RegisterPage({super.key}); @override Widget build(BuildContext context) => Scaffold(appBar: AppBar(title: const Text('Create Account')), body: const Center(child: Text('Registration stays inside the Mi Cusina app.'))); }

class MenuPage extends StatefulWidget { final String token; const MenuPage({super.key, required this.token}); @override State<MenuPage> createState() => _MenuPageState(); }
class _MenuPageState extends State<MenuPage> { List<dynamic> foods = []; @override void initState(){super.initState(); load();} Future<void> load() async { final r=await http.get(Uri.parse('$apiBase/api/mobile/foods')); if(mounted)setState(()=>foods=(jsonDecode(r.body) as Map<String,dynamic>)['foods'] as List<dynamic>); } @override Widget build(BuildContext c)=>Scaffold(appBar:AppBar(title:const Text('Mi Cusina Menu')),body:foods.isEmpty?const Center(child:CircularProgressIndicator()):ListView(children:foods.map((f)=>ListTile(title:Text(f['title']),subtitle:Text('P${f['price']}'))).toList())); }
