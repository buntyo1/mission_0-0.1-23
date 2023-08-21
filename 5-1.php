<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
    // DB接続設定
    $dsn = 'mysql:"データベース名";host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    // BDデータベース内のテーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "word TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    // 編集フォームに表示するための名前とコメント
    $edit_name = "";
    $edit_comment = "";
    $edit_id = "";
    
    // 編集機能
    if(isset($_POST["edit"]) && isset($_POST["edit_num"])){
        $edit_num = $_POST["edit_num"];
        
        // 編集対象の投稿を取得
        $sql = 'SELECT * FROM tbtest WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $edit_num, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        // 編集フォームに表示するための名前とコメントを設定
        $edit_name = $result['name'];
        $edit_comment = $result['comment'];
        $edit_id = $result['id'];
    }
    
    // 送信ボタンが押されたとき（新規投稿または編集完了）
    if(isset($_POST["submit"])){
        if(isset($_POST["name"]) && isset($_POST["comment"]) && isset($_POST["password"])){
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $date = date("Y/n/j G:i:s");
            $password = $_POST["password"];
            
            // 新規投稿の場合
            if(empty($_POST["edit_id"])){
                // データベースに挿入
                $sql = $pdo->prepare("INSERT INTO tbtest (name, comment, date, word) VALUES (:name, :comment, :date, :word)");
                $sql->bindParam(':name', $name, PDO::PARAM_STR);
                $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql->bindParam(':date', $date, PDO::PARAM_STR);
                $sql->bindParam(':word', $password, PDO::PARAM_STR);
                $sql->execute();
            } else {
                // 編集完了の場合
                $edit_id = $_POST["edit_id"];
                
                 //パスワードを取得
                $sql = 'SELECT * FROM tbtest WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindparam('id', $edit_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch();
                $stored_password = $result['word'];
                
            if($_POST["password"] === $stored_password){
                
                
                $sql = 'UPDATE tbtest SET name=:name, comment=:comment, word=:word WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':word', $password, PDO::PARAM_STR);
                $stmt->bindParam(':id', $edit_id, PDO::PARAM_INT);
                $stmt->execute();
             
               
            }
        }
    }
    }
    
    // 削除したとき
    if(isset($_POST["delete"]) && isset($_POST["delete_num"])) {
        $delete_num = $_POST["delete_num"];
        
                
                //パスワードを取得
                $sql = 'SELECT * FROM tbtest WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindparam('id', $delete_num, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch();
                $stored_password = $result['word'];
                
            if($_POST["delete_pass"] === $stored_password){
                
        $sql = 'DELETE FROM tbtest WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $delete_num, PDO::PARAM_INT);
        $stmt->execute();
                }
    }
    ?>
    
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php echo $edit_name; ?>">
        <input type="text" name="comment" placeholder="コメント" value="<?php echo $edit_comment; ?>">
        <input type="number" name="password" placeholder="パスワード">
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
        <input type="submit" name="submit"> <br>
        <input type="number" name="delete_num" placeholder="削除対象投稿番号">
        <input type="text" name="delete_pass" placeholder="パスワード">
        <input type="submit" name="delete" value="削除"> <br>
        <input type="number" name="edit_num" placeholder="編集対象番号">
        <input type="submit" name="edit" value="編集">
        
    </form>
    
    <?php
    // データベース内の投稿を表示
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'] . ',';
        echo $row['name'] . ',';
        echo $row['comment'] . ',';
        echo $row['date'] . '<br>';
        echo "<hr>";
    }
    ?>
</body>
</html>