<?php
require __DIR__ . "/includes/config.php";

//未ログインなら、ログイン画面へ
if (!isset($_SESSION['user'])) {
    noLogin();
}

$err = $_SESSION['err'] ?? "";
unset($_SESSION['err']);
$userId = $_SESSION['user']['id'] ?? "";
// 入力されていれば、入力内容の空白を除去して代入。入力されていなければ、空欄を代入。
$question = isset($_POST['question']) ? trim($_POST['question']) : "";

// 登録ボタンが押されたら実行
if (isset($_POST['questionPost'])) {

    // 入力されていなければエラー表示
    if ($question == "") {
        $_SESSION['err'] = "質問内容を入力してください。";
        header('Location: questionInput.php');
        exit;

    // 255文字以上の入力でエラー表示
    } else if (mb_strlen($question) > 255) {
        $_SESSION['err'] = "文字数の上限は255文字です。";
        header('Location: questionInput.php');
        exit;

    // 入力されていればデータベースに登録
    } else {
        if (addQuestion($userId, $question)) {
            header('Location: questions.php');
            exit;
        } else {
            $_SESSION['err'] = "質問の登録に失敗しました。";
        }
    }
}
?>

<?php
// ここから HTML を出力
require __DIR__ . "/includes/header.php";
?>

<section>
    <h2>質問を投稿する</h2>
    <form action="questionInput.php" method="post">
        <textarea name="question" placeholder="255文字以内でご入力ください"></textarea>
        <div>
            <input type="submit" value="登録" class="input-submit" name="questionPost">
            <span><?= $err ?></span>
        </div>
    </form>
    <a href="questions.php">
        <button>戻る</button>
    </a>
</section>
<?php require "includes/footer.php"; ?>