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
         * API No. 20
         * API Name : 글 작성 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "uploadPost":
            // todo: type=2 비밀게시판이면 title=null
            http_response_code(201);
            $res->result = uploadPost($req->title, $req->contents, $req->userId, $req->type);
            $res->isSuccess = TRUE;
            $res->code = 200;
            $res->message = "글 업로드 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 21
         * API Name : 글 수정 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "editPost":
            // todo: type=2 비밀게시판이면 title=null
            // $req->type
            http_response_code(201);
            $res->result = editPost($req->newTitle, $req->newContents, $req->no);
            $res->isSuccess = TRUE;
            $res->code = 210;
            $res->message = "글 수정 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 22
         * API Name : 글 삭제 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "deletePost":
            $no = 8;
            http_response_code(201);
            $res->result = deletePost($no);
            $res->isSuccess = TRUE;
            $res->code = 220;
            $res->message = "글 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 23
         * API Name : 글 공감 API
         * 마지막 수정 날짜 : 20.12.05
         */
       case "likePost":
            http_response_code(201);
            $res->result = likePost($req->boardNo, $req->userId);
            $res->isSuccess = TRUE;
            $res->code = 230;
            $res->message = "글 공감 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 24
         * API Name : 글 공감 해제 API
         * 마지막 수정 날짜 : 20.12.05
         */
       case "likePostOff":
            $boardNo = 3;
            $userId = "junghoon";
            http_response_code(200);
            $res->result = cancelLikePost($boardNo, $userId);
            $res->isSuccess = TRUE;
            $res->code = 240;
            $res->message = "글 공감 해제";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 25
         * API Name : 글 스크랩 API
         * 마지막 수정 날짜 : 20.12.05
         */
       case "scrapPost":
            http_response_code(201);
            $res->result = scrapPost($req->boardNo, $req->userId);
            $res->isSuccess = TRUE;
            $res->code = 250;
            $res->message = "글 스크랩 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 26
         * API Name : 글 스크랩 해제 API
         * 마지막 수정 날짜 : 20.12.05
         */
       case "scrapPostOff":
            $boardNo = 3;
            $userId = "junghoon";
            http_response_code(200);
            $res->result = cancelScrapPost($boardNo, $userId);
            $res->isSuccess = TRUE;
            $res->code = 260;
            $res->message = "글 스크랩 해제";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 27
         * API Name : 글 조회 API
         * 마지막 수정 날짜 : 20.12.05
         */
       case "showApost":
            http_response_code(201);
            $res->result = getApost($vars["no"]);
            // $res->result->comment = getComments($vars["no"]);
            $res->result["comments"] = getComments($vars["no"]);
            // todo: result->comment How?
            // todo: 대댓글 표현 방법
            $res->isSuccess = TRUE;
            $res->code = 270;
            $res->message = "글 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 28
         * API Name : 게시판별 글 조회 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "showListByBoard":
            $category = $_GET['category'];
            $search = $_GET['search'];

            http_response_code(200);

            if($search){
                
                $res->result = searchPost($search);
                $res->isSuccess = TRUE;
                $res->code = 290;
                $res->message = "글 검색 조회";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($category == "hot"){
                $res->result = getHotBoard();
            }
            else if($category == "best"){
                $res->result = getBestBoard();
            }
            else {
                $res->result = getPostsByBoard($category);
            }
            $res->isSuccess = TRUE;
            $res->code = 280;
            $res->message = "$category 게시판 글 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
                

        /*
         * API No. 29
         * API Name : 글 검색 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "searchPost":
            $search = $_GET['search'];
            http_response_code(200);
            $res->result = searchPost($search);
            $res->isSuccess = TRUE;
            $res->code = 290;
            $res->message = "글 검색 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;  

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
