<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Portuguese (UTF-8)
*
* Author: André Brás Simões
*       andrebrassimoes@gmail.com
*
* Adjustments by @Dentxinho and @MichelAlonso and @marcelod
*
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  17.05.2010
*
* Description:  Portuguese language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']         	= 'Conta criada com sucesso';
$lang['account_creation_unsuccessful']       	= 'Não foi possível criar a conta';
$lang['account_creation_duplicate_email']    	= 'Email em uso ou inválido';
$lang['account_creation_duplicate_identity'] 	= 'Nome de usuário em uso ou inválido';
$lang['account_creation_missing_default_group'] = 'Grupo padrão não está definido';
$lang['account_creation_invalid_default_group'] = 'Nome padrão do grupo definido é inválido';

// Password
$lang['password_change_successful']         = 'Senha alterada com sucesso';
$lang['password_change_unsuccessful']       = 'Não foi possível alterar a senha';
$lang['forgot_password_successful']         = 'Nova senha enviada por email';
$lang['forgot_password_unsuccessful']       = 'Não foi possível criar uma nova senha';

// Activation
$lang['activate_successful']                = 'Conta ativada';
$lang['activate_unsuccessful']              = 'Não foi possível ativar a conta';
$lang['deactivate_successful']              = 'Conta desativada';
$lang['deactivate_unsuccessful']            = 'Não foi possível desativar a conta';
$lang['activation_email_successful']        = 'Email de ativação enviado com sucesso';
$lang['activation_email_unsuccessful']      = 'Não foi possível enviar o email de ativação';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful']                   = 'Sessão iniciada com sucesso';
$lang['login_unsuccessful']                 = 'Usuário ou senha inválidos';
$lang['login_unsuccessful_not_active']      = 'A conta está desativada';
$lang['login_timeout']                      = 'Conta temporariamente bloqueada. Tente novamente mais tarde';
$lang['logout_successful']            		= 'Sessão encerrada com sucesso';

// Account Changes
$lang['update_successful']                  = 'Informações da conta atualizadas com sucesso';
$lang['update_unsuccessful']                = 'Não foi possível atualizar as informações da conta';
$lang['delete_successful']                  = 'Usuário excluído com sucesso';
$lang['delete_unsuccessful']                = 'Não foi possível excluir o usuário';

// Groups
$lang['group_creation_successful']          = 'Grupo criado com sucesso';
$lang['group_already_exists']               = 'Um grupo com este nome já existe';
$lang['group_update_successful']            = 'Dados do grupo atualizados com sucesso';
$lang['group_delete_successful']            = 'Grupo excluído com sucesso';
$lang['group_delete_unsuccessful']          = 'Não foi possível excluir o grupo';
$lang['group_delete_notallowed']    		= 'Não é possível excluir o grupo de administradores';
$lang['group_name_required'] 				= 'Nome do grupo é um campo obrigatório';
$lang['group_name_admin_not_alter'] 		= 'Nome do grupo administrador não pode ser alterado';

// Activation Email
$lang['email_activation_subject']           = 'Ativação da conta';
$lang['email_activate_heading']    			= 'Ative sua conta para %s';
$lang['email_activate_subheading'] 			= 'Por favor, clique neste link para %s.';
$lang['email_activate_link']       			= 'Ative sua conta';

// Forgot Password Email
$lang['email_forgotten_password_subject']   = 'Esqueci a senha';
$lang['email_forgot_password_heading']    	= 'Redefinido a senha para %s';
$lang['email_forgot_password_subheading'] 	= 'Por favor, clique neste link para %s.';
$lang['email_forgot_password_link']       	= 'Redefina sua senha';

