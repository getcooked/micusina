import { useState } from 'react';
import { Image, StyleSheet, View } from 'react-native';
import { Button, Card, Text, TextInput } from 'react-native-paper';
import { Screen } from '../components/Screen';
import { ErrorNotice } from '../components/ErrorNotice';
import { useAuth } from '../auth/AuthContext';
import { apiError } from '../services/api';

const logoUrl = 'https://micusina-pos.com/assets/imgs/mi-cusina-transparent.png';

export function LoginScreen() {
  const { signIn, signUp } = useAuth();
  const [registering, setRegistering] = useState(false);
  const [email, setEmail] = useState(''); const [password, setPassword] = useState('');
  const [name, setName] = useState(''); const [phone, setPhone] = useState(''); const [address, setAddress] = useState(''); const [confirmPassword, setConfirmPassword] = useState('');
  const [code, setCode] = useState(''); const [needsCode, setNeedsCode] = useState(false); const [error, setError] = useState<string>(); const [busy, setBusy] = useState(false);
  const submit = async () => { setBusy(true); setError(undefined); try { if (registering) { await signUp({ name, email, phone, address, password, password_confirmation: confirmPassword }); return; } const result = await signIn(email, password, code || undefined); setNeedsCode(result.twoFactorRequired); if (result.message) setError(result.message); } catch (e) { setError(apiError(e).message); } finally { setBusy(false); } };
  const switchMode = () => { setRegistering(value => !value); setError(undefined); setNeedsCode(false); };
  return <Screen><View style={styles.brand}><Image source={{ uri: logoUrl }} style={styles.logo} resizeMode="contain" /><Text variant="headlineMedium">Mi Cusina</Text></View><Card><Card.Content style={styles.form}><Text variant="titleLarge">{registering ? 'Create your account' : 'Welcome back'}</Text><Text>{registering ? 'Create your account here—no browser or website is needed.' : 'Sign in to order and follow your deliveries.'}</Text>{registering && <TextInput label="Full name" value={name} onChangeText={setName} autoCapitalize="words" />}<TextInput label="Email" value={email} onChangeText={setEmail} autoCapitalize="none" keyboardType="email-address" />{registering && <><TextInput label="Mobile number (09XXXXXXXXX)" value={phone} onChangeText={setPhone} keyboardType="phone-pad" /><TextInput label="Delivery address" value={address} onChangeText={setAddress} multiline /></>}<TextInput label="Password" value={password} onChangeText={setPassword} secureTextEntry />{registering && <TextInput label="Confirm password" value={confirmPassword} onChangeText={setConfirmPassword} secureTextEntry />}{!registering && needsCode && <TextInput label="Authenticator code" value={code} onChangeText={setCode} keyboardType="number-pad" />}<ErrorNotice message={error} /><Button mode="contained" loading={busy} disabled={busy} onPress={submit}>{registering ? 'Create account' : needsCode ? 'Verify code' : 'Sign in'}</Button><Button mode="text" onPress={switchMode}>{registering ? 'Already have an account? Sign in' : 'New to Mi Cusina? Create an account'}</Button></Card.Content></Card></Screen>;
}
const styles = StyleSheet.create({ brand: { alignItems: 'center', gap: 6, marginVertical: 12 }, logo: { height: 88, width: 160 }, form: { gap: 12 } });
