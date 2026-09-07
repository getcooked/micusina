<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Book;
use App\Models\Food;
use App\Models\Order;
use App\Models\User;
use App\Services\PayMongoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MobileApiController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'The email address or password is incorrect.'], 422);
        }

        $user->tokens()->where('name', 'mobile-app')->delete();
        return response()->json(['token' => $user->createToken('mobile-app')->plainTextToken, 'user' => $this->user($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Signed out.']);
    }

    public function me(Request $request): JsonResponse { return response()->json(['user' => $this->user($request->user())]); }

    public function foods(): JsonResponse
    {
        return response()->json(['foods' => Food::orderBy('title')->get()->map(fn (Food $food) => $this->food($food))]);
    }

    public function cart(Request $request): JsonResponse
    {
        return response()->json(['items' => Cart::where('userid', $request->user()->id)->latest()->get()]);
    }

    public function addCart(Request $request, Food $food): JsonResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $cart = Cart::firstOrNew(['userid' => $request->user()->id, 'food_id' => $food->id]);
        $quantity = (int) $cart->quantity + $data['quantity'];
        abort_if($food->stock <= 0 || $quantity > $food->stock, 422, 'Only '.$food->stock.' item(s) are available.');
        $cart->fill(['title' => $food->title, 'details' => $food->detail, 'image' => $food->image, 'quantity' => $quantity, 'price' => $this->price($food->price) * $quantity])->save();
        return response()->json(['message' => 'Added to cart.', 'item' => $cart]);
    }

    public function updateCart(Request $request, Cart $cart): JsonResponse
    {
        abort_unless($cart->userid === $request->user()->id, 403);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $food = Food::find($cart->food_id);
        abort_if($food && $data['quantity'] > $food->stock, 422, 'Not enough stock available.');
        $unit = $this->price($cart->price) / max(1, (int) $cart->quantity);
        $cart->update(['quantity' => $data['quantity'], 'price' => $unit * $data['quantity']]);
        return response()->json(['item' => $cart->fresh()]);
    }

    public function removeCart(Request $request, Cart $cart): JsonResponse
    {
        abort_unless($cart->userid === $request->user()->id, 403);
        $cart->delete();
        return response()->json(['message' => 'Removed from cart.']);
    }

    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'phone' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'in:Bantayan,Madridejos,Santa Fe'], 'barangay' => ['required', 'string', 'max:100'],
            'purok' => ['required', 'string', 'max:100'], 'address_details' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:Cash on Delivery,GCash,Bank Transfer'], 'payment_reference' => ['nullable', 'string', 'max:100'],
        ]);
        $user = $request->user();
        $address = 'Purok '.$data['purok'].', Barangay '.$data['barangay'].', '.$data['municipality'].', Bantayan Island, Cebu'.($data['address_details'] ? ' - '.$data['address_details'] : '');
        $orders = DB::transaction(function () use ($user, $data, $address) {
            $items = Cart::where('userid', $user->id)->lockForUpdate()->get();
            abort_if($items->isEmpty(), 422, 'Your cart is empty.');
            $created = collect();
            foreach ($items as $item) {
                $food = Food::lockForUpdate()->find($item->food_id);
                abort_if(!$food || $food->stock < $item->quantity, 422, $item->title.' is no longer available in this quantity.');
                $created->push(Order::create(['name' => $data['name'], 'email' => $user->email, 'phone' => $data['phone'], 'address' => $address, 'title' => $item->title, 'quantity' => $item->quantity, 'price' => $item->price, 'image' => $item->image, 'delivery_status' => 'In Progress', 'payment_method' => $data['payment_method'], 'payment_status' => $data['payment_method'] === 'Cash on Delivery' ? 'Unpaid' : 'Pending Verification', 'payment_reference' => $data['payment_reference']]));
                $food->decrement('stock', $item->quantity);
                $item->delete();
            }
            $user->update(['phone' => $data['phone'], 'address' => $address]);
            return $created;
        });
        return response()->json(['message' => 'Order placed.', 'orders' => $orders], 201);
    }

    public function orders(Request $request): JsonResponse { return response()->json(['orders' => Order::where('email', $request->user()->email)->latest()->get()]); }

    public function reservations(Request $request): JsonResponse
    {
        return response()->json(['reservations' => Book::where('user_id', $request->user()->id)->latest()->get()]);
    }

    public function createReservation(Request $request, PayMongoService $payMongo): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'], 'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:/^(09[0-9]{9}|\+639[0-9]{9})$/'], 'guest' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date', 'after_or_equal:today'], 'time' => ['required', 'string', 'max:50'],
            'payment_method' => ['required', 'in:GCash,Bank Transfer'],
        ]);
        $user = $request->user();
        $booking = Book::create([
            'user_id' => $user->id, 'first_name' => $data['first_name'], 'last_name' => $data['last_name'],
            'name' => trim($data['first_name'].' '.$data['last_name']), 'email' => $user->email, 'phone' => str_replace('+63', '0', $data['phone']),
            'guest' => $data['guest'], 'date' => $data['date'], 'time' => $data['time'], 'reservation_price' => 250,
            'deposit_amount' => 125, 'payment_method' => $data['payment_method'], 'payment_status' => 'Pending', 'status' => 'Awaiting Payment',
        ]);
        $booking->update(['gcash_reference' => 'BK-'.str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT)]);
        try {
            $checkout = $payMongo->createCheckout($booking);
            $booking->update(['paymongo_checkout_id' => data_get($checkout, 'id')]);
            return response()->json(['message' => 'Continue payment in your browser.', 'reservation' => $booking->fresh(), 'checkout_url' => data_get($checkout, 'attributes.checkout_url')], 201);
        } catch (\Throwable $exception) {
            report($exception); $booking->delete();
            return response()->json(['message' => 'Secure payment could not be started. Please try again.'], 422);
        }
    }

    public function staffDashboard(Request $request): JsonResponse
    {
        $this->staff($request->user());
        return response()->json(['pending_orders' => Order::where('delivery_status', 'In Progress')->count(), 'on_the_way_orders' => Order::where('delivery_status', 'On The Way')->count(), 'delivered_orders' => Order::where('delivery_status', 'Delivered')->count(), 'low_stock' => Food::where('stock', '<=', config('services.low_stock.threshold', 5))->count()]);
    }

    public function staffOrders(Request $request): JsonResponse { $this->staff($request->user()); return response()->json(['orders' => Order::latest()->limit(100)->get()]); }

    public function updateOrder(Request $request, Order $order): JsonResponse
    {
        $this->staff($request->user());
        $data = $request->validate(['delivery_status' => ['required', 'in:In Progress,On The Way,Delivered,Canceled'], 'payment_status' => ['nullable', 'in:Unpaid,Pending Verification,Paid']]);
        $order->update($data);
        return response()->json(['order' => $order->fresh()]);
    }

    public function inventory(Request $request): JsonResponse { $this->staff($request->user()); return $this->foods(); }

    public function updateStock(Request $request, Food $food): JsonResponse
    {
        $this->staff($request->user());
        abort_unless($request->user()->usertype === 'admin', 403, 'Only administrators can edit inventory.');
        $data = $request->validate(['stock' => ['required', 'integer', 'min:0']]);
        $food->update($data);
        return response()->json(['food' => $this->food($food->fresh())]);
    }

    private function staff(User $user): void { abort_unless(in_array($user->usertype, ['admin', 'staff'], true), 403, 'Staff access required.'); }
    private function price($value): float { return (float) preg_replace('/[^0-9.]/', '', (string) $value); }
    private function food(Food $food): array { return ['id' => $food->id, 'title' => $food->title, 'detail' => $food->detail, 'price' => $this->price($food->price), 'stock' => (int) $food->stock, 'image' => $food->image]; }
    private function user(User $user): array { return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'usertype' => $user->usertype, 'staff_role' => $user->staff_role]; }
}
