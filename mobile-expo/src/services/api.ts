import axios, { AxiosError } from 'axios';
import * as SecureStore from 'expo-secure-store';
import type { ApiError, CartItem, Food, Order, User } from '../types';

const TOKEN_KEY = 'mi_cusina_token';
const baseURL = process.env.EXPO_PUBLIC_API_BASE_URL ?? 'https://micusina-pos.com/api/mobile';
export const api = axios.create({ baseURL, timeout: 15000, headers: { Accept: 'application/json', 'Content-Type': 'application/json' } });
api.interceptors.request.use(async config => { const token = await SecureStore.getItemAsync(TOKEN_KEY); if (token) config.headers.Authorization = `Bearer ${token}`; return config; });

export function apiError(error: unknown): ApiError {
  if (!axios.isAxiosError(error)) return { message: 'Something went wrong. Please try again.' };
  const response = error as AxiosError<{ message?: string; errors?: Record<string, string[]> }>;
  if (!response.response) return { message: 'Unable to reach Mi Cusina. Check your internet connection.' };
  const fields = response.response.data?.errors;
  const firstFieldMessage = fields ? Object.values(fields).flat()[0] : undefined;
  return { message: firstFieldMessage ?? response.response.data?.message ?? 'Request failed. Please try again.', fields, unauthorized: response.response.status === 401 };
}
export const authApi = {
  async login(email: string, password: string, twoFactorCode?: string) {
    const { data } = await api.post<{ token?: string; user?: User; two_factor_required?: boolean; message?: string }>('/login', { email, password, device_name: 'Expo mobile app', two_factor_code: twoFactorCode });
    if (data.token) await SecureStore.setItemAsync(TOKEN_KEY, data.token);
    return data;
  },
  async sendRegistrationVerification(payload: { name: string; email: string; phone: string; address: string; password: string; password_confirmation: string }) {
    const { data } = await api.post<{ registration_id: string; message: string }>('/register/send-verification', payload);
    return data;
  },
  async verifyRegistration(registrationId: string, emailCode: string) {
    const { data } = await api.post<{ token: string; user: User }>('/register/verify', { registration_id: registrationId, email_code: emailCode, device_name: 'Expo mobile app' });
    await SecureStore.setItemAsync(TOKEN_KEY, data.token);
    return data.user;
  },
  async logout() { try { await api.post('/logout'); } finally { await SecureStore.deleteItemAsync(TOKEN_KEY); } },
  async restore() { const token = await SecureStore.getItemAsync(TOKEN_KEY); if (!token) return null; const { data } = await api.get<{ user: User }>('/me'); return data.user; },
};
export const mobileApi = {
  foods: async () => (await api.get<{ foods: Food[] }>('/foods')).data.foods,
  cart: async () => (await api.get<{ items: CartItem[] }>('/cart')).data.items,
  addToCart: (id: number) => api.post(`/cart/${id}`, { quantity: 1 }),
  updateCart: (id: number, quantity: number) => api.patch(`/cart/${id}`, { quantity }),
  removeFromCart: (id: number) => api.delete(`/cart/${id}`),
  checkout: (payload: Record<string, string>) => api.post('/checkout', payload),
  orders: async () => (await api.get<{ orders: Order[] }>('/orders')).data.orders,
  staffDashboard: async () => (await api.get('/staff/dashboard')).data as { pending_orders: number; on_the_way_orders: number; delivered_orders: number; low_stock: number },
  staffOrders: async () => (await api.get<{ orders: Order[] }>('/staff/orders')).data.orders,
  updateOrder: (id: number, delivery_status: 'Delivered' | 'Canceled') => api.patch(`/staff/orders/${id}`, { delivery_status }),
  inventory: async () => (await api.get<{ foods: Food[] }>('/staff/inventory')).data.foods,
};
