<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NicknameGeneratorService
{
    public function generate(): string
    {
        if (!config('services.pokeapi.enabled')){
            return Str::random(8);
        }

        try {
            $id = random_int(1, 898);
            $base_url = config('services.pokeapi.base_url');
            $response = Http::timeout(3)->get("$base_url/pokemon/$id");

            if ($response->failed()) {
                return Str::random(8);
            }

            $name = data_get($response->json(), 'name');

            return $name ? (string) $name : Str::random(8);

        } catch (\Throwable $e) {
            Log::warning('Nickname API fallback activated', [
                'provider' => 'pokeapi',
                'pokemon_id' => $id ?? null,
                'base_url' => $base_url ?? null,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
            ]);
            return Str::random(8);
        }
    }
}
