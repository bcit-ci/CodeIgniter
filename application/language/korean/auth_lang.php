<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Korean
*
* Author: Yoon, Seongsu
* 		  sople1@snooey.net
*         @sople1
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  2013-07-03
*
* Description:  Korean language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = '폼 전송값이 보안 검사를 통과하지 못했습니다.';

// Login
$lang['login_heading']         = '로그인';
$lang['login_subheading']      = '이메일이나 계정명으로 로그인 하세요.';
$lang['login_identity_label']  = '이메일/계정명:';
$lang['login_password_label']  = '비밀번호:';
$lang['login_remember_label']  = '기억하기:';
$lang['login_submit_btn']      = '로그인';
$lang['login_forgot_password'] = '비밀번호를 잊으셨습니까?';

// Index
$lang['index_heading']           = '사용자';
$lang['index_subheading']        = '사용자 목록입니다.';
$lang['index_fname_th']          = '이름';
$lang['index_lname_th']          = '성';
$lang['index_email_th']          = '이메일';
$lang['index_groups_th']         = '그룹';
$lang['index_status_th']         = '상태';
$lang['index_action_th']         = '활동';
$lang['index_active_link']       = '활성화';
$lang['index_inactive_link']     = '비활성화';
$lang['index_create_user_link']  = '새 사용자를 만듭니다';
$lang['index_create_group_link'] = '새 그룹을 만듭니다';

// Deactivate User
$lang['deactivate_heading']                  = '사용자 비활성화';
$lang['deactivate_subheading']               = '\'%s\' 사용자를 비활성화 하시겠습니까?';
$lang['deactivate_confirm_y_label']          = '예:';
$lang['deactivate_confirm_n_label']          = '아니요:';
$lang['deactivate_submit_btn']               = '전송';
$lang['deactivate_validation_confirm_label'] = '정보 확인';
$lang['deactivate_validation_user_id_label'] = '사용자 ID';

// Create User
$lang['create_user_heading']                           = '사용자 만들기';
$lang['create_user_subheading']                        = '사용자 정보를 입력해 주세요.';
$lang['create_user_fname_label']                       = '이름:';
$lang['create_user_lname_label']                       = '성:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = '회사명:';
$lang['create_user_email_label']                       = '이메일:';
$lang['create_user_phone_label']                       = '전화번호:';
$lang['create_user_password_label']                    = '비밀번호:';
$lang['create_user_password_confirm_label']            = '비밀번호 재입력:';
$lang['create_user_submit_btn']                        = '사용자 만들기';
$lang['create_user_validation_fname_label']            = '이름';
$lang['create_user_validation_lname_label']            = '성';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = '이메일 주소';
$lang['create_user_validation_phone1_label']           = '전화번호 앞자리';
$lang['create_user_validation_phone2_label']           = '전화번호 중간자리';
$lang['create_user_validation_phone3_label']           = '전화번호 마지막자리';
$lang['create_user_validation_company_label']          = '회사명';
$lang['create_user_validation_password_label']         = '비밀번호';
$lang['create_user_validation_password_confirm_label'] = '비밀번호 재입력';

// Edit User
$lang['edit_user_heading']                           = '사용자 정보 수정';
$lang['edit_user_subheading']                        = '사용자 정보를 입력해 주세요.';
$lang['edit_user_fname_label']                       = '이름:';
$lang['edit_user_lname_label']                       = '성:';
$lang['edit_user_company_label']                     = '회사명:';
$lang['edit_user_email_label']                       = '이메일:';
$lang['edit_user_phone_label']                       = '전화번호:';
$lang['edit_user_password_label']                    = '비밀번호: (바꾸려면 입력)';
$lang['edit_user_password_confirm_label']            = '비밀번호 재입력: (바꾸려면 입력)';
$lang['edit_user_groups_heading']                    = '소속 그룹';
$lang['edit_user_submit_btn']                        = '정보 저장';
$lang['edit_user_validation_fname_label']            = '이름';
$lang['edit_user_validation_lname_label']            = '성';
$lang['edit_user_validation_email_label']            = '이메일 주소';
$lang['edit_user_validation_phone1_label']           = '전화번호 앞자리';
$lang['edit_user_validation_phone2_label']           = '전화번호 중간자리';
$lang['edit_user_validation_phone3_label']           = '전화번호 마지막자리';
$lang['edit_user_validation_company_label']          = '회사명';
$lang['edit_user_validation_groups_label']           = '그룹';
$lang['edit_user_validation_password_label']         = '비밀번호';
$lang['edit_user_validation_password_confirm_label'] = '비밀번호 입력';

