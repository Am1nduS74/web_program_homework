<?php

header("Content-Type: text/html; charset=UTF-8"); // Set the content type to HTML with UTF-8 encoding
require __DIR__ . '/../backend/utils/db.php'; // Include the database connection file

try {
    $course_id = isset($_GET['course_id']) ? (int) $_GET['course_id'] : 0; // Get the course ID from the URL, if not set default will be 0

    // get course content
    global $db;
    $min_id = $course_id . '.00'; // Minimum ID for the course content
    $max_id = $course_id . '.99'; // Maximum ID

    // This prepares thee sql statement to get the course content
    $stmt = $db->prepare(" 
        SELECT id, french, english 
        FROM contents 
        WHERE CAST(id AS FLOAT) BETWEEN :min AND :max
        ORDER BY CAST(id AS FLOAT)
    ");

    $stmt->execute([':min' => $min_id, ':max' => $max_id]); 
    $verses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($verses)) {
        throw new Exception("Can not find course", 404);
    }

} catch (Exception $e) {
    http_response_code($e->getCode());
    die($e->getMessage());
}

$prev_chapter = $course_id - 1;
$next_chapter = $course_id + 1;

if ($prev_chapter % 7 == 0) {
    $prev_chapter--;
}

if ($next_chapter % 7 == 0) {
    $next_chapter++;
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Chapter <?= $course_id ?> - Assimil French</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link style="text/css" rel="stylesheet" href="css/global.css">
    <link style="text/css" rel="stylesheet" href="css/content.css">

    <style> .chapter { margin: 2rem 5rem; } </style>
</head>

<body>


    <div id="content-container" >
        <div class="nav-container">
            <?php if ($course_id > 1): ?>
                <a  href="content.php?course_id=<?= $prev_chapter ?>"><<</a>
            <?php else: ?>
                <span style="color: #bdc3c7;"><<</span>
            <?php endif; ?>

            <a href="/" style="text-decoration: none; display: block; flex-grow: 1;"> 
                <h1 style="text-align: center; color: #2c3e50; font-weight: 300; font-size: 60px;margin: 0; cursor: 
                           pointer; transition: color 0.2s ease;"> Assimil French Home</h1>
            </a>

            <?php if ($course_id < 21): ?> <
                <a href="content.php?course_id=<?= $next_chapter ?>">>></a>
            <?php else: ?>
                <span style="color: #bdc3c7;">>></span>
            <?php endif; ?>
        </div>

        <div class="chapter">
            <div class="chapter-header">
                <h2>Chapter <?= $course_id ?></h2>
                <button id="play-<?= $course_id ?>" class="play-btn" onclick="playChapter(<?= $course_id ?>)"></button> <!-- Play button for the chapter -->
            </div>

            <div id="purchase-prompt" class="hidden"> <!-- This will be only show if the chapter is not purchased -->
                <div id="validation-loading">Checking access...</div>
                <h3>This course needs to be purchased before viewing</h3>
                <p>You have not purchased the content of Chapter <?= $course_id ?></p>
                <button class="purchase-btn" onclick="window.location.href = 'purchase.php';">Purchase Now!</button>
            </div>
            <div id="content-body" class="hidden">
            <?php foreach ($verses as $verse): ?> <!-- Loop through each verse in the course content -->
                <div class="verse">
                    <span class="verse-id">§<?= htmlspecialchars($verse['id']) ?></span> 
                    <div class="text-container"> 
                        <span class="french-text"><?= htmlspecialchars($verse['french']) ?></span>
                        <span class="english-text"><?= htmlspecialchars($verse['english']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="scripts/utils.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded', () => { // Wait for the DOM to load first before executing the script
            const courseId = <?= $course_id ?>;
            const loading = document.getElementById('validation-loading'); 
            const contentContainer = document.getElementById('content-body');
            const purchasePrompt = document.getElementById('purchase-prompt');
            const purchaseBtn = document.querySelector('.purchase-btn');

            validatePurchase(courseId) // This function check if the course is purchased or not
                    .then(valid => {
                        loading.classList.add('hidden');
                        if (valid) {
                            contentContainer.classList.remove('hidden');
                        } else {
                            purchasePrompt.classList.remove('hidden');
                            const token = localStorage.getItem('token');
                            page = !token ? 'login.php' : 'purchase.php'; 
                            purchaseBtn.onclick = () => window.location.href = page; // Redirect to purchase page if not purchased
                        }
                    })
                    .catch(error => {
                        console.error('fail:', error);
                        loading.innerHTML = 'Something Wrong <a href="javascript:location.reload()">Retry</a>';
                    });
    });


        async function validatePurchase(courseId) { // This function checks if the course is purchased or not
            try {
                const headers = {'Content-Type': 'application/json'}; 
                const token = localStorage.getItem('token');
                if (token) headers['Authorization'] = `Bearer ${token}`;
                const response = await fetch('/api/purchase/validate', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({ course_id: courseId })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                return data.valid;
            } catch (error) {
                throw new Error('failed: ' + error.message);
            }
        }

    </script>
</body>

</html>