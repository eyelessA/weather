<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise;

class getWeatherService
{
    public function getWeather(array $data, $apiKey): array
    {
        $client = new Client();
        $geoUrl = "https://api.openweathermap.org/geo/1.0/direct?q={$data['city']}&limit=5&appid={$apiKey}";
        $geoResponse = Http::get($geoUrl);

        $promises = [];
        $weatherData = [];

        if ($geoResponse->successful()) {
            $geoData = $geoResponse->json();

            foreach ($geoData as $geo) {
                $lat = $geo['lat'] ?? null;
                $lon = $geo['lon'] ?? null;

                if ($lat && $lon) {
                    $promises[] = $client->getAsync("https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}");
                }
            }
            $responses = Promise\Utils::settle($promises)->wait();

            foreach ($responses as $response) {
                if ($response['state'] === 'fulfilled') {
                    $weatherResponse = json_decode($response['value']->getBody()->getContents(), true);
                    $weatherData[] = $weatherResponse;
                }
            }
        }
        return $weatherData;
    }
}
