<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>（仮）アイデア倉庫</title>

    <!-- 共通のcssの読み込み -->
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">

    <!--  個別のcssの読み込み -->
    <?php
    // 実行中のファイル名を取得。　（$_SERVER['PHP_SELF']パス全体を取得し、ファイル名から.phpを除去）
    $pageName = basename($_SERVER['SCRIPT_NAME'], '.php');
    $cssPath = "/509D1_A/css/pages/{$pageName}.css";

    // キャッシュ対策。絶対パスに変換。
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $cssPath;
    // $filePathのファイルがあれば、linkタグを表示。無ければ何も表示しない。（filemtime()ファイルの最終更新時刻)
    echo file_exists($filePath) ?
        "<link rel='stylesheet' href='{$cssPath}?v=" . filemtime($filePath) . "'>" :
        '';
    ?>
</head>

<body>
    <header class="header">
        <h1>
            <a href="questions.php">アイデア倉庫</a>
        </h1>

        <?php
        // ログアウトが押されたら、セッションを削除
        if (isset($_POST['logout'])) {
            unset($_SESSION['user']);
        }

        // いま開いているファイル名を取得
        $current = basename($_SERVER['SCRIPT_NAME']);

        ?>

        <div class="header-wrap">
            <?php
            // いま開いているファイル名を取得
            $current = basename($_SERVER['SCRIPT_NAME']);

            // questions.phpの時は、質問を追加ボタンを表示
            if ($current === "questions.php") {
                echo '<a href="questionInput.php"><button>質問を追加</button></a>';
            }

            // ログイン時は、ログアウトボタンを表示
            if (isset($_SESSION['user'])) {
                echo <<<HTML
                    <form action="changePassword.php" method="post">
                        <input type="submit" name="changePassword" value="パスワード変更" class="submit">
                    </form>
                    <form action="index.php" method="post">
                        <input type="submit" name="logout" value="ログアウト" class="submit">
                    </form>
                HTML;

                // index.phpの時意外、ログインボタンを表示
            } else {
                if ($current !== "index.php") {
                    echo '<a href="index.php"><button>ログイン</button></a>';
                }
            }
            ?>
        </div>
    </header>
    <main>
