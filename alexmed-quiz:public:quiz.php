<?php require_once __DIR__ . '/../includes/db.php';
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;
if ($course_id <= 0) { header('Location: index.php'); exit; }

// اجلب عنوان المادة
$cstmt = $pdo->prepare('SELECT name FROM courses WHERE id=?');
$cstmt->execute([$course_id]);
$course = $cstmt->fetch();
if (!$course) { header('Location: index.php'); exit; }

// اجلب الأسئلة وخياراتها
$qstmt = $pdo->prepare('SELECT q.id AS qid, q.question_text, o.id AS oid, o.option_text, o.is_correct
                        FROM questions q
                        JOIN options o ON o.question_id = q.id
                        WHERE q.course_id = ?
                        ORDER BY q.id, o.id');
$qstmt->execute([$course_id]);
$rows = $qstmt->fetchAll();

$questions = [];
foreach ($rows as $r) {
    $qid = $r['qid'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'id' => $qid,
            'text' => $r['question_text'],
            'options' => []
        ];
    }
    $questions[$qid]['options'][] = [
        'id' => $r['oid'],
        'text' => $r['option_text'],
        'ok' => (bool)$r['is_correct']
    ];
}
$questions = array_values($questions);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>كويز — <?= htmlspecialchars($course['name']) ?></title>
  <link rel="stylesheet" href="./styles.css">
</head>
<body>
<div class="container">
  <div class="card">
    <h2>مادة: <?= htmlspecialchars($course['name']) ?></h2>
    <?php if (empty($questions)): ?>
      <p class="notice">لا توجد أسئلة لهذه المادة حتى الآن.</p>
      <a class="btn" href="javascript:history.back()">↩ العودة</a>
    <?php else: ?>
      <div id="quiz">
        <div class="badge" id="prog">السؤال <span id="qnum">1</span> / <span id="qtotal"></span></div>
        <h3 id="qtext" style="margin:12px 0 10px"></h3>
        <div id="opts" class="grid"></div>
        <div class="footer">
          <button class="btn" id="nextBtn">التالي »</button>
          <button class="btn" id="finishBtn">إنهاء</button>
        </div>
        <div class="notice" id="feedback"></div>
      </div>
      <div id="result" style="display:none">
        <h2>نتيجتك</h2>
        <p>أجبتَ بشكل صحيح: <strong id="score"></strong> من <strong id="total"></strong></p>
        <a class="btn" href="courses.php?year=1&sem=1">« العودة للمواد</a>
      </div>
    <?php endif; ?>
  </div>
</div>
<script>
const DATA = <?php echo json_encode($questions, JSON_UNESCAPED_UNICODE); ?>;
let idx = 0, correct = 0, answered = false;
const qnum = document.getElementById('qnum');
const qtotal = document.getElementById('qtotal');
const qtext = document.getElementById('qtext');
const opts = document.getElementById('opts');
const feedback = document.getElementById('feedback');
const nextBtn = document.getElementById('nextBtn');
const finishBtn = document.getElementById('finishBtn');

qtotal.textContent = DATA.length;
render();

function render(){
  answered = false; feedback.textContent='';
  const q = DATA[idx];
  qnum.textContent = (idx+1);
  qtext.textContent = q.text;
  opts.innerHTML = '';
  q.options.forEach((o, i) => {
    const div = document.createElement('div');
    div.className = 'option';
    div.textContent = (String.fromCharCode(0x41+i) + ') ' + o.text);
    div.onclick = () => choose(o, div);
    opts.appendChild(div);
  });
}

function choose(opt, el){
  if (answered) return; answered = true;
  if (opt.ok){
    correct++; el.classList.add('correct');
    feedback.textContent = '✔️ إجابة صحيحة';
  } else {
    el.classList.add('wrong');
    const right = [...DATA[idx].options].find(o=>o.ok);
    feedback.textContent = '❌ خاطئة — الإجابة الصحيحة: ' + right.text;
  }
}

nextBtn.onclick = () => {
  if (idx < DATA.length - 1){ idx++; render(); }
  else showResult();
};
finishBtn.onclick = showResult;

function showResult(){
  document.getElementById('quiz').style.display='none';
  document.getElementById('result').style.display='block';
  document.getElementById('score').textContent = correct;
  document.getElementById('total').textContent = DATA.length;
}
</script>
</body>
</html>