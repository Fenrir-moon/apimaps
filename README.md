# apimaps
модуль битрикс для вывода карты подразделений

установка как обычного модуля
копируем содержимое архива в папку modules и устанавливаем средствами битрикс.

на нужной встранице вставляем этот код.
<?php
$APPLICATION->IncludeComponent(
    "nirvana:api.maps",
    "",
    array()
);
?>

при необходимости вводим свой ключ к апи яндекс карт в настройках модуля(указан мой как дефолтный)
