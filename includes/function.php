 <!-- 
--------------------------------------------------------------
現在表示しているphp合わせて、読み込むcssの切り替える。
--------------------------------------------------------------
 -->

 <?php
    function switchCss($path)
    {
        // 物理パスに変換
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $path;

        // 三項演算子を使って、存在チェックと値の返却を1行で実行
        return file_exists($filePath) ? '?v=' . filemtime($filePath) : '';
    }
    ?>

 <!-- 
--------------------------------------------------------------
PDOインスタンスを生成する。
※管理がいやすい為、dbConnect.phpkで管理。
--------------------------------------------------------------
 -->

 <!-- 
--------------------------------------------------------------
ログインID、パスワードを基にDBに登録されているユーザーか確認する。
セッションに登録する。←仕様書には無いが追加した。
※判定のみの為、適切に入力されているかは個別のphpファイルで制御。
--------------------------------------------------------------
 -->
 <?php
    function isUser(string $userId, string $userPw)
    {
        global $pdo;

        try {
            $userInfo = "
                SELECT *
                FROM user 
                WHERE loginId=?
            ";

            $sql = $pdo->prepare($userInfo);
            $sql->execute([$userId]);

            // 対応する1行を、連想配列として変数化
            $userData = $sql->fetch(PDO::FETCH_ASSOC);

            // ユーザーが存在し、かつ password_verify でパスワードが一致する場合に成功
            if ($userData && password_verify($userPw, $userData['password'])) {
                return true;
            } else {
                return false;
            }

            // 失敗した場合、ログに書き出し、処理を失敗させる
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>

 <!-- 
--------------------------------------------------------------
ログインID、パスワードを基にDBに登録されているユーザー情報を取得し、連想配列(userData)で返却する。DB未登録の場合は、falseを返却する。
※データを取る機能のみの為、適切に入力されているかは個別のphpファイルで制御。
↓以下を、連想配列で取得↓
userテーブルのid, loginid, name
--------------------------------------------------------------
 -->
<?php
    function getUser(string $userId, string $userPw)
    {
        global $pdo;

        try {
            $userInfo = "
                SELECT *
                FROM user 
                WHERE loginId=?
            ";

            $sql = $pdo->prepare($userInfo);
            $sql->execute([$userId]);

            // 対応する1行を、連想配列として変数化
            $userData = $sql->fetch(PDO::FETCH_ASSOC);

            if ($userData && password_verify($userPw, $userData['password'])) {
                // if ($userPw == $userData['password']) {
                // 機密情報の'password'を配列から削除
                unset($userData['password']);

                // $userDataに値があれば、連想配列を返却。値が無ければfalseを返却。
                return $userData;

                // 失敗した場合、ログに書き出し、処理を失敗させる
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }

    ?>

 <!-- 
--------------------------------------------------------------
引数を基に利用者情報をDBに登録する。
ログインIDとパスワードは、組み合わせユニークとする。(この条件は非推奨の為、ログインIDがユニークにします。)←これ、講師に伝えたほうがいいかな？
※判定のみの為、適切に入力されているかは個別のphpファイルで制御。
--------------------------------------------------------------
 -->
  <?php

    function addUser(string $viewName, string $userId, string $userPw)
    {
        global $pdo;

        try {
            // ログイン名の重複チェック（入力したuserIdがデータベースに無ければok)
            $sqlCheck = $pdo->prepare('SELECT COUNT(*) FROM user WHERE loginId = ?');
            $sqlCheck->execute([$userId]);
            $count = $sqlCheck->fetchColumn(); // count(*)の結果が$countに代入される

            // $countがで0true
            if ($count == 0) {

                // password_hash()パスワード化
                $hashedPassword = password_hash($userPw, PASSWORD_DEFAULT);

                // データベースに追加
                $sql = $pdo->prepare('INSERT INTO user(name, loginId, password) VALUES(?, ?, ?)');
                $sql->execute([$viewName, $userId, $hashedPassword]);

                return true;
            } else {
                // ログインIDが既に存在する
                return false;
            }
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>


 <!-- 
--------------------------------------------------------------
DBから質問表示に必要な情報を２次元連想配列で取得する。日付の新しい順、質問番号順を意識する。
↓以下を、連想配列で取得(deleteFlgは除く)↓
質問番号（questionId）,質問内容(question),氏名（questionName）,投稿日(questionDate)
--------------------------------------------------------------
 -->
 <?php
    function getQuetion(): array|false
    {
        global $pdo;

        try {
            $questionItem = "
                SELECT q.id AS questionId, u.name AS questionName, question, date AS questionDate 
                FROM question AS q
                JOIN user AS u
                ON q.userId = u.id
                WHERE deleteFlg = 0
                ORDER BY date ASC, q.id ASC
            ";

            $sql = $pdo->prepare($questionItem);
            $sql->execute();

            //fetchAll()全データを。 PDO::FETCH_ASSOCカラム名をキーとした連想配列形式にする
            $questionData = $sql->fetchAll(PDO::FETCH_ASSOC);

            return $questionData;

            // 失敗した場合、ログに書き出し、処理を失敗させる
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>


 <!-- 
--------------------------------------------------------------
登録時点の日時を取得し、質問をDBに登録する。
※登録機能のみの為、適切に入力されているかは個別のphpファイルで制御。
↓以下をDBに登録↓
questionテーブルのuserId, question, date
--------------------------------------------------------------
 -->
 <?php
    function addQuestion(int $userId, string $question)
    {
        global $pdo;

        try {
            $addQuestion = "
                INSERT INTO question (userId, question, date)
                VALUES (?, ?, NOW())
            ";

            // データベースに登録
            $sql = $pdo->prepare($addQuestion);
            $sql->execute([$userId, $question]);

            return true;

            // 失敗した場合、ログに書き出し、処理を失敗させる
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>


 <!-- 
--------------------------------------------------------------
質問IDに紐つく回答もDBから論理削除する。排他制御を意識する。
※質問とそれに紐づく回答のdeleteFlgを1(true)にしただけの為、表示非表示は個別に制御。
--------------------------------------------------------------
 -->
 <?php
    function deleteQuextion(int $questionId): bool
    {
        global $pdo;

        try {
            //まとめて更新ここから
            $pdo->beginTransaction();

            // 質問の削除設定
            $deleteQuestion = "
            UPDATE question
            SET deleteFlg = 1
            WHERE id = ?
        ";
            $sql = $pdo->prepare($deleteQuestion);
            $sql->execute([$questionId]);

            // 回答の削除設定
            $deleteAnswer = "
            UPDATE answer
            SET deleteFlg = 1
            WHERE questionId = ?
        ";
            $sql = $pdo->prepare($deleteAnswer);
            $sql->execute([$questionId]);

            // まとめて更新ここまで
            $pdo->commit();

            return true;

            // 失敗した場合、ログに書き出し、処理を失敗させる
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>

 <!-- 
--------------------------------------------------------------
質問IDに紐つく質問情報を取得する。表示項目に留意する。
↓以下を、配列で取得(deleteFlgは除く)↓
質問番号（questionId）,質問内容(question),氏名（questionName）,投稿日(questionDate)
--------------------------------------------------------------
 -->
 <?php
    function getQuestionById(int $questionId): array|false
    {
        global $pdo;

        try {

            $questionItems = "
                SELECT q.id AS questionId, u.name AS questionName, question, date AS questionDate 
                FROM question AS q
                JOIN user AS u
                ON q.userId = u.id
                WHERE q.id = ? AND deleteFlg = 0
                ORDER BY date ASC, q.id ASC
            ";
            $sql = $pdo->prepare($questionItems);
            $sql->execute([$questionId]);

            //fetch()一行ずつ取り出す。 PDO::FETCH_ASSOCカラム名をキーとした連想配列形式にする
            $questionData = $sql->fetch(PDO::FETCH_ASSOC);

            return $questionData;

            // 失敗した場合、ログに書き出し、処理を失敗させる
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>

 <!-- 
--------------------------------------------------------------
質問IDに紐つく回答情報を取得する。
表示項目に留意する。日付の新しい順、回答番号順を意識する。
↓以下を、連想配列で取得(deleteFlgは除く)↓
回答番号(answerId),氏名（answerName）,回答内容（answer）,投稿日（answerDate）
--------------------------------------------------------------
 -->
 <?php
    function getAnswersByQuestionId(int $questionId): array|false
    {
        global $pdo;

        try {
            $answerItems = "
            SELECT a.id AS answerId, u.name AS answerName, answer, date AS answerDate
            FROM answer AS a
            JOIN user AS u
            ON a.userId = u.id
            WHERE questionId = ? AND deleteFlg = 0
            ORDER BY date DESC, a.id
        ";

            $sql = $pdo->prepare($answerItems);
            $sql->execute([$questionId]);

            //fetchAll()全データを。 PDO::FETCH_ASSOCカラム名をキーとした連想配列形式にする
            $answerData = $sql->fetchAll(PDO::FETCH_ASSOC);

            return $answerData;

            // 失敗した場合、ログに書き出し、処理を失敗させる
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>

 <!-- 
--------------------------------------------------------------
登録時点の日時を取得し、回答をDBに登録する。
※登録機能のみの為、適切に入力されているかは個別のphpファイルで制御。
↓以下をDBに登録↓
answerテーブルのuserId, questionId, answer, date
--------------------------------------------------------------
 -->
 <?php
    function addAnswer(int $userId, int $questionId, string $answer): bool
    {
        global $pdo;

        try {
            // 回答をデータベースに登録
            $addAnswer = "
                INSERT INTO answer (userId, questionId, answer, date) 
                VALUES (?, ?, ?, NOW())
            ";

            $sql = $pdo->prepare($addAnswer);
            $sql->execute([$userId, $questionId, $answer]);

            return true;

            // 失敗した場合、ログに書き出し、処理を失敗させる
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>

 <!-- 
--------------------------------------------------------------
回答IDに紐つく回答をDBから論理削除する。排他制御を意識する。
※回答IDに紐つく回答のdeleteFlgを1(true)にしただけの為、表示非表示は個別に制御。
--------------------------------------------------------------
 -->
 <?php
    function deleteAnswer(int $answerId): bool
    {
        global $pdo;

        try {
            // 回答の削除設定
            $deleteAnswer = "
                UPDATE answer
                SET deleteFlg = 1
                WHERE id = ?
            ";
            $sql = $pdo->prepare($deleteAnswer);
            $sql->execute([$answerId]);

            return true;

            // 失敗した場合、ログに書き出し、処理を失敗させる
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>

 <!-- 
--------------------------------------------------------------
未ログイン時、質問・回答登録ボタンを押した際、セッションに現在のURLを登録する。
--------------------------------------------------------------
 -->

 <?php
    function noLogin()
    {
        //現在のurlをセッションに登録
        $_SESSION['url'] = $_SERVER['REQUEST_URI'];

        header('Location: index.php');
        exit;
    }
    ?>
    
 <!-- 
--------------------------------------------------------------
パスワード変更--------------------------------------------------------------
 -->

 <?php
    function changePassword(string $newPassword, int $userId)
    {
        global $pdo;

            try {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            if (isset($_SESSION['user'])) {
                $questionSearch = "
                    UPDATE user
                    SET password = ?
                    WHERE id = ?
                ";

                $sql = $pdo->prepare($questionSearch);
                $sql->execute([$hashedPassword, $userId]);

                return true;
            } else{
                return false;
            }
            
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        }
    }
    ?>