<?php require_once __DIR__ . '/../includes/db.php';
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$sem  = isset($_GET['sem'])  ? (int)$_GET['sem']  : 0;
if ($year < 1 || $year > 6 || ($sem !== 1 && $sem !== 2)) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare('SELECT id, name FROM courses WHERE year=? AND semester=? ORDER BY name');
$stmt->execute([$year, $sem]);
$courses = $stmt->fetchAll();
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>المواد</title>
  <link rel="stylesheet" href="./styles.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h2>مواد السنة <?= $year ?> — الفصل <?= $sem === 1 ? 'الأول' : 'الثاني' ?></h2>
      <?php if (!$courses): ?>
        <p class="notice">لا توجد مواد مُسجّلة بعد. اطلب من الأدمن إضافتها.</p>
      <?php else: ?>
        <div class="grid">
          <?php foreach ($courses as $c): ?>
            <a class="btn" href="quiz.php?course=<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <hr>
      <a class="btn" href="semester.php?year=<?= $year ?>">↩ العودة لاختيار الفصل</a>
    </div>
  </div>
</body>
</html>