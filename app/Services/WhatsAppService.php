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
     * Kirim pesan WA ke satu nomor atau beberapa nomor.
     *
     * @param string|array $target Nomor tujuan (string) atau array of nomor.
     * @param string $message Isi pesan
     * @param array $opts Optional: delay, countryCode, dll
     * @return array|null Jika kirim ke banyak nomor -> array hasil per nomor. Jika single -> response array|null
     */
    public function send(string|array $target, string $message, array $opts = []): ?array
    {
        // jika array, kirim satu-per-satu dan koleksi hasil
        if (is_array($target)) {
            $results = [];
            foreach ($target as $t) {
                $results[] = $this->sendSingle(trim($t), $message, $opts);
            }
            return $results;
        }

        // single
        return $this->sendSingle($target, $message, $opts);
    }

    /**
     * Kirim ke single nomor, internal.
     */
    protected function sendSingle(string $target, string $message, array $opts = []): ?array
    {
        // normalize nomor: hanya digit
        $targetClean = preg_replace('/\D+/', '', $target);

        if (strlen($targetClean) === 0) {
            Log::warning('WhatsAppService: target number is empty after cleaning', ['target_original' => $target]);
            return null;
        }

        // Jika mulai dengan '0', hapus nol dan tambahkan country code nanti
        if (substr($targetClean, 0, 1) === '0') {
            $targetClean = ltrim($targetClean, '0');
        }

        // Tambahkan default country jika belum ada
        if ($this->defaultCountry && strpos($targetClean, $this->defaultCountry) !== 0) {
            // jika nomor sangat panjang dan sudah punya country, biarkan
            $targetClean = $this->defaultCountry . $targetClean;
        }

        $payload = array_merge([
            'target' => $targetClean,
            'message' => $message,
            // default delay, bisa ditimpa oleh $opts
            'delay' => '5-10',
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
