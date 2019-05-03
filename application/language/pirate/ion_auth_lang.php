<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * Every application needs to support the famous pirate language
 *
 * @author 		Yorick Peterse <info [at] yorickpeterse [dot] com>
 * @link 		http://www.yorickpeterse.com/
 *
 */

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Ahoy, Welcome Aboard Landlubber!';
$lang['account_creation_unsuccessful'] 	 	 = 'Avast, Unable to Commandeer Ship';
$lang['account_creation_duplicate_email'] 	 = 'Letter in the Bottle Already Used or Invalid';
$lang['account_creation_duplicate_identity'] = 'Pirate Name Already Used or Invalid';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';


// Password
$lang['password_change_successful'] 	 	 = 'Secret Code Successfully Changed';
$lang['password_change_unsuccessful'] 	  	 = 'Unable to Change Secret Code';
$lang['forgot_password_successful'] 	 	 = 'Secret Code Reset Letter Sent';
$lang['forgot_password_unsuccessful'] 	 	 = 'Unable to Reset Secret Code';

// Activation
$lang['activate_successful'] 		  	 	= 'Ahoy, Your Ship Be Ready For Sailing The Seven Seas';
$lang['activate_unsuccessful'] 		 	 	= 'Avast, Furner be having trouble!';
$lang['deactivate_successful'] 		  	 	= 'Furner be burned down by the Navy';
$lang['deactivate_unsuccessful'] 	  	 	= 'Shiver me timbers! Account not Deactivated';
$lang['activation_email_successful'] 	  	= 'Letter in the Bottle Sent';
$lang['activation_email_unsuccessful']   	= 'Unable to Send Letter in the Bottle';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	 		= 'Yarr, welcome aboard!';
$lang['login_unsuccessful'] 		  	 	= 'In-Correct Secret Code';
$lang['login_unsuccessful_not_active'] 		 = 'Account is inactive';
$lang['login_timeout']                       = 'Temporarily Locked Out.  Try again later.';
$lang['logout_successful'] 		 	 		= 'Be Seeying ya Matey';

// Account Changes
$lang['update_successful'] 		 	 		= 'Ship Information Successfully Updated';
$lang['update_unsuccessful'] 		 	 	= 'Unable to Update Ship Information';
$lang['delete_successful'] 		 	 		= 'Pirate Sent to Davy Jones\' Locker';
$lang['delete_unsuccessful'] 		 	 	= 'Avast, The Pirate be Still Alive';

// Groups
$lang['group_creation_successful']  = 'Group created Successfully';
$lang['group_already_exists']       = 'Group name already taken';
$lang['group_update_successful']    = 'Group details updated';
$lang['group_delete_successful']    = 'Group deleted';
$lang['group_delete_unsuccessful'] 	= 'Unable to delete group';
$lang['group_delete_notallowed']    = 'Can\'t delete the administrators\' group';
$lang['group_name_required'] 		= 'Group name is a required field';
$lang['group_name_admin_not_alter'] = 'Admin group name can not be changed';

// Activation Email
$lang['email_activation_subject']            = 'Account Activation';
$lang['email_activate_heading']    = 'Activate account for %s';
$lang['email_activate_subheading'] = 'Please click this link to %s.';
$lang['email_activate_link']       = 'Activate Your Account';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Forgotten Password Verification';
$lang['email_forgot_password_heading']    = 'Reset Password for %s';
$lang['email_forgot_password_subheading'] = 'Please click this link to %s.';
$lang['email_forgot_password_link']       = 'Reset Your Password';
