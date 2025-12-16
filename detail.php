<?php
require __DIR__ . "/includes/config.php";

// エスケープ
if (!function_exists('h')) {
    function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$questionId = (int)($_GET['questionId'] ?? 0);

/* -------- 回答削除 -------- */
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['deleteAnswer'])) {
    $answerId = (int)($_POST['answerId'] ?? 0);
    $qid      = (int)($_POST['questionId'] ?? 0);

    if ($answerId > 0 && !empty($_SESSION['user'])) {
        $stmt = $pdo->prepare('SELECT userId, questionId FROM answer WHERE id = ? AND deleteFlg = 0');
        $stmt->execute([$answerId]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ((int)$row['userId'] === (int)($_SESSION['user']['id'] ?? 0)) {
                deleteAnswer($answerId);
                $qid = (int)$row['questionId'];
            }
        }
    }

    header('Location: detail.php?questionId=' . $qid);
    exit;
}

/* -------- 質問 -------- */
$question = $questionId ? getQuestionById($questionId) : null;

/* -------- 回答一覧 -------- */
$answers = [];
if ($question) {
    $sql = "
        SELECT a.id AS answerId, a.userId AS answerUserId, u.name AS answerName,
               a.answer, a.date AS answerDate
        FROM answer AS a
        JOIN user   AS u ON a.userId = u.id
        WHERE a.questionId = ? AND a.deleteFlg = 0
        ORDER BY a.date DESC, a.id ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([(int)$question['questionId']]);
    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php
// ここから HTML を出力
require __DIR__ . "/includes/header.php";
?>
<section>
  <?php if (!$question): ?>
    <p>対象の質問が見つかりませんでした。</p>
    <div class="card-wrap">
      <form action="questions.php" method="get">
        <button type="submit">戻る</button>
      </form>
    </div>
    <?php require_once __DIR__ . '/includes/footer.php'; exit; ?>
  <?php endif; ?>

  <!-- 質問本体 -->
  <section>
    <h2>質問</h2>
    <div class="card">

      <p class="card-title"><?= h($question['questionName'] ?? '') ?></p>

      <p><?= nl2br(h($question['question'] ?? '')) ?></p>

      <p class="card-date">
        <?= !empty($question['questionDate']) ? date('Y/m/d H:i', strtotime($question['questionDate'])) : '日時未設定' ?>
      </p>

      <div class="card-wrap">
        <form action="answer.php" method="get">
          <input type="hidden" name="questionId" value="<?= (int)$question['questionId'] ?>">
          <button type="submit">回答</button>
        </form>
      </div>
    </div>
  </section>

  <!-- 回答一覧 -->
  <section>
    <h2 class="card-offset">回答</h2>

    <?php if ($answers): ?>
      <?php foreach ($answers as $a): ?>
        <?php $mine = !empty($_SESSION['user']) && (int)$_SESSION['user']['id'] === (int)$a['answerUserId']; ?>

        <div class="card card-offset">


          <p class="card-title"><?= h($a['answerName'] ?? '') ?></p>

          <p><?= nl2br(h($a['answer'] ?? '')) ?></p>

          <p class="card-date">
            <?= !empty($a['answerDate']) ? date('Y/m/d H:i', strtotime($a['answerDate'])) : '日時未設定' ?>
          </p>

          <?php if ($mine): ?>
            <div class="card-wrap">
              <form action="detail.php?questionId=<?= (int)$question['questionId'] ?>" method="post">
                <input type="hidden" name="questionId" value="<?= (int)$question['questionId'] ?>">
                <input type="hidden" name="answerId"   value="<?= (int)$a['answerId'] ?>">
                <button type="submit" name="deleteAnswer">削除</button>
              </form>
            </div>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>まだ回答はありません。</p>
    <?php endif; ?>
  </section>

  <!-- 戻る -->
  <div class="card-wrap">
    <form action="questions.php" method="get">
      <button type="submit">戻る</button>
    </form>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
