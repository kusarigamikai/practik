<?php
require_once 'config.php';

header('Content-Type: application/json');

if (isset($_GET['date'])) {
    $date = $conn->real_escape_string($_GET['date']);
    
    // Все возможные временные слоты
    $all_slots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
    
    // Получаем занятые слоты на выбранную дату
    $sql = "SELECT appointment_time FROM appointments WHERE appointment_date = ? AND status != 'cancelled'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booked_slots = [];
    while ($row = $result->fetch_assoc()) {
        $booked_slots[] = $row['appointment_time'];
    }
    $stmt->close();
    
    // Фильтруем доступные слоты
    $available_slots = array_diff($all_slots, $booked_slots);
    
    echo json_encode([
        'date' => $date,
        'availableSlots' => array_values($available_slots),
        'bookedSlots' => $booked_slots
    ]);
} else {
    echo json_encode(['error' => 'Date not specified']);
}

$conn->close();
?>