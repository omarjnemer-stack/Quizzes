<?php require_once __DIR__ . '/../includes/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$msg = '';
// إضافة مادة
if (isset($_POST['action']) && $_POST['action']==='add_course') {
    $year = (int)($_POST['year'] ?? 0);
    $sem  = (int)($_POST['semester'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    if ($year>=1 && $year<=6 && ($sem===1||$sem===2) && $name!=='') {
        $stmt=$pdo->prepare('INSERT INTO courses (year, semester, name) VALUES (?,?,?)');
        try{ $stmt->execute([$year,$sem,$name]); $msg='تمت إضافة المادة.'; }
        catch(Exception $e){ $msg='هذه المادة موجودة مسبقًا.'; }
    } else { $msg='بيانات مادة غير صالحة.'; }
}

// جلب المواد لعرضها بالقائمة
$courses=$pdo->query('SELECT id, year, semester, name FROM courses ORDER BY year, semester, name')->fetchAll();

// إضافة سؤال
if (isset($_POST['action']) && $_POST['action']==='add_q') {
    $cid=(int)($_POST['course_id']??0);
    $q  =trim($_POST['question']??'');
    $a1 =trim($_POST['opt1']??'');
    $a2 =trim($_POST['opt2']??'');
    $a3 =trim($_POST['opt3']??'');
    $a4 =trim($_POST['opt4']??'');
    $correct=(int)($_POST['correct']??1); // 1..4
    if ($cid>0 && $q!=='' && $a1!=='' && $a2!=='' && $a3!=='' && $a4!=='' && $correct>=1 && $correct<=4){
        $pdo->beginTransaction();
        try{
            $stmt=$pdo->prepare('INSERT INTO questions (course_id, question_text) VALUES (?,?)');
            $stmt->execute([$cid,$q]);
            $qid=(int)$pdo->lastInsertId();
            $opts=[$a1,$a2,$a3,$a4];
            for($i=0;$i<4;$i++){
              $ok = ($i+1===$correct) ? 1 : 0;
              $stmt=$pdo->prepare('INSERT INTO options (question_id, option_text, is_correct) VALUES (?,?,?)');
              $stmt->execute([$qid,$opts[$i],$ok]);
            }
            $pdo->commit();
            $msg='تمت إضافة السؤال.';
        }catch(Exception $e){ $pdo->rollBack(); $msg='فشل إضافة السؤال.'; }
    } else { $msg='أكمل كل الحقول بشكل صحيح.'; }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>لوحة الأدمن</title>
  <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
<div class="container"><div class="card">
  <h2>مرحبًا <?= htmlspecialchars($_SESSION['admin_user']) ?></h2>
  <p class="notice">لوحة إدارة المواد والأسئلة</p>
  <p><a class="btn" href="logout.php">تسجيل الخروج</a></p>
  <?php if ($msg): ?><p class="notice" style="color:#10b981;"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

  <h3>إضافة مادة</h3>
  <form method="post">
    <input type="hidden" name="action" value="add_course">
    <label>السنة</label>
    <select class="select" name="year" required>
      <?php for($y=1;$y<=6;$y++): ?><option value="<?= $y ?>">السنة <?= $y ?></option><?php endfor; ?>
    </select>
    <label>الفصل</label>
    <select class="select" name="semester" required>
      <option value="1">الأول</option>
      <option value="2">الثاني</option>
    </select>
    <label>اسم المادة</label>
    <input class="select" name="name" required placeholder="مثال: Anatomy">
    <br><br><button class="btn" type="submit">إضافة المادة</button>
  </form>

  <hr>
  <h3>إضافة سؤال</h3>
  <form method="post">
    <input type="hidden" name="action" value="add_q">
    <label>المادة</label>
    <select class="select" name="course_id" required>
      <?php foreach($courses as $c): ?>
        <option value="<?= $c['id'] ?>">
          سنة <?= $c['year'] ?> / فصل <?= $c['semester'] ?> — <?= htmlspecialchars($c['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <label>نص السؤال</label>
    <textarea class="select" name="question" rows="3" required></textarea>
    <div class="grid">
      <div>
        <label>الخيار (A)</label>
        <input class="select" name="opt1" required>
      </div>
      <div>
        <label>الخيار (B)</label>
        <input class="select" name="opt2" required>
      </div>
      <div>
        <label>الخيار (C)</label>
        <input class="select" name="opt3" required>
      </div>
      <div>
        <label>الخيار (D)</label>
        <input class="select" name="opt4" required>
      </div>
    </div>
    <label>الإجابة الصحيحة</label>
    <select class="select" name="correct" required>
      <option value="1">A</option>
      <option value="2">B</option>
      <option value="3">C</option>
      <option value="4">D</option>
    </select>
    <br><br><button class="btn" type="submit">إضافة السؤال</button>
  </form>

  <hr>
  <h3>المواد الحالية</h3>
  <ul>
    <?php if(!$courses) echo '<li class="notice">لا توجد مواد بعد</li>'; ?>
    <?php foreach($courses as $c): ?>
      <li>سنة <?= $c['year'] ?> / فصل <?= $c['semester'] ?> — <?= htmlspecialchars($c['name']) ?></li>
    <?php endforeach; ?>
  </ul>
</div></div>
</body>
</html>