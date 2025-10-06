<?php
// Подключаем конфиг
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Переменные для сообщений
$message = '';
$message_type = '';

// Обработка формы
if ($_POST && isset($_POST['client_name'])) {
    $client_name = trim($_POST['client_name']);
    $phone = trim($_POST['phone']);
    $service = trim($_POST['service']);
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);
    
    // Проверяем обязательные поля
    if (!empty($client_name) && !empty($phone) && !empty($service) && !empty($date) && !empty($time)) {
        // Безопасное добавление в БД
        $stmt = $conn->prepare("INSERT INTO appointments (client_name, phone, service, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $client_name, $phone, $service, $date, $time);
        
        if ($stmt->execute()) {
            $message = "✅ Запись успешно создана!";
            $message_type = 'success';
        } else {
            $message = "❌ Ошибка: " . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    } else {
        $message = "❌ Пожалуйста, заполните все поля";
        $message_type = 'error';
    }
}

// Получаем список услуг
$services = [];
$services_result = $conn->query("SELECT * FROM services WHERE active = 1");
if ($services_result) {
    while ($row = $services_result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Получаем занятые слоты на сегодня
$booked_today = [];
$today = date('Y-m-d');
$booked_result = $conn->query("SELECT appointment_time FROM appointments WHERE appointment_date = '$today'");
if ($booked_result) {
    while ($row = $booked_result->fetch_assoc()) {
        $booked_today[] = $row['appointment_time'];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Салон красоты - Запись</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        header { background: #6c5ce7; color: white; padding: 30px; text-align: center; border-radius: 10px; margin-bottom: 20px; }
        .form-section { background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; font-size: 16px; }
        button { background: #6c5ce7; color: white; padding: 15px; border: none; border-radius: 5px; font-size: 18px; cursor: pointer; width: 100%; }
        button:hover { background: #5b4cdb; }
        .message { padding: 15px; margin: 15px 0; border-radius: 5px; text-align: center; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .schedule-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-top: 15px; }
        .time-slot { padding: 10px; text-align: center; border-radius: 5px; font-weight: bold; }
        .free { background: #d4edda; color: #155724; }
        .booked { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>💇‍♀️ Салон красоты "Элегант"</h1>
            <p>Онлайн запись на услуги</p>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h2>📅 Записаться на услугу</h2>
            <form method="POST">
                <div class="form-group">
                    <label>👤 Ваше имя:</label>
                    <input type="text" name="client_name" required>
                </div>
                
                <div class="form-group">
                    <label>📞 Телефон:</label>
                    <input type="tel" name="phone" placeholder="+79991234567" required>
                </div>
                
                <div class="form-group">
                    <label>💅 Услуга:</label>
                    <select name="service" required>
                        <option value="">Выберите услугу</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo $service['name']; ?>">
                                <?php echo $service['name']; ?> - <?php echo $service['price']; ?> руб.
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>📅 Дата:</label>
                    <input type="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>⏰ Время:</label>
                    <select name="time" required>
                        <option value="">Выберите время</option>
                        <option value="09:00">09:00</option>
                        <option value="10:00">10:00</option>
                        <option value="11:00">11:00</option>
                        <option value="12:00">12:00</option>
                        <option value="13:00">13:00</option>
                        <option value="14:00">14:00</option>
                        <option value="15:00">15:00</option>
                        <option value="16:00">16:00</option>
                        <option value="17:00">17:00</option>
                        <option value="18:00">18:00</option>
                    </select>
                </div>
                
                <button type="submit">💾 Записаться</button>
            </form>
        </div>

        <div class="form-section">
            <h2>🕒 Расписание на сегодня (<?php echo date('d.m.Y'); ?>)</h2>
            <div class="schedule-grid">
                <?php
                $time_slots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
                foreach ($time_slots as $slot) {
                    $is_booked = in_array($slot, $booked_today);
                    echo '<div class="time-slot ' . ($is_booked ? 'booked' : 'free') . '">';
                    echo $slot . '<br>' . ($is_booked ? '❌ Занято' : '✅ Свободно');
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Простая валидация даты
        const dateInput = document.querySelector('input[name="date"]');
        const timeSelect = document.querySelector('select[name="time"]');
        
        dateInput.addEventListener('change', function() {
            if (this.value) {
                timeSelect.disabled = false;
            } else {
                timeSelect.disabled = true;
            }
        });
    </script>
</body>
</html>
<?php
// Закрываем соединение
$conn->close();
?>