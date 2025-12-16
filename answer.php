<?php
require __DIR__ . "/includes/config.php";

//未ログインなら、ログイン画面へ
if (!isset($_SESSION['user'])) {
    noLogin();
}

$err = $_SESSION['err'] ?? "";
unset($_SESSION['err']);
// エスケープ関数
if (!function_exists('h')) {
    function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// パラメータ
$questionId = isset($_GET['questionId']) ? (int)$_GET['questionId'] : 0;
?>

<?php
// ここから HTML を出力
require __DIR__ . "/includes/header.php";
?>
<section>
    <?php
    // 質問取得（存在チェック用）
    $question = $questionId ? getQuestionById($questionId) : null;
    if (!$question) {
        echo '<section class="--page">';
        echo '<p>対象の質問が見つかりませんでした。</p>';
        echo '<p><a href="questions.php">戻る</a></p>';
        echo '</section>';
        require_once __DIR__ . '/includes/footer.php';
        exit;
    }

    // 送信処理（回答登録）
    if (isset($_POST['answerPost'])) {
        $answerText = trim((string)($_POST['answer'] ?? ''));
        // ログイン必須（仮ログインがあるなら通るはず）
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        if ($userId > 0 && $questionId > 0 && $answerText !== '') {
            // addAnswer(userId, questionId, answer) を使用
            if (addAnswer($userId, $questionId, $answerText)) {
                // 二重送信防止で詳細へ戻る
                header('Location: detail.php?questionId=' . $questionId);
                exit;
            } else {
                $_SESSION['err'] = "回答の登録に失敗しました。";
                // header('Location: answer.php');
                // exit;
            }
        } else {
            $_SESSION['err'] = "回答を入力してください。";
            // header('Location: answer.php');
            // exit;
        }
    }
    ?>

    <link rel="stylesheet" href="css/pages/answer.css?v=<?= h(file_exists(__DIR__.'/css/pages/answer.css') ? filemtime(__DIR__.'/css/pages/answer.css') : time()) ?>">

    <section class="answer-page">

    <div class="q-head">
        <h2 class="q-title">質問</h2>
        <div class="card question-card">
            <p class="name"><?= h($question['questionName'] ?? '') ?></p>
            <p class="body"><?= nl2br(h($question['question'] ?? '')) ?></p>
            <p class="meta">
            <?= !empty($question['questionDate']) ? date('Y/m/d H:i', strtotime($question['questionDate'])) : '日時未設定' ?>
            </p>
        </div>
    </div>

    <div class="answer-form">
        <h3 class="answer-textarea">回答</h3>
        <form action="answer.php?questionId=<?= (int)$questionId ?>" method="post">
            <textarea name="answer" rows="6" class="answer-textarea"></textarea>
<?php//回答を登録するボタン?>
            <div class="answer-actions">
            <button type="submit" class="btn" name="answerPost">登録</button>
    <?php if (!empty($err)): ?>
        <p class="answer-error"><?= $err ?></p>
    <?php endif; ?>

        </div>
    </form>
<?php//何も入力せずdetailphpに戻るボタン?>
    <form action="detail.php" method="get" class="answer-back-form">
        <input type="hidden" name="questionId" value="<?= (int)$question['questionId'] ?>">
        <button type="submit" class="btn">戻る</button>
    </form>
  </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
