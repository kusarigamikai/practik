<?php
// config.php
$host = 'localhost';
$user = 'root'; // ваше имя пользователя БД
$password = ''; // ваш пароль БД
$database = 'beauty_salon'; // имя вашей БД

// Подключение к базе данных
$conn = new mysqli($host, $user, $password, $database);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Установка кодировки
$conn->set_charset("utf8");
?>