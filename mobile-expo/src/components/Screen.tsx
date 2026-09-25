import { type ReactNode } from 'react';
import { ScrollView, StyleSheet, type RefreshControlProps } from 'react-native';

export function Screen({ children, refreshControl }: { children: ReactNode; refreshControl?: React.ReactElement<RefreshControlProps> }) {
  return <ScrollView style={styles.page} contentContainerStyle={styles.content} refreshControl={refreshControl} keyboardShouldPersistTaps="handled">{children}</ScrollView>;
}
const styles = StyleSheet.create({ page: { backgroundColor: '#f7f8fb' }, content: { gap: 12, paddingBottom: 28 } });
