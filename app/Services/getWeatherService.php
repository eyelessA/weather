<?php

namespace App\Services;

use App\Exceptions\CityFetchException;
use App\Exceptions\CityNotFoundException;
use App\Exceptions\WeatherFetchException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise;

readonly class getWeatherService
{
    private Client $client;
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.open-weather.api_key');
        $this->client = new Client();
    }

    /**
     * Получает погоду для каждого из найденых городов
     *
     * @throws CityNotFoundException
     * @throws CityFetchException
     * @throws WeatherFetchException
     * @throws GuzzleException
     */
    public function getWeather(string $city): array
    {
        $promises = [];
        $weatherData = [];
        foreach ($this->getCities($city) as $cityData) {
            if (!isset($cityData['lat'], $cityData['lon'])) {
                throw new CityFetchException("City data could not be loaded. Please try again later.");
            }

            $lat = $cityData['lat'];
            $lon = $cityData['lon'];

            $promises[] = $this->client->getAsync("https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$this->apiKey}");
        }
        $getWeatherResponses = Promise\Utils::settle($promises)->wait();

        foreach ($getWeatherResponses as $response) {
            if ($response['state'] === 'fulfilled') {
                $weatherResponse = json_decode($response['value']->getBody()->getContents(), true);
                $weatherData[] = $weatherResponse;
            } else {
                throw new WeatherFetchException('Weather data could not be loaded. Please try again later.');
            }
        }

        return $weatherData;
    }

    /**
     * Получает города по названию
     *
     * @param string $city
     * @return array
     * @throws CityFetchException
     * @throws CityNotFoundException
     * @throws GuzzleException
     */
    public function getCities(string $city): array
    {
        $getCitiesUrl = "https://api.openweathermap.org/geo/1.0/direct?q={$city}&limit=5&appid={$this->apiKey}";
        $getCitiesResponse = $this->client->get($getCitiesUrl);

        if ($getCitiesResponse->getStatusCode() != 200) {
            throw new CityFetchException("City data could not be loaded. Please try again later.");
        }

        $citiesData = json_decode( $getCitiesResponse->getBody()->getContents(), true);

        if (empty($citiesData)) {
            throw new CityNotFoundException("City not found.");
        }

        return $citiesData;
    }
}
