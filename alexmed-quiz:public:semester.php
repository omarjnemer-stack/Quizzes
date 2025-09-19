<?php require_once __DIR__ . '/../includes/db.php';
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
if ($year < 1 || $year > 6) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>اختيار الفصل</title>
  <link rel="stylesheet" href="./styles.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h2>السنة <?= $year ?></h2>
      <p class="notice">اختر الفصل:</p>
      <div class="grid">
        <a class="btn" href="courses.php?year=<?= $year ?>&sem=1">الفصل الأول</a>
        <a class="btn" href="courses.php?year=<?= $year ?>&sem=2">الفصل الثاني</a>
      </div>
      <p class="notice"><a class="btn" href="index.php">↩ العودة</a></p>
    </div>
  </div>
</body>
</html>