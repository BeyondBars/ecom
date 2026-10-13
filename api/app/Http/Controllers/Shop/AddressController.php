<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\SaveAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return AddressResource::collection($request->user()->addresses()->get());
    }

    public function store(SaveAddressRequest $request): JsonResponse
    {
        $user = $request->user();
        $makeDefault = $request->boolean('is_default') || !$user->addresses()->exists();

        $address = DB::transaction(function () use ($user, $request, $makeDefault) {
            if ($makeDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            return $user->addresses()->create([...$request->validated(), 'is_default' => $makeDefault]);
        });

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function update(SaveAddressRequest $request, Address $address): AddressResource
    {
        DB::transaction(function () use ($request, $address) {
            if ($request->boolean('is_default')) {
                $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            }

            $address->update($request->validated());
        });

        return new AddressResource($address);
    }

    public function destroy(Request $request, Address $address): Response
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        DB::transaction(function () use ($request, $address) {
            $address->delete();

            // Keep one default address when the default is removed
            $addresses = $request->user()->addresses();
            if (!$addresses->clone()->where('is_default', true)->exists()) {
                $addresses->clone()->first()?->update(['is_default' => true]);
            }
        });

        return response()->noContent();
    }
}
