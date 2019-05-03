<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - French
*
* Author:     Stan
* 		      tfspir@gmail.com
*
* Updated by: Gwenaël Gallon
* 			  github@dev-ggallon
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.23.2010
* Updated:  06.16.2017
*
* Description:  French language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']            = 'Compte créé avec succès';
$lang['account_creation_unsuccessful']          = 'Impossible de créer le compte';
$lang['account_creation_duplicate_email']       = 'Email déjà utilisé ou invalide';
$lang['account_creation_duplicate_identity']    = 'Nom d\'utilisateur déjà utilisé ou invalide';
$lang['account_creation_missing_default_group'] = 'Le groupe par défaut n\'est pas configuré';
$lang['account_creation_invalid_default_group'] = 'Le nom du groupe par défaut n\'est pas valide';


// Password
$lang['password_change_successful']   = 'Le mot de passe a été changé avec succès';
$lang['password_change_unsuccessful'] = 'Impossible de changer le mot de passe';
$lang['forgot_password_successful']   = 'Mail de réinitialisation du mot de passe envoyé';
$lang['forgot_password_unsuccessful'] = 'Impossible de réinitialiser le mot de passe';

// Activation
$lang['activate_successful']           = 'Compte activé';
$lang['activate_unsuccessful']         = 'Impossible d\'activer le compte';
$lang['deactivate_successful']         = 'Compte désactivé';
$lang['deactivate_unsuccessful']       = 'Impossible de désactiver le compte';
$lang['activation_email_successful']   = 'Email d\'activation envoyé avec succès';
$lang['activation_email_unsuccessful'] = 'Impossible d\'envoyer l\'email d\'activation';

// Login / Logout
$lang['login_successful']              = 'Connecté avec succès';
$lang['login_unsuccessful']            = 'Erreur lors de la connexion';
$lang['login_unsuccessful_not_active'] = 'Ce compte est inactif';
$lang['login_timeout']                 = 'Compte temporairement bloqué suite à de trop nombreuses tentatives.  Veuillez réessayer plus tard.';
$lang['logout_successful']             = 'Déconnexion effectuée avec succès';

// Account Changes
$lang['update_successful']   = 'Compte utilisateur mis à jour avec succès';
$lang['update_unsuccessful'] = 'Impossible de mettre à jour le compte utilisateur';
$lang['delete_successful']   = 'Utilisateur supprimé';
$lang['delete_unsuccessful'] = 'Impossible de supprimer l\'utilisateur';
$lang['deactivate_current_user_unsuccessful']= 'Vous ne pouvez pas vous désactiver vous-même.';

// Groups
$lang['group_creation_successful'] = 'Groupe créé avec succès';
$lang['group_already_exists']      = 'Nom du groupe déjà pris';
$lang['group_update_successful']   = 'Informations sur le groupe mis à jour';
$lang['group_delete_successful']   = 'Groupe supprimé';
$lang['group_delete_unsuccessful'] = 'Impossible de supprimer le groupe';
$lang['group_delete_notallowed']    = 'Le groupe Administrateur ne peut pas être supprimé';
$lang['group_name_required']       = 'Le nom du groupe est un champ obligatoire';
$lang['group_name_admin_not_alter'] = 'Le nom du groupe Admin ne peut pas être modifié';

// Activation Email
$lang['email_activation_subject']  = 'Activation du compte';
$lang['email_activate_heading']    = 'Activer le compte pour %s';
$lang['email_activate_subheading'] = 'S\'il vous plaît cliquer sur ce lien pour %s.';
$lang['email_activate_link']       = 'Activez votre compte';

// Forgot Password Email
$lang['email_forgotten_password_subject'] = 'Mot de Passe Oublié - Vérification';
$lang['email_forgot_password_heading']    = 'Réinitialiser le mot de passe pour %s';
$lang['email_forgot_password_subheading'] = 'Veuillez cliquer sur ce lien pour %s.';
$lang['email_forgot_password_link']       = 'Réinitialiser votre mot de passe';

