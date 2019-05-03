<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Japanese
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Author/Translation: Daniel Davis
*         @ourmaninjapan
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.19.2013
*
* Description:  Japanese language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'セキュリティに問題が生じ送信できませんでした。';

// Login
$lang['login_heading']         = 'ログイン';
$lang['login_subheading']      = 'メールアドレス又はユーザー名とパスワードでログインして下さい。';
$lang['login_identity_label']  = 'メールアドレス又はユーザー名：';
$lang['login_password_label']  = 'パスワード：';
$lang['login_remember_label']  = '次回から自動的にログイン：';
$lang['login_submit_btn']      = 'ログイン';
$lang['login_forgot_password'] = 'パスワードを忘れましたか？';

// Index
$lang['index_heading']           = 'ユーザー';
$lang['index_subheading']        = 'ユーザー一覧';
$lang['index_fname_th']          = '名';
$lang['index_lname_th']          = '姓';
$lang['index_email_th']          = 'メールアドレス';
$lang['index_groups_th']         = 'グループ';
$lang['index_status_th']         = '状態';
$lang['index_action_th']         = '操作';
$lang['index_active_link']       = '有効';
$lang['index_inactive_link']     = '無効';
$lang['index_create_user_link']  = 'ユーザーの新規作成';
$lang['index_create_group_link'] = 'グループの新規作成';

// Deactivate User
$lang['deactivate_heading']                  = 'ユーザーの無効化';
$lang['deactivate_subheading']               = '本当にユーザー「%s」を無効にしますか。';
$lang['deactivate_confirm_y_label']          = 'はい：';
$lang['deactivate_confirm_n_label']          = 'いいえ：';
$lang['deactivate_submit_btn']               = '送信';
$lang['deactivate_validation_confirm_label'] = '確認';
$lang['deactivate_validation_user_id_label'] = 'ユーザーID';

// Create User
$lang['create_user_heading']                           = 'ユーザーの作成';
$lang['create_user_subheading']                        = 'ユーザー情報を入力して下さい。';
$lang['create_user_fname_label']                       = '名：';
$lang['create_user_lname_label']                       = '姓：';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = '会社名：';
$lang['create_user_email_label']                       = 'メールアドレス：';
$lang['create_user_phone_label']                       = '電話番号：';
$lang['create_user_password_label']                    = 'パスワード：';
$lang['create_user_password_confirm_label']            = 'パスワード（確認用）：';
$lang['create_user_submit_btn']                        = '作成';
$lang['create_user_validation_fname_label']            = '名';
$lang['create_user_validation_lname_label']            = '姓';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'メールアドレス';
$lang['create_user_validation_phone1_label']           = '電話番号の第1部';
$lang['create_user_validation_phone2_label']           = '電話番号の第2部';
$lang['create_user_validation_phone3_label']           = '電話番号の第3部';
$lang['create_user_validation_company_label']          = '会社名';
$lang['create_user_validation_password_label']         = 'パスワード';
$lang['create_user_validation_password_confirm_label'] = 'パスワードの確認';

// Edit User
$lang['edit_user_heading']                           = 'ユーザーの編集';
$lang['edit_user_subheading']                        = 'ユーザー情報を入力して下さい。';
$lang['edit_user_fname_label']                       = '名：';
$lang['edit_user_lname_label']                       = '姓：';
$lang['edit_user_company_label']                     = '会社名：';
$lang['edit_user_email_label']                       = 'メールアドレス：';
$lang['edit_user_phone_label']                       = '電話番号：:';
$lang['edit_user_password_label']                    = 'パスワード（パスワードを変更する場合のみ）：';
$lang['edit_user_password_confirm_label']            = 'パスワードの確認（パスワードを変更する場合のみ）：';
$lang['edit_user_groups_heading']                    = '所属グループ';
$lang['edit_user_submit_btn']                        = '保存';
$lang['edit_user_validation_fname_label']            = '名';
$lang['edit_user_validation_lname_label']            = '姓';
$lang['edit_user_validation_email_label']            = 'メールアドレス';
$lang['edit_user_validation_phone1_label']           = '電話番号の第1部';
$lang['edit_user_validation_phone2_label']           = '電話番号の第2部';
$lang['edit_user_validation_phone3_label']           = '電話番号の第3部';
$lang['edit_user_validation_company_label']          = '会社名';
$lang['edit_user_validation_groups_label']           = 'グループ';
$lang['edit_user_validation_password_label']         = 'パスワード';
$lang['edit_user_validation_password_confirm_label'] = 'パスワードの確認';

