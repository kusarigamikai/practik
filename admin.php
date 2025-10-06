<?php
// Простая админ-панель для просмотра записей
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "beauty_salon";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение всех записей
$sql = "SELECT * FROM appointments ORDER BY appointment_date, appointment_time";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Салон красоты</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .admin-table th, .admin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .admin-table th {
            background-color: #667eea;
            color: white;
        }
        .admin-table tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Админ-панель салона красоты</h1>
            <p>Управление записями</p>
        </header>

        <div class="booking-form">
            <h2>Все записи</h2>
            <?php if ($result->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Телефон</th>
                            <th>Услуга</th>
                            <th>Дата</th>
                            <th>Время</th>
                            <th>Создано</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['client_name']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['service']; ?></td>
                            <td><?php echo $row['appointment_date']; ?></td>
                            <td><?php echo $row['appointment_time']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Записей пока нет.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>