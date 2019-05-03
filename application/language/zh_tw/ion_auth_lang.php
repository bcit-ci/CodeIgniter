<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Name:  Ion Auth Lang - Chinese Traditional
 *
 * Author: Bo-Yi Wu
 *         appleboy.tw@gmail.com
 *         @taiwan
 *
 * Location: http://github.com/benedmunds/ion_auth/
 *
 * Created:  03.14.2011
 *
 * Description:  Chinese language file for Ion Auth messages and errors
 *
 */

// Account Creation
$lang['account_creation_successful']         = '帳號建立成功';
$lang['account_creation_unsuccessful']       = '無法建立帳號';
$lang['account_creation_duplicate_email']    = '電子郵件已被使用或不合法';
$lang['account_creation_duplicate_identity'] = '帳號已存在或不合法';
$lang['account_creation_missing_default_group'] = '尚未設定預設群組';
$lang['account_creation_invalid_default_group'] = '預設群組名稱不合法';

// Password
$lang['password_change_successful']   = '密碼變更成功';
$lang['password_change_unsuccessful'] = '密碼變更失敗';
$lang['forgot_password_successful']   = '密碼已重設，請收取電子郵件';
$lang['forgot_password_unsuccessful'] = '密碼重設失敗';

// Activation
$lang['activate_successful']           = '帳號已啟動';
$lang['activate_unsuccessful']         = '啟動帳號失敗';
$lang['deactivate_successful']         = '帳號已關閉';
$lang['deactivate_unsuccessful']       = '關閉帳號失敗';
$lang['activation_email_successful']   = '已寄送啟動帳號電子郵件';
$lang['activation_email_unsuccessful'] = '啟動帳號電子郵件失敗';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful']   = '登入成功';
$lang['login_unsuccessful'] = '登入失敗';
$lang['login_unsuccessful_not_active'] = '帳號尚未啟動';
$lang['login_timeout'] = '帳號暫時被鎖定，請稍候再試';
$lang['logout_successful']  = '登出成功';

// Account Changes
$lang['update_successful']   = '帳號資料更新成功';
$lang['update_unsuccessful'] = '更新帳號資料失敗';
$lang['delete_successful']   = '帳號已刪除';
$lang['delete_unsuccessful'] = '刪除帳號失敗';

// Groups
$lang['group_creation_successful']  = '建立群組成功';
$lang['group_already_exists']       = '群組名稱已重複';
$lang['group_update_successful']    = '更新群組成功';
$lang['group_delete_successful']    = '群組已刪除';
$lang['group_delete_unsuccessful']  = '刪除群組失敗';
$lang['group_delete_notallowed']    = '無法刪除管理者群組';
$lang['group_name_required']        = '群組名稱為必填欄位';
$lang['group_name_admin_not_alter'] = '不能變更管理者群組名稱';

// Activation Email
$lang['email_activation_subject']  = '啟動帳號';
$lang['email_activate_heading']    = '啟動帳號 %s';
$lang['email_activate_subheading'] = '請點此連結 %s';
$lang['email_activate_link']       = '啟動您的帳號';

// Forgot Password Email
$lang['email_forgotten_password_subject'] = '密碼重設驗證';
$lang['email_forgot_password_heading']    = '重新啟用密碼 %s';
$lang['email_forgot_password_subheading'] = '請點此連結 %s';
$lang['email_forgot_password_link']       = '重新啟動密碼';

