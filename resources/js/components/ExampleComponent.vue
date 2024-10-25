<template>
    <div class="container flex justify-center items-start min-h-screen mt-16">
        <div class="w-full max-w-sm min-w-[200px]">
            <div class="w-full">
                <div class="flex flex-col">
                    <button @click="toggleUnits"
                            class="text-left mb-2 text-indigo-600">
                        {{ isKelvin ? 'Switch to Celsius' : 'Switch to Kelvin' }}
                    </button>
                    <div class="relative flex items-center">
                        <div class="w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="absolute w-5 h-5 top-2.5 left-2.5 text-slate-600">
                                <path fill-rule="evenodd"
                                      d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <input v-model="city"
                                   class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md pl-10 pr-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                                   placeholder="Search city"
                            />
                            <div
                                v-if="cities.length > 1 && this.selectedCity === null"
                                class="absolute left-0 z-10 mt-2 w-full origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                <div class="py-1" role="none">
                                    <!-- Вывод списка городов с помощью v-for -->
                                    <a
                                        v-for="(key, value) in cities"
                                        href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        role="menuitem"
                                        @click="selectCity(key)"
                                    >
                                        {{ key.name }},
                                        {{ key.sys.country }}
                                        {{
                                            isKelvin ? (convertTemperature(key.main.temp)).toFixed(0) + ' K' : (convertTemperature(key.main.temp)).toFixed(0) + ' °C'
                                        }}
                                        <div style="font-size: 12px;">
                                            {{ (key.coord.lat).toFixed(3) }}
                                            {{ (key.coord.lon).toFixed(3) }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button @click.prevent="validateCity()"
                                class="rounded-md bg-slate-800 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
                                type="button">
                            Search
                        </button>
                    </div>
                </div>

            </div>
            <p v-if="this.error" class="text-red-500 text-sm mt-2">{{ this.error }}</p>

            <div>
                <h1 class="ml-2 mt-5 whitespace-nowrap">
                    Recent Searches:
                </h1>
                <div v-for="(key, value) in recentSearches" class="mt-2 ml-3">
                    <ul class="list-disc ml-5">
                        <li class="text-sm font-normal">{{ key }}</li>
                    </ul>
                </div>
            </div>

            <div v-if="selectedCity"
            class="mt-5 w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md py-2 shadow-sm">
            <div class="bg-white p-7 rounded-md">
                            <div>
                                <h2 class="text-2xl font-bold mb-3">{{ selectedCity.name }}</h2>

                                <p>Country: {{ selectedCity.sys.country }}</p>
                                <p>
                                    Temperature:
                                    {{
                                        isKelvin ? (convertTemperature(selectedCity.main.temp)).toFixed(0) + ' K' : (convertTemperature(selectedCity.main.temp)).toFixed(0) + ' °C'
                                    }}
                                </p>
                                <p>Coordinates: {{ (selectedCity.coord.lat).toFixed(3) }}, {{
                                        (selectedCity.coord.lon).toFixed(3)
                                    }}</p>
                                <p>Wind Speed: {{ (selectedCity.wind.speed + 'm/s') }}</p>
                                <p>Weather Conditions: {{ (selectedCity.weather[0].description) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        console.log('Component mounted.')
    },

    data() {
        return {
            city: null,
            cities: [{}],
            selectedCity: null,
            isKelvin: true,
            error: null,
            recentSearches: JSON.parse(localStorage.getItem('recentSearches')) || [],
            lastSearch: localStorage.getItem('lastSearch') || null
        }
    },

    methods: {
        validateCity() {
            if (this.city === null || this.city.trim() === '') {
                this.error = 'The ‘Search city’ field cannot be empty.'; // Выводим сообщение об ошибке
            } else if (!/^[A-Za-zА-Яа-яЁё\s]+$/.test(this.city)) {
                this.error = 'The city must contain only letters';
            } else {
                this.error = null; // Очищаем ошибку, если поле заполнено
                this.getWeather(); // Если поле заполнено, отправляем запрос
            }
        },

        getWeather() {
            this.selectedCity = null
            axios.post('/api/get-weather', {city: this.city})
                .then(res => {
                    for (let i = 0; i < (res.data.length); i++) {
                        this.cities = res.data
                    }
                    this.addRecentSearches(this.city)
                })
                .catch(error => {
                    this.error = error.response.data.message;
                });
        },

        selectCity(city) {
            this.selectedCity = city
        },

        toggleUnits() {
            this.isKelvin = !this.isKelvin;
        },
        convertTemperature(tempInKelvin) {
            if (!this.isKelvin) {
                return (tempInKelvin - 273.15)// Конвертируем в Цельсий
            } else {
                return (tempInKelvin) // Конвертируем в Кельвин
            }
        },
        addRecentSearches(city) {
            this.recentSearches.push(city);
            this.lastSearch = city;

            // Сохраняем массив и последний поиск в localStorage
            localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
            localStorage.setItem('lastSearch', city);

            if (this.recentSearches.length > 5) {
                this.recentSearches.shift(); // Удаляем первый элемент, если длина массива больше 5
                localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches)); // Обновляем данные в localStorage
            }
        }
    }
}
</script>

