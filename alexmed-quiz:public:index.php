<?php require_once __DIR__ . '/../includes/db.php'; ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AlexMed Quiz Bank</title>
  <link rel="stylesheet" href="./styles.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>AlexMed Quiz Bank</h1>
      <p class="notice">اختر سنتك الدراسية للبدء.</p>
      <div class="grid">
        <?php for ($y=1; $y<=6; $y++): ?>
          <a class="btn" href="semester.php?year=<?= $y ?>">السنة <?= $y ?></a>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</body>
</html>