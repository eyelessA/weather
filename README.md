## Weather

Разработать простое приложение для прогноза погоды, которое позволит пользователям искать города и просматривать текущие погодные условия для выбранного местоположения.

## Требования:
1. Поиск города:
   - Пользователи должны иметь возможность искать город по имени и видеть текущую температуру, погодные условия и скорость ветра для данного местоположения.
2. Единицы измерения:
   - Приложение должно позволять переключаться между единицами
   измерения Цельсия и Фаренгейта. 4. Интеграция с API:
   - Используйте публичный API погоды (например, OpenWeatherMap) для получения текущих погодных данных.
3. Обработка ошибок:
   - Отображайте соответствующие сообщения об ошибках, если API
   возвращает ошибку или запрос пользователя не совпадает с известными
   местоположениями.
4. Функция "недавние поиски":
   - Включите функцию "недавние поиски", которая отображает список из последних пяти городов, которые пользователь искал.
5. Обработка ошибок и валидация:
   - Используйте подходящую обработку ошибок и валидацию, чтобы
   обеспечить надежность и удобство использования приложения.

## Как запустить

- `composer install`
- `npm install`
- `npm run dev`
