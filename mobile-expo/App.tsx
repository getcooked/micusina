import { Text, View } from 'react-native';
import { ActivityIndicator, MD3LightTheme, PaperProvider } from 'react-native-paper';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { AuthProvider, useAuth } from './src/auth/AuthContext';
import { LoginScreen } from './src/screens/LoginScreen';
import { MenuScreen } from './src/screens/MenuScreen';
import { CartScreen } from './src/screens/CartScreen';
import { OrdersScreen } from './src/screens/OrdersScreen';
import { AccountScreen } from './src/screens/AccountScreen';
import { StaffDashboardScreen } from './src/screens/StaffDashboardScreen';
import { StaffOrdersScreen } from './src/screens/StaffOrdersScreen';
import { LowStockScreen } from './src/screens/LowStockScreen';

const Tab = createBottomTabNavigator();
const theme = { ...MD3LightTheme, colors: { ...MD3LightTheme.colors, primary: '#8d197f', secondary: '#e70db5', background: '#f7f8fb' } };
const icon = (symbol: string) => () => <Text style={{ fontSize: 21 }}>{symbol}</Text>;
function Root() { const { user, loading } = useAuth(); if (loading) return <View style={{ flex: 1, justifyContent: 'center' }}><ActivityIndicator color="#8d197f" /></View>; if (!user) return <LoginScreen />; const staff = user.usertype === 'staff' || user.usertype === 'admin'; return <NavigationContainer><Tab.Navigator screenOptions={{ headerShown: false, tabBarActiveTintColor: '#8d197f', tabBarInactiveTintColor: '#6f747d', tabBarLabelStyle: { fontSize: 12, fontWeight: '700' }, tabBarStyle: { height: 68, paddingTop: 6 } }}>{staff ? <><Tab.Screen name="Dashboard" component={StaffDashboardScreen} options={{ tabBarIcon: icon('▦') }} /><Tab.Screen name="Orders" component={StaffOrdersScreen} options={{ tabBarIcon: icon('▤') }} /><Tab.Screen name="Low stock" component={LowStockScreen} options={{ tabBarIcon: icon('!') }} /></> : <><Tab.Screen name="Menu" component={MenuScreen} options={{ tabBarIcon: icon('🍴') }} /><Tab.Screen name="Cart" component={CartScreen} options={{ tabBarIcon: icon('🛒') }} /><Tab.Screen name="Orders" component={OrdersScreen} options={{ tabBarIcon: icon('▤') }} /><Tab.Screen name="Account" component={AccountScreen} options={{ tabBarIcon: icon('◯') }} /></>}</Tab.Navigator></NavigationContainer>; }
export default function App() { return <PaperProvider theme={theme}><AuthProvider><Root /></AuthProvider></PaperProvider>; }
