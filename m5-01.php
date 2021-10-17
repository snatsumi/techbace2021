<!DOCTYPE html> 
<html>
<html lang= "ja">
<head>
   <meta charset = "utf-8">
   <title>mission_5-1</title>
</head>
<body>
<?php    
    //データベースへの接続処理
    $dsn = "データベース名";
    $user = "ユーザー名";
    $password = "パスワード";
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //存在しない場合テーブルを作成する処理
    $sql = "CREATE TABLE IF NOT EXISTS tb_chat_5"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"
    ."comment TEXT,"
    ."date DATETIME,"
    ."pass char(32)"
    .");";
    $stmt = $pdo -> query($sql);  

    $date = date("Y/m/d/ H:i:s");
    $get_name = "";
    $get_comment = "";
    $get_pass = "";
    $get_id = "";

    //メインの処理
    if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && empty($_POST["hidden"])){
        $name = ($_POST["name"]);
        $comment = ($_POST["comment"]);
        $new_pass = ($_POST["pass"]);
        newPost($pdo, $name, $comment, $date, $new_pass);
        showPost($pdo);
    } elseif (!empty($_POST["delete_num"]) && !empty($_POST["delete_pass"])){
        $delete_num = ($_POST["delete_num"]);
        $delete_pass = ($_POST["delete_pass"]);
        deletePost($pdo, $delete_num, $delete_pass);
        showPost($pdo);
    } elseif (!empty($_POST["edit_num"]) && !empty($_POST["edit_pass"])){
        $edit_num = ($_POST["edit_num"]);
        $edit_pass = $_POST["edit_pass"];
        getPost($pdo, $edit_num, $edit_pass);
        showPost($pdo);
    } elseif (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && !empty($_POST["hidden"])){
        $edit_num = ($_POST["hidden"]);
        $name = ($_POST["name"]);
        $comment = ($_POST["comment"]);
        $new_pass = ($_POST["pass"]);
        editPost($pdo, $edit_num, $name, $comment, $date, $new_pass);
        showPost($pdo);
    } 
    //新規投稿の処理
    function newPost($pdo, $name, $comment, $date, $pass){
        $sql = $pdo->prepare("INSERT INTO tb_chat_5(name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
        $sql -> bindParam(":name", $name, PDO::PARAM_STR);
        $sql -> bindParam(":comment", $comment, PDO::PARAM_STR);
        $sql -> bindParam(":date", $date, PDO::PARAM_STR);
        $sql -> bindParam(":pass", $pass, PDO::PARAM_STR);
        $sql -> execute();
        
        echo "新規投稿を受け付けました。<br>"; 
    }
    //削除の処理
    function deletePost($pdo, $delete_num, $pass){
        $stmt = $pdo->prepare("SELECT pass FROM tb_chat_5 WHERE id=:num");
        $stmt -> bindParam(":num", $delete_num, PDO::PARAM_INT);
        $stmt -> execute();
        $results = $stmt->fetchAll(); 
        $saved_pass = $results[0][0];

        if ( !empty($saved_pass)){
            if ($pass == $saved_pass){
                $sql = "DELETE FROM tb_chat_5 WHERE id=:num";
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(":num", $delete_num, PDO::PARAM_INT);
                $stmt -> execute();

                echo "投稿の削除を受け付けました<br><br>";
            }
        }   else {
            echo "指定された番号は存在しません<br>";
        }
    }
    
    //編集フォームに入力した番号の投稿を入力フォームに表示する処理
    function getPost($pdo, $edit_num, $pass){
        global $get_id;
        global $get_name;
        global $get_comment;
        global $get_pass;
        $stmt = $pdo -> prepare("SELECT * FROM tb_chat_5 WHERE id=:num");
        $stmt -> bindParam(":num", $edit_num, PDO::PARAM_INT);
        $stmt -> execute();
        $results = $stmt -> fetchAll();
        $saved_pass = $results[0][4];

        if ( !empty($saved_pass)){
            if ($pass == $saved_pass) {
                $get_id = $results[0][0];
                $get_name = $results[0][1];
                $get_comment = $results[0][2];
                $get_pass = $results[0][4];
            }
        }
    }
    
    //編集の処理(編集したものを表示)
    function editPost($pdo, $id, $name, $comment, $date, $pass){
        $sql = "UPDATE tb_chat_5 SET name=:name,comment=:comment, date=:date, pass=:pass WHERE id=:id";
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindParam(":name", $name, PDO::PARAM_STR);
        $stmt -> bindParam(":comment", $comment, PDO::PARAM_STR);
        $stmt -> bindParam(":date", $date, PDO::PARAM_STR);
        $stmt -> bindParam(":pass", $pass, PDO::PARAM_STR);
        $stmt -> bindParam(":id", $id, PDO::PARAM_INT);
        $stmt -> execute();

        echo "編集を受け付けました。<br>";
        }

    //投稿の内容を表示させる処理
    function showPost($pdo){
        echo "<strong>【投稿一覧】</strong><br>";
        $sql = "SELECT * FROM tb_chat_5";
        $stmt = $pdo -> query($sql); 
        $results = $stmt -> fetchAll();
        foreach ( $results as $row ) {
          echo $row["id"]." ".$row["name"]." ".$row["comment"]." ".$row["date"]."<br>";
        }
    }    
?>
<form method ="post" action ="">
<hr>
<h1 class="midashi_1" style="color:aqua;"> 🌸掲示板🌸 </h1>   
<strong>◎入力フォーム◎</strong><br>
 <input type = "text" name = "name" placeholder = "名前" value ="<?=$get_name ?>"><br>
 <input type = "text" name = "comment" placeholder = "コメント" value ="<?=$get_comment?>"><br>
 <input type = "password" name = "pass" placeholder = "パスワード" value ="<?=$get_pass?>"><br>
 <input type = "hidden" name = "hidden" value ="<?=$get_id?>">
 <input type = "submit"><br>
 <br>
 <strong>◎削除フォーム◎</strong><br>
 <input type = "text" name = "delete_num" placeholder = "削除対象番号"><br>
 <input type = "password" name = "delete_pass" placeholder = "パスワード"><br>
 <input type = "submit" name = "delete" value = "削除"><br>
 <br>
 <strong>◎編集フォーム◎</strong><br>
 <input type = "text" name = "edit_num" placeholder = "編集対象番号"><br>
 <input type = "password" name = "edit_pass" placeholder = "パスワード"><br>
 <input type = "submit" name = "edit" value = "編集">
</form>
</body>
</html>