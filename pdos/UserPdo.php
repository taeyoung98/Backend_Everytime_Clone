<?php

    function createUser($id, $pw, $name, $nick, $univ, $stuNo, $email){
       $pdo = pdoSqlConnect();
       $query = "INSERT INTO User (id, password, name, nickName, university, stuNo, email) VALUES (?, ?, ?, ?, (SELECT no FROM University WHERE name=?), ?, ?);";

       $st = $pdo->prepare($query);
       $st->execute([$id, $pw, $name, $nick, $univ, $stuNo, $email]);

       $st = null; $pdo = null;
   }

   function isValidNickname($nick, $univ){
        $pdo = pdoSqlConnect();
        $query = "SELECT EXISTS(SELECT * FROM User WHERE nickName = ? AND university = (SELECT no FROM University WHERE name = ?)) AS exist;";

        $st = $pdo->prepare($query);
        $st->execute([$nick, $univ]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $st=null; $pdo = null;

        return intval($res[0]["exist"]);
    }

    function isValidSchool($univ){
        $pdo = pdoSqlConnect();
        $query = "SELECT EXISTS(SELECT * FROM University WHERE name = ?) AS exist;";

        $st = $pdo->prepare($query);
        $st->execute([$univ]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $st=null; $pdo = null;

        return intval($res[0]["exist"]);
    }

    function isValidStuNo($stuNo, $univ){
        $pdo = pdoSqlConnect();
        $query = "SELECT EXISTS(SELECT * FROM User WHERE stuNo = ? AND university = (SELECT no FROM University WHERE name = ?)) AS exist;";

        $st = $pdo->prepare($query);
        $st->execute([$stuNo, $univ]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $st=null; $pdo = null;

        return intval($res[0]["exist"]);
    }

    function isValidEmail($email){
        $pdo = pdoSqlConnect();
        $query = "SELECT EXISTS(SELECT * FROM User WHERE email = ?) AS exist;";

        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $st=null; $pdo = null;

        return intval($res[0]["exist"]);
    }

    function deleteUser($id){
       $pdo = pdoSqlConnect();
       $query = "UPDATE User SET isDeleted='Y' WHERE userId = ?;";

       $st = $pdo->prepare($query);
       $st->execute([$id]);

       $st = null;
       $pdo = null;
   }

    function changePassword($newPassword, $id){
       $pdo = pdoSqlConnect();
       $query = "UPDATE User SET password = ? WHERE (id = ?);";

       $st = $pdo->prepare($query);
       $st->execute([$newPassword, $id]);

       $st = null; $pdo = null;
   }

    function changeEmail($newEmail, $id){
       $pdo = pdoSqlConnect();
       $query = "UPDATE User SET email = ? WHERE (id = ?);";

       $st = $pdo->prepare($query);
       $st->execute([$newEmail, $id]);

       $st = null; $pdo = null;
   }

    function changeNickname($newNickname, $id){
       $pdo = pdoSqlConnect();
       $query = "UPDATE User SET nickName = ? WHERE (id = ?);";

       $st = $pdo->prepare($query);
       $st->execute([$newNickname, $id]);

       $st = null; $pdo = null;
   }

    function getMyProfile($id){
       $pdo = pdoSqlConnect();
       $query = "SELECT id, name, nickName, (SELECT name FROM University WHERE no=User.university) university, stuNo, email FROM User WHERE id = ?;";

       $st = $pdo->prepare($query);
       $st->execute([$id]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();

       $st = null; $pdo = null;

       return $res[0];
   }

    function getMyPosts($id){
       $pdo = pdoSqlConnect();
       $query = "SELECT no, 
            (SELECT type FROM BoardCategory WHERE no=Board.type) category,
            title, contents,  
            (SELECT COUNT(no) FROM `Like` WHERE boardNo=Board.no) likeCnt, 
            (SELECT COUNT(no) FROM Comment WHERE boardNo=Board.no) commentCnt, 
            createdAt 
        FROM Board 
        WHERE userId=? AND isDeleted='N'
        ORDER BY createdAt DESC;";

       $st = $pdo->prepare($query);
       $st->execute([$id]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();

       $st = null; $pdo = null;

       return $res;
   }

    function getMyCommentPosts($id){
       $pdo = pdoSqlConnect();
       $query = "SELECT no, 
            (SELECT type FROM BoardCategory WHERE no=Board.type) category, 
            title, contents, 
            (SELECT nickName FROM User WHERE id=Board.userId) nickName,  
            (SELECT COUNT(no) FROM `Like` WHERE boardNo=Board.no) likeCnt, 
            (SELECT COUNT(no) FROM Comment WHERE boardNo=Board.no) commentCnt, 
            createdAt  
        FROM Board 
        WHERE no IN (SELECT boardNo FROM Comment WHERE userId=?) 
            AND isDeleted='N'
        ORDER BY createdAt DESC;";

       $st = $pdo->prepare($query);
       $st->execute([$id]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();

       $st = null; $pdo = null;

       return $res;
   }

    function getMyScraps($id){
       $pdo = pdoSqlConnect();
       $query = "SELECT no, 
            (SELECT type FROM BoardCategory WHERE no=Board.type) category, 
            title, contents, 
            (SELECT nickName FROM User WHERE id=Board.userId) nickName,  
            (SELECT COUNT(no) FROM `Like` WHERE boardNo=Board.no) likeCnt, 
            (SELECT COUNT(no) FROM Comment WHERE boardNo=Board.no) commentCnt, 
            createdAt  
        FROM Board 
        WHERE no IN (SELECT boardNo FROM Scrap WHERE userId=?) 
            AND isDeleted='N'
        ORDER BY createdAt DESC;";

       $st = $pdo->prepare($query);
       $st->execute([$id]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();

       $st = null; $pdo = null;

       return $res;
   }

    function getUsersPerSchool($school){
       $pdo = pdoSqlConnect();
       $query = "SELECT univ.name 'university', usr.id, usr.name, usr.nickName, usr.stuNo, usr.email
                FROM User AS usr 
                    RIGHT OUTER JOIN University AS univ 
                    ON usr.university = univ.no
                WHERE univ.name = ? AND usr.isDeleted = 'N'
                GROUP BY univ.name, usr.id, usr.name, usr.nickName, usr.stuNo, usr.email;";

       $st = $pdo->prepare($query);
       $st->execute([$school]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();

       $st = null; $pdo = null;

       return $res;
   }

