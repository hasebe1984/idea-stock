<?php
require __DIR__ . "/includes/config.php";

// 削除ボタンのPOSTを受けたら deleteQuextionを実行して一覧に戻る
if (isset($_POST['delete'])) {
    $qid = (int)($_POST['questionId'] ?? 0);     
    if ($qid > 0 && function_exists('deleteQuextion')) {
        deleteQuextion($qid);                    
    }
    header('Location: questions.php');          // ★リロードでの多重送信防止＆一覧に戻す
    exit;
}
?>


<?php
$err = '';

// エスケープ関数（既存のまま）
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<?php
// ログイン判定と自分のユーザーID取得
if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool { return !empty($_SESSION['user']); }
}
if (!function_exists('authUserId')) {
    function authUserId(): int { return (int)($_SESSION['user']['id'] ?? 0); }
}
?>

<?php if ($err !== ''): ?>
    <span><?= h($err) ?></span>
<?php endif; ?>

<?php
$items = $pdo->query(
    'SELECT id, userId FROM question WHERE deleteFlg = 0 ORDER BY `date` DESC, id DESC'
)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
// ここから HTML を出力
require __DIR__ . "/includes/header.php";
?>

<section>
    <?php
    if ($items) {
        foreach ($items as $it) {

            $qid     = (int)$it['id'];      // 対象の質問ID
            $ownerId = (int)$it['userId'];  // 投稿者ID

            $row = getQuestionById($qid); // 戻り: ['questionId','questionName','question','questionDate']
            if (!$row) { continue; }

            $id   = (int)($row['questionId'] ?? 0);
            $name = h($row['questionName'] ?? '');
            $body = nl2br(h($row['question'] ?? ''));
            $ts   = strtotime($row['questionDate'] ?? '');
            $when = $ts ? date('Y/m/d H:i', $ts) : '日時未設定';

            echo '<div class="card">';

            // 質問者名 → card-title に
            echo '<p class="card-title">', $name, '</p>';

            // 質問本文
            echo '<p>', $body, '</p>';

            // 日付 → card-date に
            echo '<p class="card-date">', $when, '</p>';

            // ボタン行は card-wrap で横並びに
            echo '<div class="card-wrap">';

            // 詳細ボタン（btn / inline は使わない）
            echo '<form action="detail.php" method="get">';
            echo '  <input type="hidden" name="questionId" value="', $id, '">';
            echo '  <button type="submit" name="detail">詳細</button>';
            echo '</form>';

            // 自分の投稿だけ削除ボタン
            if (isLoggedIn() && authUserId() === $ownerId) {
                echo '<form action="questions.php" method="post">';
                echo '  <input type="hidden" name="questionId" value="', $qid, '">';
                echo '  <button type="submit" name="delete">削除</button>';
                echo '</form>';
            }

            echo '</div>';  // card-wrap
            echo '</div>';  // card
        }
    } else {
        echo '<p>データがありません。</p>';
    }
    ?>
</section>
<? require_once __DIR__ . '/includes/footer.php'; ?>
