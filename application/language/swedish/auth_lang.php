<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Swedish
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Daniel Davis
*         @ourmaninjapan
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.09.2013
*
* Description:  Swedish language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Detta formulär klarade inte av våra säkerhetskontroller.';

// Login
$lang['login_heading']         = 'Logga in';
$lang['login_subheading']      = 'Logga in med email/användarnamn och lösenord nedanför.';
$lang['login_identity_label']  = 'Email/Användarnamn:';
$lang['login_password_label']  = 'Lösenord:';
$lang['login_remember_label']  = 'Kom ihåg mig:';
$lang['login_submit_btn']      = 'Logga in';
$lang['login_forgot_password'] = 'Glömt lösenord?';

// Index
$lang['index_heading']           = 'Användare';
$lang['index_subheading']        = 'Lista över användare nedanför.';
$lang['index_fname_th']          = 'Förnamn';
$lang['index_lname_th']          = 'Efternamn';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Grupper';
$lang['index_status_th']         = 'Status';
$lang['index_action_th']         = 'Åtgärder';
$lang['index_active_link']       = 'Aktiv';
$lang['index_inactive_link']     = 'Inaktiv';
$lang['index_create_user_link']  = 'Skapa ny användare';
$lang['index_create_group_link'] = 'Skapa ny grupp';

// Deactivate User
$lang['deactivate_heading']                  = 'Inaktivera Användare';
$lang['deactivate_subheading']               = 'Är du säker att du vill inaktivera användaren \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Ja:';
$lang['deactivate_confirm_n_label']          = 'Nej:';
$lang['deactivate_submit_btn']               = 'Skicka';
$lang['deactivate_validation_confirm_label'] = 'bekräftelse';
$lang['deactivate_validation_user_id_label'] = 'användar ID';

// Create User
$lang['create_user_heading']                           = 'Skapa Användare';
$lang['create_user_subheading']                        = 'Ange användarens uppgifter nedanför.';
$lang['create_user_fname_label']                       = 'Förnamn:';
$lang['create_user_lname_label']                       = 'Efternamn:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = 'Företagsnamn:';
$lang['create_user_email_label']                       = 'Email:';
$lang['create_user_phone_label']                       = 'Telefon:';
$lang['create_user_password_label']                    = 'Lösenord:';
$lang['create_user_password_confirm_label']            = 'Bekräfta Lösenord:';
$lang['create_user_submit_btn']                        = 'Skapa Användare';
$lang['create_user_validation_fname_label']            = 'Förnamn';
$lang['create_user_validation_lname_label']            = 'Efternamn';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'Email Adress';
$lang['create_user_validation_phone_label']            = 'Telefonnummer';
$lang['create_user_validation_company_label']          = 'Företagsnamn';
$lang['create_user_validation_password_label']         = 'Lösenord';
$lang['create_user_validation_password_confirm_label'] = 'Lösenordsbekräftelse';

// Edit User
$lang['edit_user_heading']                           = 'Ändra användare';
$lang['edit_user_subheading']                        = 'Ange användarens uppgifter nedanför.';
$lang['edit_user_fname_label']                       = 'Förnamn:';
$lang['edit_user_lname_label']                       = 'Efternamn:';
$lang['edit_user_company_label']                     = 'Företagsnamn:';
$lang['edit_user_email_label']                       = 'Email:';
$lang['edit_user_phone_label']                       = 'Telefon:';
$lang['edit_user_password_label']                    = 'Lösenord: (om lösenord ändras)';
$lang['edit_user_password_confirm_label']            = 'Bekräfta Lösenord: (om lösenord ändras)';
$lang['edit_user_groups_heading']                    = 'Medlem i grupper';
$lang['edit_user_submit_btn']                        = 'Spara Användare';
$lang['edit_user_validation_fname_label']            = 'Förnamn';
$lang['edit_user_validation_lname_label']            = 'Efternamn';
$lang['edit_user_validation_email_label']            = 'Email Adress';
$lang['edit_user_validation_phone_label']            = 'Telefonnummer';
$lang['edit_user_validation_company_label']          = 'Företagsnamn';
$lang['edit_user_validation_groups_label']           = 'Grupper';
$lang['edit_user_validation_password_label']         = 'Lösenord';
$lang['edit_user_validation_password_confirm_label'] = 'Lösenordsbekräftelse';

// Create Group
$lang['create_group_title']                  = 'Skapa Grupp';
$lang['create_group_heading']                = 'Skapa Grupp';
$lang['create_group_subheading']             = 'Ange gruppuppgifter nedan.';
$lang['create_group_name_label']             = 'Gruppnamn:';
$lang['create_group_desc_label']             = 'Beskrivning:';
$lang['create_group_submit_btn']             = 'Skapa Grupp';
$lang['create_group_validation_name_label']  = 'Gruppnamn';
$lang['create_group_validation_desc_label']  = 'Beskrivning';

// Edit Group
$lang['edit_group_title']                  = 'Ändra Grupp';
$lang['edit_group_saved']                  = 'Grupp Sparad';
$lang['edit_group_heading']                = 'Ändra Grupp';
$lang['edit_group_subheading']             = 'Ange gruppuppgifter nedan.';
$lang['edit_group_name_label']             = 'Gruppnamn:';
$lang['edit_group_desc_label']             = 'Beskrivning:';
$lang['edit_group_submit_btn']             = 'Spara Grupp';
$lang['edit_group_validation_name_label']  = 'Gruppnamn';
$lang['edit_group_validation_desc_label']  = 'Beskrivning';

// Change Password
$lang['change_password_heading']                               = 'Ändra Lösenord';
$lang['change_password_old_password_label']                    = 'Gammalt lösenord:';
$lang['change_password_new_password_label']                    = 'Nytt lösenord (åtminstone %s karaktärer långt):';
$lang['change_password_new_password_confirm_label']            = 'Bekräfta nytt lösenord:';
$lang['change_password_submit_btn']                            = 'Ändra';
$lang['change_password_validation_old_password_label']         = 'Gammalt lösenord';
$lang['change_password_validation_new_password_label']         = 'Nytt Lösenord';
$lang['change_password_validation_new_password_confirm_label'] = 'Bekräfta nytt lösenord';

// Forgot Password
$lang['forgot_password_heading']                 = 'Glömt lösenord';
$lang['forgot_password_subheading']              = 'Ange %s så vi kan skicka email om lösenordsåterställning.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Skicka';
$lang['forgot_password_validation_email_label']  = 'Email Adress';
$lang['forgot_password_username_identity_label'] = 'Användarnamn';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'Email adressen finns inte i vårt register.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = 'Ändra Lösenord';
$lang['reset_password_new_password_label']                    = 'Nytt Lösenord (åtminstone %s karaktärer långt):';
$lang['reset_password_new_password_confirm_label']            = 'Bekräfta nytt lösenord:';
$lang['reset_password_submit_btn']                            = 'Ändra';
$lang['reset_password_validation_new_password_label']         = 'Nytt Lösenord';
$lang['reset_password_validation_new_password_confirm_label'] = 'Bekräfta nytt lösenord';
