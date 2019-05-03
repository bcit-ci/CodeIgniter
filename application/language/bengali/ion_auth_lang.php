<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Bengali
*
* Author: Ben Edmunds
*         ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Arifur Rahman
*         @arif2009
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.25.2018
*
* Description:  Bengali language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']            = 'অ্যাকাউন্টটি সফলভাবে তৈরি হয়েছে';
$lang['account_creation_unsuccessful']          = 'অ্যাকাউন্টটি তৈরি করা যাচ্ছেনা';
$lang['account_creation_duplicate_email']       = 'ইমেলটি ইতিমধ্যে ব্যবহৃত হয়েছে অথবা এটি ভুল';
$lang['account_creation_duplicate_identity']    = 'এটি ইতিমধ্যে ব্যবহৃত হয়েছে অথবা ভুল';
$lang['account_creation_missing_default_group'] = 'পূর্বনির্ধারিত গ্রুপ সেট করা হয়নি';
$lang['account_creation_invalid_default_group'] = 'পূর্বনির্ধারিত দলটি ভুল হয়েছে';


// Password
$lang['password_change_successful']          = 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে';
$lang['password_change_unsuccessful']        = 'পাসওয়ার্ডটি পরিবর্তন করা যাচ্ছেনা';
$lang['forgot_password_successful']          = 'পাসওয়ার্ড পরিবর্তনের জন্য ই-মেইল করা হয়েছে';
$lang['forgot_password_unsuccessful']        = 'পরিবর্তনযোগ লিঙ্ক ই-মেইল করা যাচ্ছেনা';

// Activation
$lang['activate_successful']                 = 'অ্যাকাউন্টটি সফলভাবে চালু হয়েছে';
$lang['activate_unsuccessful']               = 'অ্যাকাউন্টটি চালু করা যাচ্ছেনা';
$lang['deactivate_successful']               = 'অ্যাকাউন্টটি নিষ্ক্রিয় করা হয়েছে';
$lang['deactivate_unsuccessful']             = 'অ্যাকাউন্টটি নিষ্ক্রিয় করা যাচ্ছেনা';
$lang['activation_email_successful']         = 'সক্রিয়করণ ইমেল পাঠানো হয়েছে। আপনার ইনবক্স অথবা স্প্যামে চেক করুণ';
$lang['activation_email_unsuccessful']       = 'সক্রিয়করণ ইমেল পাঠানো যাচ্ছেনা';
$lang['deactivate_current_user_unsuccessful']= 'আপনি নিজেকে নিজেকে নিষ্ক্রিয় করতে পারবেন না।';

// Login / Logout
$lang['login_successful']                    = 'আপনি সফলভাবে প্রবেশ করেছেন';
$lang['login_unsuccessful']                  = 'প্রবেশ করা যাচ্ছেনা';
$lang['login_unsuccessful_not_active']       = 'অ্যাকাউন্টটি নিষ্ক্রিয়';
$lang['login_timeout']                       = 'অস্থায়ীভাবে লক হয়েছে। পরে আবার চেষ্টা করুণ।';
$lang['login_unsuccessful']                  = 'লগইন করা যাচ্ছেনা';
$lang['login_unsuccessful_not_active']       = 'অ্যাকাউন্টটি নিষ্ক্রিয়';
$lang['login_timeout']                       = 'অস্থায়ীভাবে লগ আউট হয়েছে। পরে আবার চেষ্টা করুণ।';
$lang['logout_successful']                   = 'সফলভাবে লগ আউট হয়েছে';

// Account Changes
$lang['update_successful']                   = 'অ্যাকাউন্টের তথ্য সফলভাবে সংস্করণ করা হয়েছে';
$lang['update_unsuccessful']                 = 'অ্যাকাউন্টের তথ্য সংস্করণ করা যাচ্ছেনা';
$lang['delete_successful']                   = 'ব্যবহারকারীকে মুছে ফেলা হয়েছে';
$lang['delete_unsuccessful']                 = 'ব্যবহারকারীকে মুছে ফেলা যাচ্ছেনা';

// Groups
$lang['group_creation_successful']           = 'সফলভাবে দলটি তৈরি করা হয়েছে';
$lang['group_already_exists']                = 'একই নামে ইতিমধ্যে আরেকটি গ্রুপ তৈরি করা হয়েছে';
$lang['group_update_successful']             = 'দলটির বিবরণ সংস্করণ করা হয়েছে';
$lang['group_delete_successful']             = 'দলটি মুছে ফেলা হয়েছে';
$lang['group_delete_unsuccessful']           = 'দলটি মুছে ফেলা যাচ্ছেনা';
$lang['group_delete_notallowed']             = 'অ্যাডমিনিস্ট্রেটরদের দলটি মুছে ফেলা যাবেনা';
$lang['group_name_required']                 = 'দলের নামটি অবশ্যই দিতে হবে';
$lang['group_name_admin_not_alter']          = 'অ্যাডমিনিস্ট্রেটরদের দলটির নাম সংস্করণ করা যাবেনা';

// Activation Email
$lang['email_activation_subject']            = 'অ্যাকাউন্ট সক্রিয়করণ';
$lang['email_activate_heading']              = '%s এর জন্য অ্যাকাউন্ট সক্রিয়করণ প্রক্রিয়া';
$lang['email_activate_subheading']           = 'দয়া করে এই লিঙ্কটি ক্লিক করুণ %s.';
$lang['email_activate_link']                 = 'আপনার অ্যাকাউন্ট টি সক্রিয় করুণ';

// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'ভুলে যাওয়া পাসওয়ার্ড পুনরূদ্ধার';
$lang['email_forgot_password_heading']       = '%s এর জন্য পাসওয়ার্ড পুনরূদ্ধার করন প্রক্রিয়া';
$lang['email_forgot_password_subheading']    = 'দয়া করে এই লিঙ্কটি ক্লিক করুণ %s.';
$lang['email_forgot_password_link']          = 'আপনার অ্যাকাউন্ট টি পুনরূদ্ধার করুণ';

// New Password Email
$lang['email_new_password_subject']          = 'নতুন পাসওয়ার্ড';
$lang['email_new_password_heading']          = '%s এর জন্য নতুন পাসওয়ার্ড';
$lang['email_new_password_subheading']       = 'আপনার পাসওয়ার্ড পুনরায় সেট করা হয়েছে: %s';
