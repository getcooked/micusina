import { useCallback, useState } from 'react';
import { Image, StyleSheet, View } from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import { Button, Card, Text } from 'react-native-paper';
import { Screen } from '../components/Screen';
import { ErrorNotice } from '../components/ErrorNotice';
import { apiError, mobileApi } from '../services/api';
import type { Food } from '../types';

export function MenuScreen() {
  const [foods, setFoods] = useState<Food[]>([]); const [error, setError] = useState<string>();
  const load = async () => { try { setError(undefined); setFoods(await mobileApi.foods()); } catch (e) { setError(apiError(e).message); } };
  useFocusEffect(useCallback(() => { load(); }, []));
  return <Screen><Text variant="headlineSmall">Menu</Text><ErrorNotice message={error} /><Button onPress={load}>Refresh</Button>{foods.map(food => <Card key={food.id}><Card.Content style={styles.card}>{food.image_url && <Image source={{ uri: food.image_url }} style={styles.productImage} resizeMode="cover" />}<View style={styles.details}><Text variant="titleMedium">{food.title}</Text><Text>{food.detail}</Text><Text>₱{food.price.toFixed(2)} · {food.stock} available</Text><Button mode="contained-tonal" disabled={food.stock < 1} onPress={() => mobileApi.addToCart(food.id).catch(e => setError(apiError(e).message))}>Add to cart</Button></View></Card.Content></Card>)}</Screen>;
}
const styles = StyleSheet.create({ card: { flexDirection: 'row', gap: 12 }, productImage: { width: 104, height: 104, borderRadius: 10, backgroundColor: '#f4f1ef' }, details: { flex: 1, gap: 5 } });
