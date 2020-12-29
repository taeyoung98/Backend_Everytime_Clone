<?php

    function uploadPost($title, $contents, $userId, $type){
       $pdo = pdoSqlConnect();
       $query = "INSERT INTO Board (title, contents, userId, type) VALUES (?, ?, ?, (SELECT no FROM BoardCategory WHERE type=?));";

       $st = $pdo->prepare($query);
       $st->execute([$title, $contents, $userId, $type]);

       $st = null;
       $pdo = null;
   }

    function editPost($newTitle, $newContents, $no){
       $pdo = pdoSqlConnect();
       $query = "UPDATE Board SET title=?, contents=? WHERE no=?;";

       $st = $pdo->prepare($query);
       $st->execute([$newTitle, $newContents, $no]);

       $st = null;
       $pdo = null;
   }

    function deletePost($no){
       $pdo = pdoSqlConnect();
       $query = "UPDATE Board SET isDeleted='Y' WHERE no=?;";

       $st = $pdo->prepare($query);
       $st->execute([$no]);

       $st = null;
       $pdo = null;
   }

    function likePost($boardNo, $userId){
       $pdo = pdoSqlConnect();
       $query = "INSERT INTO `Like` (boardNo, userId) VALUES (?, ?);";

       $st = $pdo->prepare($query);
       $st->execute([$boardNo, $userId]);

       $st = null;
       $pdo = null;
   }

    function cancelLikePost($boardNo, $userId){
       $pdo = pdoSqlConnect();
       $query = "DELETE FROM `Like` WHERE boardNo=? AND userId=?;";

       $st = $pdo->prepare($query);
       $st->execute([$boardNo, $userId]);

       $st = null;
       $pdo = null;
   }

   function scrapPost($boardNo, $userId){
       $pdo = pdoSqlConnect();
       $query = "INSERT INTO Scrap (boardNo, userId) VALUES (?, ?);";

       $st = $pdo->prepare($query);
       $st->execute([$boardNo, $userId]);

       $st = null;
       $pdo = null;
   }

    function cancelScrapPost($boardNo, $userId){
       $pdo = pdoSqlConnect();
       $query = "DELETE FROM Scrap WHERE boardNo=? AND userId=?;";

       $st = $pdo->prepare($query);
       $st->execute([$boardNo, $userId]);

       $st = null;
       $pdo = null;
   }

    function getApost($no){
       $pdo = pdoSqlConnect();
       $query = "SELECT no, 
                    (SELECT type FROM everytime.BoardCategory WHERE no=Board.type) category,
                    title, contents, 
                    (SELECT nickName FROM everytime.User WHERE id=userId) nickName, 
                    createdAt,
                    (SELECT COUNT(no) FROM everytime.`Like` WHERE boardNo=Board.no) likeCnt, 
                    (SELECT COUNT(no) FROM everytime.Comment WHERE boardNo=Board.no) commentCnt
                FROM everytime.Board 
                WHERE isDeleted='N' and no=?;";

       $st = $pdo->prepare($query);
       $st->execute([$no]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();
       $st = null; $pdo = null;
       return $res[0];
   }

    function getPostsByBoard($category, $last){
       $limit = 3;
        
       $pdo = pdoSqlConnect();
       $query = "SELECT b.no, bc.type 'category', b.title, b.contents, 
                    (SELECT nickName FROM User WHERE id=b.userId) nickName, 
                    (SELECT COUNT(no) FROM `Like` WHERE boardNo=b.no) likeCnt, 
                    (SELECT COUNT(no) FROM Comment WHERE boardNo=b.no) commentCnt, 
                    b.createdAt
                FROM Board AS b, BoardCategory AS bc 
                WHERE b.type = bc.no AND b.isDeleted='N' AND b.type=(SELECT no FROM BoardCategory WHERE type=?) AND b.no < ?
                ORDER BY createdAt DESC
                LIMIT $limit;";

       $st = $pdo->prepare($query);
       $st->execute([$category, $last]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();
       $st = null; $pdo = null;
       return $res;
   }

    function getHotBoard(){
       $pdo = pdoSqlConnect();
       $query = "SELECT b.no, b.title, b.contents, 
                    (SELECT nickName FROM everytime.User WHERE id=b.userId) nickName, 
                    COUNT(*) AS likeCnt,  
                    (SELECT COUNT(no) FROM everytime.Comment WHERE boardNo=b.no) commentCnt, 
                    b.createdAt
                FROM everytime.Board AS b INNER JOIN everytime.`Like` AS l ON b.no=l.boardNo
                WHERE b.isDeleted='N'
                GROUP BY l.boardNo
                HAVING likeCnt > 5
                ORDER BY b.createdAt DESC;";

       $st = $pdo->prepare($query);
       $st->execute();
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();
       $st = null; $pdo = null;
       return $res;
   }

    function getBestBoard(){
       $pdo = pdoSqlConnect();
       $query = "SELECT b.no, b.title, b.contents, 
                    (SELECT nickName FROM everytime.User WHERE id=b.userId) nickName, 
                    COUNT(*) AS likeCnt,  
                    (SELECT COUNT(no) FROM everytime.Comment WHERE boardNo=b.no) commentCnt, 
                    b.createdAt
                FROM everytime.Board AS b INNER JOIN everytime.`Like` AS l ON b.no=l.boardNo
                WHERE b.isDeleted='N' AND b.createdAt BETWEEN '2020-07-01 00:00:00' AND '2020-12-31 23:59:59'
                GROUP BY l.boardNo
                HAVING likeCnt > 5
                ORDER BY likeCount DESC;";

       $st = $pdo->prepare($query);
       $st->execute();
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();
       $st = null; $pdo = null;
        return $res;
   }

    function searchPost($search){
       $pdo = pdoSqlConnect();
       $query = "SELECT * FROM everytime.Board 
                WHERE isDeleted='N' 
                    AND title LIKE '%$search%' 
                    OR contents LIKE '%$search%'
                ORDER BY createdAt DESC;";

       $st = $pdo->prepare($query);
       $st->execute([$category]);
       $st->setFetchMode(PDO::FETCH_ASSOC);
       $res = $st->fetchAll();
       $st = null; $pdo = null;
       return $res;
   }