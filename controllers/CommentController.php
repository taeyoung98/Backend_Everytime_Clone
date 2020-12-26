<?php
require 'function.php';

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;

        // todo: 공백 안됨

        /*
         * API No. 31
         * API Name : 댓글 조회 API
         * 마지막 수정 날짜 : 20.12.12
         */
        case "showComments":
            http_response_code(200);
            $res->result = getComments($vars["no"]);
            $res->isSuccess = TRUE;
            $res->code = 310;
            $res->message = "댓글 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 32
         * API Name : 댓글 작성 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "uploadComment":
            http_response_code(201);
            $res->result = uploadComment($req->contents, $req->userId, $req->boardNo, $req->parent);
            $res->isSuccess = TRUE;
            $res->code = 320;
            $res->message = "댓글 업로드 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 33
         * API Name : 댓글 삭제 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "deleteComment":
            $no = 9;
            http_response_code(201);
            $res->result = deleteComment($no);
            $res->isSuccess = TRUE;
            $res->code = 330;
            $res->message = "댓글 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 34
         * API Name : 댓글 공감 API
         * 마지막 수정 날짜 : 20.12.05
         */
       case "likeComment":
            http_response_code(201);
            $res->result = likeComment($req->commentNo, $req->userId);
            $res->isSuccess = TRUE;
            $res->code = 340;
            $res->message = "댓글 공감 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 35
         * API Name : 댓글 공감 해제 API
         * 마지막 수정 날짜 : 20.12.05
         */
       case "likeCommentOff":
            $commentNo = 7;
            $userId = "junghoon";
            http_response_code(200);
            $res->result = cancelLikeComment($commentNo, $userId);
            $res->isSuccess = TRUE;
            $res->code = 350;
            $res->message = "댓글 공감 해제";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
