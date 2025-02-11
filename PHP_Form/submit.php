<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pship";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone']; 

// Валидация на сервере
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}
if (!preg_match("/^[0-9]{10,}$/", $phone)) { 
    die("Invalid phone number");
}

// Проверка на существование записи за последние 5 минут

$sql = "SELECT * FROM requests WHERE name = '$name' AND email = '$email' AND phone = '$phone' AND created_at >= NOW() - INTERVAL 5 MINUTE";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    die("Вы уже отправляли заявку в последние 5 минут.");
} else {

    // Вставка новой записи
    $stmt = $conn->prepare("INSERT INTO requests (name, email, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $phone);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Ошибка при отправке заявки.";
    }
    $stmt->close();
}

$conn->close();
?>