<?php
require __DIR__ . "/includes/config.php";

$newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : "";
$confirmPass = isset($_POST['confirmPass']) ? trim($_POST['confirmPass']) : "";
$message = $_SESSION['message'] ?? "";
unset($_SESSION['message']);
$success = $_SESSION['success'] ?? "";
unset($_SESSION['success']);

if (isset($_POST['change'])) {

    if (empty($newPassword) || empty($confirmPass)) {
        $_SESSION['message'] = "全ての項目を入力してください。";
    }

    if (strlen($newPassword) < 6 || strlen($newPassword) > 10 || !preg_match('/^[a-zA-Z0-9]+$/', $newPassword)) {
        $_SESSION['message'] = "パスワードは6文字以上、10文字以下の半角英数字のみで設定してください。";
    }

    if ($newPassword !== $confirmPass) {
        $_SESSION['message'] = "新しいパスワードと確認用パスワードが一致していません。";
    }
    if (isset($_SESSION['message'])) {
        header('Location:changePassword.php');
        exit;
    }

    if (changePassword($newPassword, $_SESSION['user']['id'])) {
        $_SESSION['success'] = "パスワードを変更しました。";
        header('Location:changePassword.php');
        exit;
    } else {
        $_SESSION['message'] = "パスワードの変更に失敗しました。";
        header('Location:changePassword.php');
        exit;
    }
}

?>

<?php
// ここから HTML を出力
require __DIR__ . "/includes/header.php";
?>

<section>
    <h2>パスワード変更<h2>
            <form action="changePassword.php" method="post">
                <div class="text-wrap">
                    <label for="pass">新しいパスワード</label>
                    <input type="text" name="newPassword" placeholder=6文字から10文字、半角英数字のみ class="text">
                </div>
                <div class="text-wrap">
                    <label for="pass">確認用パスワード</label>
                    <input type="text" name="confirmPass" placeholder=6文字から10文字、半角英数字のみ class="text">
                </div>
                <div>
                    <input type="submit" value="登録" name="change" class="input-submit">
                    <?php if(isset($message)):?>
                        <span><?= $message ?></span>
                    <?php endif;?>   
                    <?php if(isset($success)):?>
                        <?= $success ?>
                    <?php endif;?>   
                </div>
            </form>
            <a href="questions.php">
                <button type="button">戻る</button>
            </a>
</section>
<?php require 'includes/footer.php'; ?>