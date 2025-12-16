<?php
require __DIR__ . "/includes/config.php";

$viewName = isset($_POST['viewName']) ? trim($_POST['viewName']) : "";
$userId = isset($_POST['userId']) ? trim($_POST['userId']) : "";
$userPw = isset($_POST['pass']) ? trim($_POST['pass']) : "";
$message = "";

// フォームがPOSTされた場合のみ実行
if (isset($_POST['register'])) {

    if (empty($viewName) || empty($userId) || empty($userPw)) {
        $message = "全ての項目を入力してください。";
    } elseif (mb_strlen($viewName) > 10) {
        $message = "表示名は10文字以下で入力してください";
    } elseif (mb_strlen($userId) < 8 || strlen($userId) > 10 || !preg_match('/^[a-zA-Z0-9]+$/', $userId)) {
        $message = "ユーザーIDは8文字以上10文字以下かつ半角英数字で入力してください。";
    } elseif (mb_strlen($userPw) < 6 || !preg_match('/^[a-zA-Z0-9]+$/', $userPw)) {
        $message = "パスワードは6文字以上かつ半角英数字で入力してください。";
    } else {

        if (addUser($viewName, $userId, $userPw)) {
            // 登録成功の場合のみリダイレクト
            header('Location:index.php');
            exit; // 成功したらここでスクリプト終了
        } else {
            $message = "この利用者IDは、既に使用されています。";
        }
    }
}
?>

<?php
// ここから HTML を出力
require __DIR__ . "/includes/header.php";
?>


<section>
    <h2>利用者登録</h2>
            <form action="userAdd.php" method="post">
                <div class="input-wrap">
                    <label for="viewName"> 表示名 </label>
                    <input type="text" name="viewName" placeholder=例）山田太郎 class="input-text">
                </div>
                <div class="input-wrap">
                    <label for="userId"> ユーザーID </label>
                    <input type="text" name="userId" placeholder=8文字以上10文字以下かつ半角英数字のみ class="input-text">
                </div>
                <div class="input-wrap">
                    <label for="pass"> パスワード </label>
                    <input type="text" name="pass" placeholder=6文字以上、半角英数字のみ class="input-text">
                </div>
                <div>
                    <input type="submit" value="登録" name="register" class="input-submit margin-left">
                    <span><?= htmlspecialchars($message) ?></span>
                </div>
            </form>
            <a href="index.php" class = "margin-left">
                <button type="button">戻る</button>
            </a>
</section>
<?php require 'includes/footer.php'; ?>