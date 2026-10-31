<?php

namespace App\Services\Shop;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\Shop\NewOrderNotification;
use App\Notifications\Shop\OrderPlacedNotification;
use App\Support\Enums\PaymentMethod;
use App\Support\Enums\ShippingMethod;
use App\Support\Enums\UserAbility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        protected CartPricer $pricer,
        protected RazorpayGateway $razorpay,
    ) {
    }

    /**
     * Turn a cart into an order: re-price it, reserve stock and count the coupon use, all in one
     * transaction so two customers can't buy the last item twice.
     *
     * @param  array  $data  validated CheckoutRequest data
     */
    public function placeOrder(User $user, array $data): Order
    {
        $paymentMethod = PaymentMethod::from($data['payment_method']);
        $shippingMethod = ShippingMethod::from($data['shipping_method']);
        $shippingAddress = $this->shippingAddress($user, $data);

        if ($paymentMethod === PaymentMethod::Razorpay && !$this->razorpay->isConfigured()) {
            throw ValidationException::withMessages(['payment_method' => 'Online payment is not available right now. Please choose Cash on Delivery.']);
        }

        $order = DB::transaction(function () use ($user, $data, $paymentMethod, $shippingMethod, $shippingAddress) {
            $products = Product::query()
                ->with('images', 'category')
                ->whereIn('id', collect($data['items'])->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $quote = $this->pricer->price(
                $data['items'],
                $shippingMethod,
                $data['coupon_code'] ?? null,
                $user,
                strictCoupon: true,
                lockedProducts: $products,
            );

            if ($paymentMethod === PaymentMethod::CashOnDelivery) {
                $this->assertCodAllowed($quote->total);
            }

            $order = Order::create([
                'number' => 'pending-'.bin2hex(random_bytes(8)),
                'user_id' => $user->id,
                'email' => $user->email,
                'payment_method' => $paymentMethod,
                'shipping_method' => $shippingMethod,
                'shipping_address' => $shippingAddress,
                'coupon_id' => $quote->coupon?->id,
                'coupon_code' => $quote->coupon?->code,
                'subtotal' => $quote->subtotal,
                'discount' => $quote->discount,
                'shipping' => $quote->shipping,
                'total' => $quote->total,
                'currency' => config('shop.currency'),
                'customer_note' => $data['note'] ?? null,
            ]);

            // Readable, sequential order numbers: LFC-100001, LFC-100002...
            $order->update(['number' => sprintf('%s-%d', config('shop.order_prefix'), 100000 + $order->id)]);

            foreach ($quote->lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product_id'],
                    'name' => $line['name'],
                    'sku' => $line['sku'],
                    'image_url' => $line['image_url'],
                    'options' => $line['options'],
                    'price' => $line['price'],
                    'quantity' => $line['quantity'],
                    'total' => $line['total'],
                ]);

                $products[$line['product_id']]->decrement('stock', $line['quantity']);
            }

            if ($quote->coupon) {
                Coupon::whereKey($quote->coupon->id)->increment('used_count');
            }

            $order->record('placed');

            return $order;
        });

        if ($paymentMethod === PaymentMethod::Razorpay) {
            try {
                $order->update(['razorpay_order_id' => $this->razorpay->createOrder((float) $order->total, $order->number)]);
            } catch (\RuntimeException $e) {
                app(OrderManager::class)->cancel($order, 'Payment could not be started', notify: false);

                throw ValidationException::withMessages(['payment_method' => $e->getMessage()]);
            }
        } else {
            $this->sendOrderPlacedNotifications($order);
        }

        return $order->load('items.product:id,slug', 'events');
    }

    public function sendOrderPlacedNotifications(Order $order): void
    {
        $order->user?->notify(new OrderPlacedNotification($order));

        $staff = User::query()->enabled()->get()->filter(fn (User $user) => $user->can(UserAbility::ManageOrders->value));
        Notification::send($staff, new NewOrderNotification($order));
    }

    protected function shippingAddress(User $user, array $data): array
    {
        if (!empty($data['address_id'])) {
            $address = $user->addresses()->whereKey($data['address_id'])->first();

            if (!$address) {
                throw ValidationException::withMessages(['address_id' => 'Choose one of your saved addresses.']);
            }

            return $address->toShippingAddress();
        }

        $address = new Address($data['address']);

        if (!empty($data['save_address'])) {
            $user->addresses()->create([
                ...$data['address'],
                'label' => $data['address']['label'] ?? 'Home',
                'is_default' => !$user->addresses()->exists(),
            ]);
        }

        return $address->toShippingAddress();
    }

    protected function assertCodAllowed(float $total): void
    {
        if (!Setting::value('cod_enabled')) {
            throw ValidationException::withMessages(['payment_method' => 'Cash on Delivery is not available.']);
        }

        $max = Setting::value('cod_max_order_total');

        if ($max && $total > (float) $max) {
            throw ValidationException::withMessages([
                'payment_method' => sprintf('Cash on Delivery is available for orders up to ₹%s. Please pay online.', number_format((float) $max)),
            ]);
        }
    }
}
