<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\EmailSignature;
use App\Services\VerificationService;
use App\Notifications\SignupVerifyEmailNotification;
use App\Http\Requests\VerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

/**
 * Customer sign-up and account removal from the storefront. Verifying the link (GET /verify) and
 * creating the account (POST /account-setup) use the starter's endpoints.
 */
class AccountController extends Controller
{
    /**
     * Same as the starter's POST /verification, but the link opens the storefront.
     */
    public function register(VerificationRequest $request): JsonResponse
    {
        $email = $request->input('email');

        EmailSignature::where('email', $email)->whereNull('verified_at')->delete();

        $signedUrl = VerificationService::generateSignedUrl(
            '/register/verify',
            $email,
            30,
            ['email' => $email],
            config('shop.storefront_url'),
        );

        Notification::route('mail', $email)->notify(new SignupVerifyEmailNotification($signedUrl));

        return response()->json(['message' => 'Verification email sent']);
    }

    /**
     * Customers can close their own account. Past orders are kept (without the link to the user)
     * for the store's records.
     */
    public function destroy(Request $request): Response
    {
        $user = $request->user();
        $request->validate(['password' => ['required', 'string']]);

        if (!$user->isCustomer()) {
            abort(403, 'Staff accounts are removed by an administrator.');
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages(['password' => 'The password is incorrect.']);
        }

        // Log out first: logging out saves the user (remember token), which would re-create it
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        DB::transaction(function () use ($user) {
            $user->roles()->detach();
            $user->abilities()->detach();
            $user->verificationCodes()->delete();
            $user->delete();
        });

        return response()->noContent();
    }
}
