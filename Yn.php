<!DOCTYPE html>

<html lang="ja">

<head>

    <meta charset="UTF-8">

    <title>mission_5-1</title>

</head>

<body>
<?php
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtable(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32),
    comment TEXT,
    dt TEXT,
    password CHAR(6),
    edi INT(10),
    edipass CHAR(6),
    del INT(10),
    delpass CHAR(6) 
    )";
    if(!empty($_POST["namae"])&&!empty($_POST["komennto"])){//名前、コメントの両方が入力されているとき
        if(!empty($_POST["pasuwa_do"])){//かつ、パスワードも入力されているとき
            if(empty($_POST["nannba"])){//新規投稿
                $stmt = $pdo->query($sql);
                $sql = $pdo -> prepare("INSERT INTO tbtable (name, comment, password, dt) VALUES (:name, :comment, :password, :dt)");//名前とコメントとパスワードの値を取得
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql -> bindParam(':dt', $datetime, PDO::PARAM_STR);
                $name=$_POST["namae"];
                $comment=$_POST["komennto"];
                $password=$_POST["pasuwa_do"];
                $datetime=date("Y/n/j H:i:s");
                $sql -> execute();
            }else{//新規投稿ではないとき＝編集の時は                    
                $id =$_POST["nannba"];//変更する投稿番号
                $name = $_POST["namae"];//変更したい名前、コメント、パスワード、更新時の日付
                $comment = $_POST["komennto"]; 
                $password=$_POST["pasuwa_do"];
                $datetime=date("Y/n/j H:i:s");
                $sql = 'UPDATE tbtable SET name=:name,comment=:comment,password=:password,dt=:dt WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
                $stmt -> bindParam(':dt', $datetime, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
    if(!empty($_POST["edit"]) && !empty($_POST["password1"])){//編集番号とパスワードが入力されているとき
        $edit=$_POST["edit"];//編集番号とパスワードを取得
        $pass1=$_POST["password1"];
        $sql = 'SELECT * FROM tbtable';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($row["id"]==$edit && $row["password"]==$pass1){//パスワードが一致したときのみ入力フォームに値を返す
                $editnumber=$row["id"];
                $editname=$row["name"];
                $editcomment=$row["comment"];
                $editpassword=$row["password"];
            }
        }
    }
    if(!empty($_POST["delete"]) && !empty($_POST["password2"])){//削除番号とパスワードが入力されているとき
        $delete=$_POST["delete"];//削除番号とパスワードを取得
        $pass2=$_POST["password2"];
        $sql = 'SELECT * FROM tbtable';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($row["id"]==$delete && $row["password"]==$pass2){
                $id = $row["id"];
                $sql = 'delete from tbtable where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    
?>
<hr><hr>
<h1>掲示板</h1>
<hr><hr>
<form action="" method="post">
        <h3>[投稿]</h3>
        名　　　　前：
        <input type="text" name="namae" placeholder="名前を入力" value="<?php if(isset($editname)){echo $editname;}?>">
        <br>
        コ　メ ン ト ：
        <input type="text" name="komennto" placeholder="コメントを入力" value="<?php if(isset($editcomment)){echo $editcomment;}?>">
        <br>
        パ ス ワード ： 
        <input type="text" name="pasuwa_do" placeholder="パスワードを入力" title="半角数字6桁まで" value="<?php if(isset($editpassword)){echo $editpassword;}?>">
        <input type="submit" name="submit">
        <input type="hidden" name="nannba" value="<?php if(isset($editnumber)){echo $editnumber;}?>">
        <br>
        <h3>[投稿編集]</h3>
        編集対象番号：
        <input type="number" name="edit" placeholder="編集対象番号を入力">
        <br>
        パ ス ワード ： 
        <input type="text" name="password1" placeholder="パスワードを入力"　title="半角数字6桁まで">
        <input type="submit" name="submit" value="編集">
        <br>
        <h3>[投稿削除]</h3>
        削除対象番号：
        <input type="number" name="delete" placeholder="削除対象番号を入力">
        <br>
        パ ス ワード ： 
        <input type="text" name="password2" placeholder="パスワードを入力"　title="半角数字6桁まで">
        <input type="submit" name="submit" value="削除">
    </form>
    <br>
    <hr>
    <h2>[投稿一覧]</h2>
<?php
    $sql = 'SELECT * FROM tbtable';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].'　';
        echo $row['name'].'　';
        echo $row['comment'].'　';
        echo $row['dt'].'<br>';
    }
?>
</body>
</html>