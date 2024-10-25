<?php

namespace App\Http\Controllers;

use App\Http\Requests\Weather\WeatherRequest;
use App\Services\getWeatherService;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    private getWeatherService $getWeatherService;

    public function __construct(getWeatherService $getWeatherService)
    {
        $this->getWeatherService = $getWeatherService;
    }

    public function getWeather(WeatherRequest $weatherRequest): JsonResponse
    {
        $data = $weatherRequest->validated();
        $apiKey = env('OPENWEATHER_API_KEY');

        $weatherData = $this->getWeatherService->getWeather($data, $apiKey);

        if (empty($weatherData)) {
            return response()->json([
                'error' => 'city not found'
            ]);
        } else {
            return response()->json($weatherData);
        }
    }
}

