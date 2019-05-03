<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Japanese
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Translation: Nobuo Kihara
* 		  softark@gmail.com
*
* Translation: Daniel Davis
*         @ourmaninjapan
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.14.2010
*
* Description:  Japanese language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'アカウントを作成しました';
$lang['account_creation_unsuccessful'] 	 	 = 'アカウントを作成することが出来ません';
$lang['account_creation_duplicate_email'] 	 = 'メールアドレスが登録済みまたは不正です';
$lang['account_creation_duplicate_identity'] = 'ユーザー名が登録済みまたは不正です';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'デフォルトグループが設定されていません';
$lang['account_creation_invalid_default_group'] = 'デフォルトグループの名前が無効です';

// Password
$lang['password_change_successful'] 	 	 = 'パスワードを変更しました';
$lang['password_change_unsuccessful'] 	  	 = 'パスワードを変更することが出来ません';
$lang['forgot_password_successful'] 	 	 = 'パスワード再設定メールを送信しました';
$lang['forgot_password_unsuccessful'] 	 	 = 'パスワードを再設定することが出来ません';

// Activation
$lang['activate_successful'] 		  	 = 'アカウントを有効にしました';
$lang['activate_unsuccessful'] 		 	 = 'アカウントを有効にすることが出来ません';
$lang['deactivate_successful'] 		  	 = 'アカウントを無効にしました';
$lang['deactivate_unsuccessful'] 	  	 = 'アカウントを無効にすることが出来ません';
$lang['activation_email_successful'] 	 = 'アクティベーション・メールを送信しました';
$lang['activation_email_unsuccessful']   = 'アクティベーション・メールを送信できません';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	 = 'ログインしました';
$lang['login_unsuccessful'] 		 = 'ログイン出来ません';
$lang['login_unsuccessful_not_active'] 		 = 'アカウントが無効です';
$lang['login_timeout']                       = 'アカウントが仮にロックされています。後でもう一度試してください';
$lang['logout_successful'] 		 	 = 'ログアウトしました';

// Account Changes
$lang['update_successful'] 		 	 = 'アカウント情報を更新しました';
$lang['update_unsuccessful'] 		 = 'アカウント情報を更新することが出来ません';
$lang['delete_successful'] 		 	 = 'ユーザーを削除しました';
$lang['delete_unsuccessful'] 		 = 'ユーザーを削除することが出来ません';

// Groups
$lang['group_creation_successful']  = 'グループを作成しました';
$lang['group_already_exists']       = 'このグループ名はすでに使われています';
$lang['group_update_successful']    = 'グループ情報を更新しました';
$lang['group_delete_successful']    = 'グループを削除しました';
$lang['group_delete_unsuccessful'] 	= 'グループを削除することが出来ません';
$lang['group_delete_notallowed']    = 'administratorsグループは削除できません';
$lang['group_name_required'] 		= 'グループ名が必要です。';
$lang['group_name_admin_not_alter'] = '管理者グループ名は変更できません';

// Activation Email
$lang['email_activation_subject']            = 'アカウントの承認';
$lang['email_activate_heading']    = '%s アカウントを有効化します';
$lang['email_activate_subheading'] = 'このリンクをクリックして %s';
$lang['email_activate_link']       = 'アカウントを有効にして下さい';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = '忘れたパスワードの確認';
$lang['email_forgot_password_heading']    = '%s のパスワードのリセット';
$lang['email_forgot_password_subheading'] = 'こちらのリンクをクリックしてください。 %s';
$lang['email_forgot_password_link']       = 'パスワードのリセット';
