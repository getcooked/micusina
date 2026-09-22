import { Button, Text } from 'react-native-paper'; import { Screen } from '../components/Screen'; import { useAuth } from '../auth/AuthContext';
export function AccountScreen() { const { user, signOut } = useAuth(); return <Screen><Text variant="headlineSmall">{user?.name}</Text><Text>{user?.email}</Text><Button mode="outlined" onPress={signOut}>Sign out</Button></Screen>; }
