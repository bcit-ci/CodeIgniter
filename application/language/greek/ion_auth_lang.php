<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Greek
*
* Author: Vagelis Papaloukas
* 		  vagelispapalou@yahoo.gr
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  02.04.2011
*
* Description:  Greek language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Ο Λογαριασμός Δημιουργήθηκε Επιτυχώς';
$lang['account_creation_unsuccessful'] 	 	 = 'Αποτυχία Δημιουργίας Λογαριασμού';
$lang['account_creation_duplicate_email'] 	 = 'Το Email χρησιμποιείται ήδη ή είναι λάθος';
$lang['account_creation_duplicate_identity'] 	 = 'Ο Χρήστης υπάρχει ήδη ή είναι λάθος';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';


// Password
$lang['password_change_successful'] 	 	 = 'Επιτυχής Αλλαγή Κωδικού';
$lang['password_change_unsuccessful'] 	  	 = 'Αδυναμία Αλλαγής Κωδικού';
$lang['forgot_password_successful'] 	 	 = 'Εστάλη Email Κωδικού Επαναφοράς';
$lang['forgot_password_unsuccessful'] 	 	 = 'Αδυναμία Επαναφοράς Κωδικού';

// Activation
$lang['activate_successful'] 		  	 = 'Ο Λογαριασμός Ενεργοποιήθηκε';
$lang['activate_unsuccessful'] 		 	 = 'Αδυναμία Ενεργοποίησης Λογαριασμού';
$lang['deactivate_successful'] 		  	 = 'Ο Λογαριασμός Απενεργοποιήθηκε';
$lang['deactivate_unsuccessful'] 	  	 = 'Αδυναμία Απενεργοποίησης Λογαριασμού';
$lang['activation_email_successful'] 	  	 = 'Εστάλη Email Ενεργοποίησης Λογαριασμού';
$lang['activation_email_unsuccessful']   	 = 'Αδυναμία Αποστολής Email Ενεργοποίησης';

// Login / Logout
$lang['login_successful'] 		  	 = 'Συνδεθήκατε Επιτυχώς';
$lang['login_unsuccessful'] 		  	 = 'Λάθος Στοιχεία';
$lang['login_unsuccessful_not_active'] 		 = 'Account is inactive';
$lang['login_timeout']                       = 'Temporarily Locked Out.  Try again later.';
$lang['logout_successful'] 		 	 = 'Αποσυνδεθήκατε Επιτυχώς';

// Account Changes
$lang['update_successful'] 		 	 = 'Οι Πληροφορίες του Λογαριασμού Ενημερώθηκαν Επιτυχώς';
$lang['update_unsuccessful'] 		 	 = 'Αδυναμία Ενημέρωσης Πληροφοριών Λογαριασμού';
$lang['delete_successful'] 		 	 = 'Ο Χρήστης Διαγράφηκε';
$lang['delete_unsuccessful'] 		 	 = 'Αδυναμία Διαγραφής Χρήστη';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

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
