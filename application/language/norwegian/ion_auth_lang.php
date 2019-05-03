<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Norwegian
*
* Author: Tomas E. Sandven
* 		  tomas191191@gmail.com
*         @codemonkey1991
*
* Author: Yngve Høiseth
* 		  yngve.hoiseth@gmail.com
*         @yhoiseth
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  01.01.2012
* Last-Edit: 16.11.2014
*
* Description:  Norwegian language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']			= 'Konto opprettet';
$lang['account_creation_unsuccessful']			= 'Klarte ikke å opprette konto';
$lang['account_creation_duplicate_email']		= 'E-posten er allerede i bruk eller ugyldig';
$lang['account_creation_duplicate_identity']	= 'Brukernavnet er allerede i bruk eller ugyldig';
$lang['account_creation_missing_default_group'] = 'Standardgruppe er ikke valgt';
$lang['account_creation_invalid_default_group'] = 'Ugyldig gruppenavn';


// Password
$lang['password_change_successful']	  = 'Passordet har blitt endret';
$lang['password_change_unsuccessful'] = 'Klarte ikke å endre passord';
$lang['forgot_password_successful']	  = 'E-post for tilbakestilling av passord har blitt sendt';
$lang['forgot_password_unsuccessful'] = 'Klarte ikke å tilbakestille passord';

// Activation
$lang['activate_successful']		   = 'Kontoen har blitt aktivert';
$lang['activate_unsuccessful']		   = 'Klarte ikke å aktivere konto';
$lang['deactivate_successful']		   = 'Kontoen har blitt deaktivert';
$lang['deactivate_unsuccessful']	   = 'Klarte ikke å deaktivere konto';
$lang['activation_email_successful']   = 'E-post for aktivering av konto har blitt sendt';
$lang['activation_email_unsuccessful'] = 'Klarte ikke å sende e-post for aktivering av konto';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful']			   = 'Logget inn';
$lang['login_unsuccessful']			   = 'Feil e-post/brukernavn eller passord';
$lang['login_unsuccessful_not_active'] = 'Kontoen er inaktiv';
$lang['login_timeout']				   = 'Midlertidig sperret. Logg inn senere.';
$lang['logout_successful']			   = 'Logget ut';

// Account Changes
$lang['update_successful'] 	 = 'Kontoinformasjon oppdatert';
$lang['update_unsuccessful'] = 'Klarte ikke å oppdatere kontoinformasjon';
$lang['delete_successful']	 = 'Konto slettet';
$lang['delete_unsuccessful'] = 'Klarte ikke å slette konto';

// Groups
$lang['group_creation_successful'] = 'Gruppe opprettet';
$lang['group_already_exists']	   = 'Gruppenavnet finnes allerede';
$lang['group_update_successful']   = 'Gruppeinformasjon oppdatert';
$lang['group_delete_successful']   = 'Gruppe slettet';
$lang['group_delete_unsuccessful'] = 'Klarte ikke å slette gruppe';
$lang['group_delete_notallowed']    = 'Can\'t delete the administrators\' group';
$lang['group_name_required']	   = 'Gruppenavn må fylles inn';
$lang['group_name_admin_not_alter'] = 'Admingruppenavnet kan ikke endres';

// Activation Email
$lang['email_activation_subject']  = 'Aktivering av konto';
$lang['email_activate_heading']	   = 'Aktivér konto for %s';
$lang['email_activate_subheading'] = 'Klikk på denne linken for å %s.';
$lang['email_activate_link']	   = 'Aktivér konto';

// Forgot Password Email
$lang['email_forgotten_password_subject'] = 'Glemt passord: bekreftelse';
$lang['email_forgot_password_heading']    = 'Tilbakestill passord for %s';
$lang['email_forgot_password_subheading'] = 'Klikk på denne linken for å %s.';
$lang['email_forgot_password_link']       = 'Tilbakestill passord';

