# Everytime Restful API

## 사용 스택
- AWS LEMP server (putty)
- PHP Framework
- HTTP Rest API
- AWS RDS MySQL 
- Ajax

## 8th Missions

- [x] 보완) 공백 체크
- [x] 보완) 에러 로직-존재 유무
- [x] 보완) 에러 로직-형식 부합
- [x] 보완) GET sql 모두 'no' return
- [x] 보완) 글 조회=php(Board 조회+Comment 조회)
- [ ] 보완) API 명세서
- [ ] ajax 해보기
- [x] jwt 적용해서 api 수정하기
- [x] 무한 스크롤 필요한 api에 페이징 적용하기

## 9th Missions

- [x] 보완) 비밀번호 정규화 regex 적용 - createUser
- [-] 보완) api pagination 서버에서 컨트롤 페이징
  클라이언트에서 컨트롤 할 수 있게, 게시글의 마지막 인덱스를 params(path, query) 전달. pdo sql 함수
  클라이언트 측에서 리미트값을 입력할 수 있도록
  일단은 변수 선언해서
- [-] 보완) BoardPdo에서 댓글조회 함수 퀴리 2번 호출 res 다르게 해서 for문 depth 적용
- [x] 보완) user URI restful하게 수정, 로그아웃 api delete
- [x] 프로드서버, 테스트 서버 분리해서 구축
- [ ] ERD 설계 유튜브

## 10th Missions

- [x] third party api 실습 해보기 (Open Weather API)
- [x] AWS RDS
