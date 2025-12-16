<?php
require __DIR__ . "/includes/config.php";

// 入力値を取得する関数
function Input($key)
{
    if (isset($_POST[$key])) {
        return trim($_POST[$key]);
    }
    return null;
}


// ログイン処理関数
function Login(){
    $userId = Input('userId');
    $userPw = Input('userPw');

if (isset($_POST['login'])) {
    if (empty($userId) || empty($userPw)) {
            return "ユーザーIDとパスワードを入力してください。";
        } elseif (strlen($userId) < 8) {
            return "ユーザーIDは8文字以上で入力してください。";
        } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $userId)) {
            return "ユーザーIDは半角英数字のみで入力してください。";
        } elseif (strlen($userPw) < 6) {
            return "パスワードは6文字以上で入力してください。";
        } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $userPw)) {
            return "パスワードは半角英数字のみで入力してください。";
        } else {

            $userData = getUser($userId, $userPw);

            if ($userData) {
                // セッション登録
                $_SESSION['user'] = [
                    'id' => $userData['id'],
                    'loginId' => $userData['loginId'],
                    'name' => $userData['name'],
                ];
                // 画面遷移先判定
                if (isset($_SESSION['url'])) {
                    header('Location: ' . $_SESSION['url']);
                    unset($_SESSION['url']);
                    exit;
                    // echo "成功";

                } else {
                    header('Location: questions.php');
                    exit;
                    // echo "失敗";
                }
            } else {
                return "ユーザーIDまたはパスワードが正しくありません。";
            }
        }
    }
}
?>

<?php
// ここから HTML を出力
require __DIR__ . "/includes/header.php";
?>

<section>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // POST → ログイン判定
        $err = Login();
        echo "<span>" . $err . "</span>";
    }
    ?>

    <form action="index.php" method=post  class="index-outer">
        <div class="index-inner">
            <div class="input-wrap">
                <label for="userId">ユーザーID</label>
                <input type="text" name="userId" id="userId" class="input-text">
            </div>
            <div class="input-wrap">
                <label for="userPw">パスワード</label>
                <input type="password" name="userPw" id="userPw" class="input-text">
            </div>
        </div>
        <input type="submit" value="ログイン" class="input-submit" name="login">
    </form>
    <a href="userAdd.php" class="index-position">
        <button>新規登録</button>
    </a>
</section>

<?php require 'includes/footer.php'; ?>