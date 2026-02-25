<?php

namespace App\Services;

use Illuminate\Support\Str;

class NicknameGeneratorService
{
    public function generate(): string
    {
        if (!config('services.pokeapi.enabled')){
            return Str::random(8);
        }

        return '';
    }
}