// Create Group
$lang['create_group_title']                  = 'グループの作成';
$lang['create_group_heading']                = 'グループの作成';
$lang['create_group_subheading']             = 'グループ情報を入力して下さい。';
$lang['create_group_name_label']             = 'グループ名：';
$lang['create_group_desc_label']             = '詳細：';
$lang['create_group_submit_btn']             = '作成';
$lang['create_group_validation_name_label']  = 'グループ名';
$lang['create_group_validation_desc_label']  = '詳細';

// Edit Group
$lang['edit_group_title']                  = 'グループの編集';
$lang['edit_group_saved']                  = '保存できました';
$lang['edit_group_heading']                = 'グループの編集';
$lang['edit_group_subheading']             = 'グループ情報を入力して下さい。';
$lang['edit_group_name_label']             = 'グループ名：';
$lang['edit_group_desc_label']             = '詳細：';
$lang['edit_group_submit_btn']             = '保存';
$lang['edit_group_validation_name_label']  = 'グループ名';
$lang['edit_group_validation_desc_label']  = '詳細';

// Change Password
$lang['change_password_heading']                               = 'パスワードの変更';
$lang['change_password_old_password_label']                    = '元のパスワード：';
$lang['change_password_new_password_label']                    = '新しいパスワード（少なくとも%s字以上）：';
$lang['change_password_new_password_confirm_label']            = '新しいパスワード（確認用）：';
$lang['change_password_submit_btn']                            = '変更';
$lang['change_password_validation_old_password_label']         = '元のパスワード';
$lang['change_password_validation_new_password_label']         = '新しいパスワード';
$lang['change_password_validation_new_password_confirm_label'] = '新しいパスワードの確認';

// Forgot Password
$lang['forgot_password_heading']                 = 'パスワードの再発行';
$lang['forgot_password_subheading']              = '新しいパスワードをメールで送信するため、%sを入力して下さい。';
$lang['forgot_password_email_label']             = '%s：';
$lang['forgot_password_submit_btn']              = '送信';
$lang['forgot_password_validation_email_label']  = 'メールアドレス';
$lang['forgot_password_username_identity_label'] = 'ユーザー名';
$lang['forgot_password_email_identity_label']    = 'メールアドレス';
$lang['forgot_password_email_not_found']         = 'No record of that email address.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = 'パスワードの変更';
$lang['reset_password_new_password_label']                    = '新しいパスワード（少なくとも%s字以上）：';
$lang['reset_password_new_password_confirm_label']            = '新しいパスワード（確認用）：';
$lang['reset_password_submit_btn']                            = '変更';
$lang['reset_password_validation_new_password_label']         = '新しいパスワード';
$lang['reset_password_validation_new_password_confirm_label'] = '新しいパスワードの確認';

// Activation Email
$lang['email_activate_heading']    = 'アカウントの有効化： %s';
$lang['email_activate_subheading'] = 'このリンクをクリックして%s。';
$lang['email_activate_link']       = 'アカウントを有効にして下さい';

// Forgot Password Email
$lang['email_forgot_password_heading']    = 'パスワードのリセット： %s';
$lang['email_forgot_password_subheading'] = 'このリンクをクリックして%s。';
$lang['email_forgot_password_link']       = 'パスワードをリセットして下さい';


