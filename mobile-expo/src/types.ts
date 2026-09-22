export type User = { id: number; name: string; email: string; phone?: string | null; address?: string | null; usertype: 'admin' | 'staff' | string; staff_role?: string | null };
export type Food = { id: number; title: string; detail?: string | null; category: string; price: number; stock: number; image_url?: string | null };
export type CartItem = { id: number; food_id: number; title: string; details?: string; quantity: number; price: number };
export type Order = { id: number; title: string; quantity: number; price: number | string; delivery_status: string; created_at: string; name?: string; address?: string };
export type ApiError = { message: string; fields?: Record<string, string[]>; unauthorized?: boolean };
