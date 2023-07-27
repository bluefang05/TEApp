<?php
include '../config/database.php';


// Obtenemos el objeto PDO
$pdo = getPDO();

// Obtenemos el parámetro de la consulta 'count' de la solicitud, o usamos un valor predeterminado de 50 si no está presente
$count = isset($_GET['count']) ? (int) $_GET['count'] : 50;

// Preparamos y ejecutamos la consulta para obtener todas las emociones
$emotion_stmt = $pdo->prepare("SELECT * FROM Emotions");
$emotion_stmt->execute();

// Recuperamos todas las emociones
$emotions = $emotion_stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [];

foreach($emotions as $emotion){
    // Preparamos y ejecutamos la consulta para obtener las imágenes de cada emoción
    $image_stmt = $pdo->prepare("SELECT image_path FROM Images WHERE emotion_id = :emotion_id");
    $image_stmt->execute(['emotion_id' => $emotion['id']]);

    // Recuperamos las imágenes
    $images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($images as $image){
        // Agregamos la imagen y la emoción a la respuesta
        $response[] = [$image['image_path'], $emotion['emotion']];
        
        // Si ya hemos alcanzado la cantidad solicitada de imágenes, salimos del bucle
        if(count($response) >= $count){
            break 2;
        }
    }
}

// Devolvemos las filas como una respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);
