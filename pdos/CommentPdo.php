<?php

    function getComments($no){
       $pdo = pdoSqlConnect();
       $query = "SELECT (no, parent, contents, 
                    (SELECT nickName FROM everytime.User WHERE id=userId) nickName, createdAt,
                    (SELECT COUNT(no) FROM everytime.CommentLike WHERE commentNo=Comment.no) likeCnt 
                    )
                FROM everytime.Comment
                WHERE isDeleted='N' and boardNo=? and parent is NULL
                GROUP BY boardNo, no, createdAt, parent, contents, likeCnt;";

       $st = $pdo->prepare($query);
       $st->execute([$no]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();

       $st = null;
       $pdo = null;

       return $res;
    }


    function getComments($no){
        $pdo = pdoSqlConnect();

        $query = "DROP PROCEDURE IF EXISTS testReply;
                CREATE PROCEDURE testReply(IN cNo INT)
                BEGIN
                    SELECT GROUP_CONCAT('{<br>', jsonitem, '<br>}' SEPARATOR ',<br>') AS jsonarray 
                    FROM (
                        SELECT CONCAT (
                            '&emsp;', '\"no\": ', no, ',<br>'
                            '&emsp;', '\"contents\": ', '\"', contents, '\"', ',<br>' 
                            '&emsp;', '\"nickName\": ', '\"', (SELECT nickName FROM everytime.User WHERE id=userId), '\"', ',<br>' 
                            '&emsp;', '\"likeCnt\": ', (SELECT COUNT(no) FROM everytime.CommentLike WHERE commentNo=Comment.no), ',<br>' 
                            '&emsp;', '\"createdAt\": ', '\"', createdAt, '\"'
                            '&emsp;', '\"recomments\": ', (
                                SELECT GROUP_CONCAT('{<br>', jsonitemR, '<br>}' SEPARATOR ',<br>') AS jsonarrayR FROM (
                                    SELECT CONCAT (
                                        '&emsp;', '\"no\": ', no, ',<br>'
                                        '&emsp;', '\"parent\": ', parent, ',<br>'
                                        '&emsp;', '\"contents\": ', '\"', contents, '\"', ',<br>' 
                                        '&emsp;', '\"nickName\": ', '\"', (SELECT nickName FROM everytime.User WHERE id=userId), '\"', ',<br>' 
                                        '&emsp;', '\"likeCnt\": ', (SELECT COUNT(no) FROM everytime.CommentLike WHERE commentNo=Comment.no), ',<br>' 
                                        '&emsp;', '\"createdAt\": ', '\"', createdAt, '\"'
                                    ) AS jsonitemR
                                    FROM everytime.Comment
                                    WHERE isDeleted='N' and boardNo=? and parent=cNo
                                ) AS singlejsonR
                            )
                        ) AS jsonitem
                        FROM everytime.Comment
                        WHERE isDeleted='N' and boardNo=? and parent is NULL and no=cNo
                    ) AS singlejson;
                END;
                CALL testReply(5);";

       $st = $pdo->prepare($query);
       $st->execute([$no]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();

       $st = null;
       $pdo = null;

       return $res;
    }

    function uploadComment($contents, $userId, $boardNo, $parent){
       $pdo = pdoSqlConnect();
       $query = "INSERT INTO Comment (contents, userId, boardNo, parent) VALUES (?, ?, ?, ?);";

       $st = $pdo->prepare($query);
       $st->execute([$contents, $userId, $boardNo, $parent]);

       $st = null;
       $pdo = null;
   }

    function deleteComment($no){
       $pdo = pdoSqlConnect();
       $query = "UPDATE Comment SET isDeleted='Y' WHERE no=?;";

       $st = $pdo->prepare($query);
       $st->execute([$no]);

       $st = null;
       $pdo = null;
   }

    function likeComment($commentNo, $userId){
       $pdo = pdoSqlConnect();
       $query = "INSERT INTO CommentLike (commentNo, userId) VALUES (?, ?);";

       $st = $pdo->prepare($query);
       $st->execute([$commentNo, $userId]);

       $st = null;
       $pdo = null;
   }

    function cancelLikeComment($commentNo, $userId){
       $pdo = pdoSqlConnect();
       $query = "DELETE FROM CommentLike WHERE commentNo=? AND userId=?;";

       $st = $pdo->prepare($query);
       $st->execute([$commentNo, $userId]);

       $st = null;
       $pdo = null;
   }