// Create Group
$lang['create_group_title']                  = '그룹 만들기';
$lang['create_group_heading']                = '그룹 만들기';
$lang['create_group_subheading']             = '그룹 정보를 입력하세요.';
$lang['create_group_name_label']             = '그룹명:';
$lang['create_group_desc_label']             = '설명:';
$lang['create_group_submit_btn']             = '그룹 만들기';
$lang['create_group_validation_name_label']  = '그룹명';
$lang['create_group_validation_desc_label']  = '설명';

// Edit Group
$lang['edit_group_title']                  = '그룹 정보 수정';
$lang['edit_group_saved']                  = '그룹이 저장되었습니다';
$lang['edit_group_heading']                = '그룹 정보 수정';
$lang['edit_group_subheading']             = '그룹 정보를 입력해 주세요.';
$lang['edit_group_name_label']             = '그룹명:';
$lang['edit_group_desc_label']             = '설명:';
$lang['edit_group_submit_btn']             = '정보 저장';
$lang['edit_group_validation_name_label']  = '그룹명';
$lang['edit_group_validation_desc_label']  = '설명';

// Change Password
$lang['change_password_heading']                               = '비밀번호 바꾸기';
$lang['change_password_old_password_label']                    = '옛 비밀번호:';
$lang['change_password_new_password_label']                    = '새 비밀번호 (최소 %s 자 이상):';
$lang['change_password_new_password_confirm_label']            = '새 비밀번호 재입력:';
$lang['change_password_submit_btn']                            = '바꾸기';
$lang['change_password_validation_old_password_label']         = '옛 비밀번호';
$lang['change_password_validation_new_password_label']         = '새 비밀번호';
$lang['change_password_validation_new_password_confirm_label'] = '새 비밀번호 재입력';

// Forgot Password
$lang['forgot_password_heading']                 = '비밀번호 찾기';
$lang['forgot_password_subheading']              = '비밀번호를 찾으려면 %s을 입력하세요. 재설정 방법을 보내드립니다.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = '보내기';
$lang['forgot_password_validation_email_label']  = '이메일 주소';
$lang['forgot_password_username_identity_label'] = '계정명';
$lang['forgot_password_email_identity_label']    = '이메일';
$lang['forgot_password_email_not_found']         = 'No record of that email address.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = '비밀번호 바꾸기';
$lang['reset_password_new_password_label']                    = '새 비밀번호 (최소 %s 자 이상):';
$lang['reset_password_new_password_confirm_label']            = '새 비밀번호 재입력:';
$lang['reset_password_submit_btn']                            = '바꾸기';
$lang['reset_password_validation_new_password_label']         = '새 비밀번호';
$lang['reset_password_validation_new_password_confirm_label'] = '새 비밀번호 재입력';

// Activation Email
$lang['email_activate_heading']    = '%s 계정 활성화';
$lang['email_activate_subheading'] = '다음 링크를 클릭하여 %s로 접근해 주십시오.';
$lang['email_activate_link']       = '계정 활성화';

// Forgot Password Email
$lang['email_forgot_password_heading']    = '%s 계정 비밀번호 변경';
$lang['email_forgot_password_subheading'] = '다음 링크를 클릭하여 %s로 접근해 주십시오.';
$lang['email_forgot_password_link']       = '비밀번호 변경';


