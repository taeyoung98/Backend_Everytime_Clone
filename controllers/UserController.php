<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

// todo
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));

try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
        * API No. 8
        * API Name : 회원 가입 API
        * 마지막 수정 날짜 : 20.12.12
        */
        case "signUp":
            $id = $req->id;
            $pw = $req->password;
            $name = $req->name;
            $nick = $req->nickName;
            $univ = $req->university;
            $stuNo = $req->stuNo;
            $email = $req->email;
            
            // 공백
            if(empty($id)||empty($pw)||empty($name)||empty($nick)||empty($univ)||empty($stuNo)||empty($email)) {
                $res->isSuccess = FALSE;
                $res->code = 0;
                $res->message = "공백이 입력됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            // id 4미만 10초과 
            if(!preg_match_all('/^[0-9a-zA-Z]{4,10}$/', $id, $match_81)) {
                $res->isSuccess = FALSE;
                $res->code = 81;
                $res->message = "ID 글자수는 4~10입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            // pw 6미만 18초과
            if(!preg_match_all('/^[0-9a-zA-Z]{6,18}$/', $pw, $match_82)){
                $res->isSuccess = FALSE;
                $res->code = 82;
                $res->message = "비밀번호 글자수는 6~18입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            // todo: 83. password 영어 대소문자 숫자 기호 혼합인지

            // DB에 존재하지 않는 학교
            if(!isValidSchool($univ)){
                $res->isSuccess = FALSE;
                $res->code = 84;
                $res->message = "미등록된 학교이거나 입력이 잘못됐습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            // 한 학교에 같은 닉네임 중복
            if(isValidNickName($nick, $univ)){
                $res->isSuccess = FALSE;
                $res->code = 85;
                $res->message = "이 닉네임은 누군가 사용중입니다.";
                $res->nick = $nick;
                $res->univ = $univ;
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            // 학번 숫자로만 딱 9자리 아닐 때
            if(!preg_match_all('/^[0-9]{9}$/', $stuNo, $match_86)){
                $res->isSuccess = FALSE;
                $res->code = 86;
                $res->message = "학번(stuNo)은 숫자로만 9자리 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            // 한 학교에 같은 학번 중복
            if(isValidStuNo($stuNo, $univ)){
                $res->isSuccess = FALSE;
                $res->code = 87;
                $res->message = "이미 가입된 학번(stuNo)입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            // email 형식 부합X
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $res->isSuccess = FALSE;
                $res->code = 88;
                $res->message = "{$email}은 유효한 형식의 이메일 주소가 아닙니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            // email 중복
            if (isValidEmail($email)) {
                $res->isSuccess = FALSE;
                $res->code = 89;
                $res->message = "이미 가입된 이메일입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            
            http_response_code(201);
            $res->result = createUser($id, $pw, $name, $nick, $univ, $stuNo, $email);
            $res->isSuccess = TRUE;
            $res->code = 80;
            $res->message = "회원 가입 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 9
         * API Name : 회원 탈퇴 API
         * 마지막 수정 날짜 : 20.12.12
         */
        case "withdraw":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 91;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            http_response_code(200);
            $res->result = deleteUser($id);
            $res->isSuccess = TRUE;
            $res->code = 90;
            $res->message = "계정 탈퇴 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);

            // todo: 해당 id의 jwt token 무력화 How?
            break;
             
       /*
        * API No. 10
        * API Name : 사용자 로그인 API
        * 마지막 수정 날짜 : 20.12.12
        */
        case "signIn":
            $id = $req->id;
            $pw = $req->password;

            if(!isValidUser($id, $pw)){
                $res->isSuccess = FALSE;
                $res->code = 101;
                $res->message = "유효하지 않은 아이디 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            http_response_code(201);
            $jwt = getJWToken($id, $pw, JWT_SECRET_KEY);
            $res->result->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "로그인 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        
        /*
         * API No. 11
         * API Name : 사용자 로그아웃 API
         * 마지막 수정 날짜 : 20.12.12
         */
        case "signOut":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 91;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 110;
            $res->message = "로그아웃 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);

            // todo: 해당 id의 jwt token 무력화 How?
            break;

        /*
         * API No. 12
         * API Name : 비밀번호 변경 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "changePassword":
            // todo: password가 일치
            // todo: 영어숫자 혼합 8자 이상
            // $req->password
            http_response_code(200);
            $res->result = changePassword($req->newPassword, $req->id);
            $res->isSuccess = TRUE;
            $res->code = 120;
            $res->message = "비밀번호 변경 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 13
         * API Name : 이메일 변경 API
         * 마지막 수정 날짜 : 20.12.05
         */
       case "changeEmail":
            // todo: password가 일치
            // todo: 이메일 형식 부합
            // todo: 기존에 존재X
            // $req->password
            http_response_code(200);
            $res->result = changeEmail($req->newEmail, $req->id);
            $res->isSuccess = TRUE;
            $res->code = 130;
            $res->message = "이메일 변경 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 14
         * API Name : 닉네임 설정 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "changeNickname":
            // todo: 기존에 존재X
            // todo: updatedAt 30일 이내면 X
            http_response_code(200);
            $res->result = changeNickname($req->newNickname, $req->id);
            $res->isSuccess = TRUE;
            $res->code = 140;
            $res->message = "닉네임 설정 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 15
         * API Name : 내 프로필 조회 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "showMyProfile":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 91;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            http_response_code(201);
            $res->result = getMyProfile($id);
            $res->isSuccess = TRUE;
            $res->code = 150;
            $res->message = "내 프로필 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 16
         * API Name : 내가 쓴 글 조회 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "showMyPosts":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 91;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            http_response_code(201);
            $res->result = getMyPosts($id);
            $res->isSuccess = TRUE;
            $res->code = 160;
            $res->message = "내가 쓴 글 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 17
         * API Name : 댓글 단 글 조회 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "showMyCommentPosts":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 91;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            http_response_code(201);
            $res->result = getMyCommentPosts($id);
            $res->isSuccess = TRUE;
            $res->code = 170;
            $res->message = "댓글 단 글 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;  

        /*
         * API No. 18
         * API Name : 스크랩 조회 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "showMyScraps":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 91;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $id = $data->id;

            http_response_code(201);
            $res->result = getMyScraps($id);
            $res->isSuccess = TRUE;
            $res->code = 180;
            $res->message = "스크랩 조회";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 19
         * API Name : 학교별 유저 조회 API
         * 마지막 수정 날짜 : 20.12.05
         */
        case "showStudentsPerSchool":
            $school = $_GET["school"];

            // 학교 이름 틀림
            if(!isValidSchool($school)){
                $res->isSuccess = FALSE;
                $res->code = 84;
                $res->message = "{$school}은 잘못된 학교명입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            http_response_code(200);
            $res->result = getUsersPerSchool($school);
            $res->isSuccess = TRUE;
            $res->code = 190;
            $res->message = "{$school} 사용자 조회!";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}

