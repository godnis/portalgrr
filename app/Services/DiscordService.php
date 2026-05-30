<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiscordService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.discord.url');
        $this->token = config('services.discord.token');
    }

    public function enviar(string $rota, array $data): array
    {
        $response = Http::withToken($this->token)
            ->post($this->baseUrl . $rota, $data );

        if ($response->successful()) {
            return $response->json();
        }

        // padroniza erro
        return [
            'error' => true,
            'status' => $response->status(),
            'body' => $response->body()
        ];
    }
}