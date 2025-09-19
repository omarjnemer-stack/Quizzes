<?php require_once __DIR__ . '/../includes/db.php';

// إن وُجد أدمن؟
$hasAdmin = (int)$pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0;

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$hasAdmin && isset($_POST['new_username'], $_POST['new_password'])) {
        $u = trim($_POST['new_username']);
        $p = $_POST['new_password'];
        if ($u !== '' && $p !== '') {
            $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
            $stmt->execute([$u, password_hash($p, PASSWORD_DEFAULT)]);
            $hasAdmin = true; // صار في أدمن
        } else { $error = 'يجب إدخال اسم مستخدم وكلمة مرور.'; }
    } elseif ($hasAdmin && isset($_POST['username'], $_POST['password'])) {
        $u = trim($_POST['username']);
        $p = $_POST['password'];
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username=?');
        $stmt->execute([$u]);
        $user = $stmt->fetch();
        if ($user && password_verify($p, $user['password_hash'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_user'] = $user['username'];
            header('Location: index.php'); exit;
        } else { $error = 'بيانات دخول غير صحيحة.'; }
    }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>لوحة الإدارة</title>
  <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
<div class="container"><div class="card">
  <h2>لوحة الإدارة</h2>
  <?php if ($error): ?><p class="notice" style="color:#f87171;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

  <?php if (!$hasAdmin): ?>
    <p class="notice">لا يوجد حساب أدمن بعد — أنشئ أول حساب:</p>
    <form method="post">
      <label>اسم المستخدم</label>
      <input class="select" name="new_username" required>
      <label>كلمة المرور</label>
      <input class="select" type="password" name="new_password" required>
      <br><br>
      <button class="btn" type="submit">إنشاء وتسجيل الدخول</button>
    </form>
  <?php else: ?>
    <form method="post">
      <label>اسم المستخدم</label>
      <input class="select" name="username" required>
      <label>كلمة المرور</label>
      <input class="select" type="password" name="password" required>
      <br><br>
      <button class="btn" type="submit">دخول</button>
    </form>
  <?php endif; ?>
</div></div>
</body></nhtml>