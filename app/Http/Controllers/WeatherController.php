<?php

namespace App\Http\Controllers;

use App\Exceptions\CityNotFoundException;
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
        $city = $weatherRequest->validated('city');
        try {
            $weatherData = $this->getWeatherService->getWeather($city);
            return response()->json($weatherData);
        } catch (CityNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}

