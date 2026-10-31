<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Contact messages, newsletter subscribers and back-in-stock requests.
 */
class InboxController extends Controller
{
    public function messages()
    {
        $this->authorize('viewAny', ContactMessage::class);

        return ContactMessageResource::collection(
            QueryBuilder::for(ContactMessage::class)
                ->allowedFilters(AllowedFilter::exact('status'), AllowedFilter::callback('search', fn ($q, $v) => $q->where(fn ($q) => $q
                    ->where('email', 'like', "%{$v}%")->orWhere('subject', 'like', "%{$v}%")->orWhere('name', 'like', "%{$v}%"))))
                ->defaultSort('-created_at')
                ->allowedSorts('created_at')
                ->paginate(min((int) request('per_page', 15), 100))
        );
    }

    public function updateMessage(Request $request, ContactMessage $message): ContactMessageResource
    {
        $this->authorize('update', $message);
        $message->update($request->validate(['status' => ['required', Rule::in(ContactMessage::STATUSES)]]));

        return new ContactMessageResource($message);
    }

    public function destroyMessage(ContactMessage $message): Response
    {
        $this->authorize('delete', $message);
        $message->delete();

        return response()->noContent();
    }

    public function subscribers(Request $request)
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $query = NewsletterSubscriber::query()
            ->whereNull('unsubscribed_at')
            ->when($request->input('filter.search'), fn ($q, $v) => $q->where('email', 'like', "%{$v}%"))
            ->latest();

        return response()->json($query->paginate(min((int) $request->input('per_page', 25), 100)));
    }

    public function exportSubscribers(): StreamedResponse
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'source', 'subscribed_at']);
            NewsletterSubscriber::whereNull('unsubscribed_at')->orderBy('id')->each(
                fn ($s) => fputcsv($out, [$s->email, $s->source, $s->created_at?->toDateTimeString()])
            );
            fclose($out);
        }, 'newsletter-subscribers.csv', ['Content-Type' => 'text/csv']);
    }

    public function destroySubscriber(NewsletterSubscriber $subscriber): Response
    {
        $this->authorize('delete', $subscriber);
        $subscriber->update(['unsubscribed_at' => now()]);

        return response()->noContent();
    }

    /**
     * Waiting back-in-stock requests per product: shows demand for sold-out items.
     */
    public function stockAlerts()
    {
        $this->authorize('viewAny', \App\Models\Product::class);

        return response()->json(['data' => StockAlert::query()
            ->whereNull('notified_at')
            ->selectRaw('product_id, count(*) as waiting, max(created_at) as latest_request')
            ->groupBy('product_id')
            ->with('product:id,name,slug,stock')
            ->orderByDesc('waiting')
            ->get()]);
    }
}
