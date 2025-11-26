<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $endpoint;
    protected string $token;
    protected string $defaultCountry;

    public function __construct()
    {
        $this->endpoint = config('services.fonnte.endpoint') ?? env('FONNTE_ENDPOINT');
        $this->token = config('services.fonnte.token') ?? env('FONNTE_TOKEN');
        $this->defaultCountry = config('services.fonnte.country') ?? env('FONNTE_DEFAULT_COUNTRY', '62');
    }

    /**
     * Kirim pesan WA.
     *
     * @param string $target Nomor tujuan (bisa tanpa kode negara)
     * @param string $message Isi pesan
     * @param array $opts Optional: delay, countryCode, dll
     * @return array|null Response array on success, null on failure
     */
    public function send(string $target, string $message, array $opts = []): ?array
    {
        // normalize nomor: hanya digit
        $targetClean = preg_replace('/\D+/', '', $target);

        if (strlen($targetClean) === 0) {
            Log::warning('WhatsAppService: target number is empty after cleaning', ['target_original' => $target]);
            return null;
        }

        // if no country code, prepend default
        if (!preg_match('/^(\+)?[1-9][0-9]{6,}$/', $targetClean)) {
            // If length suggests local number (e.g. starts with 8 for Indonesia), prepend country
            if (substr($targetClean, 0, 1) === '0') {
                $targetClean = ltrim($targetClean, '0');
            }
            // if still looks local (e.g. 8xxx...), add country
            if (strlen($targetClean) <= 12 && strlen($this->defaultCountry) > 0 && strpos($targetClean, $this->defaultCountry) !== 0) {
                $targetClean = $this->defaultCountry . $targetClean;
            }
        }

        $payload = array_merge([
            'target' => $targetClean,
            'message' => $message,
            'delay' => '5-10', // contoh kalau mau delay
            'countryCode' => $this->defaultCountry,
        ], $opts);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Accept' => 'application/json',
            ])->post($this->endpoint, $payload);

            $body = $response->json();

            if ($response->successful()) {
                Log::info('WhatsAppService: message sent', ['target' => $targetClean, 'payload' => $payload, 'response' => $body]);
                return $body;
            }

            // tidak sukses
            Log::error('WhatsAppService: failed to send', [
                'target' => $targetClean,
                'payload' => $payload,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return $body ?: null;
        } catch (\Throwable $e) {
            Log::error('WhatsAppService: exception sending message', [
                'target' => $targetClean,
                'message' => $message,
                'exception' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
