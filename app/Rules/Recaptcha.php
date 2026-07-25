<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    /**
     * Verifies the g-recaptcha-response token against Google's siteverify
     * endpoint. Fails closed: any missing config, network error, or
     * unsuccessful verification rejects the request.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret_key');

        if (! $secret) {
            Log::warning('reCAPTCHA secret key is not configured; rejecting submission.');
            $fail('reCAPTCHA is not configured. Please contact the site administrator.');
            return;
        }

        if (! $value) {
            $fail('Please complete the reCAPTCHA verification.');
            return;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secret,
                'response' => $value,
            ]);

            if (! $response->successful() || ! ($response->json('success') === true)) {
                $fail('reCAPTCHA verification failed. Please try again.');
            }
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA verification request failed: ' . $e->getMessage());
            $fail('Could not verify reCAPTCHA right now. Please try again.');
        }
    }
}
