import { Button, Card, Text } from 'react-native-paper';
import { Screen } from '../components/Screen';
import { useAuth } from '../auth/AuthContext';

export function CustomerHomeScreen({ navigation }: { navigation: { navigate: (name: string) => void } }) {
  const { user } = useAuth();

  return <Screen>
    <Text variant="headlineSmall">Welcome, {user?.name}</Text>
    <Text>Order your Mi Cusina favorites and follow your deliveries here.</Text>
    <Card>
      <Card.Content style={{ gap: 8 }}>
        <Text variant="titleLarge">Ready to order?</Text>
        <Text>Browse available dishes, add them to your cart, then check out securely.</Text>
        <Button mode="contained" onPress={() => navigation.navigate('Menu')}>Browse menu</Button>
      </Card.Content>
    </Card>
    <Card>
      <Card.Content style={{ gap: 8 }}>
        <Text variant="titleLarge">Track an order</Text>
        <Button mode="outlined" onPress={() => navigation.navigate('Orders')}>View my orders</Button>
      </Card.Content>
    </Card>
  </Screen>;
}
