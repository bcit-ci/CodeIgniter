<?php
/**
 * Name:    Ion Auth
 * Author:  Ben Edmunds
 *           ben.edmunds@gmail.com
 *           @benedmunds
 *
 * Added Awesomeness: Phil Sturgeon
 *
 * Created:  10.01.2009
 *
 * Description:  Modified auth system based on redux_auth with extensive customization. This is basically what Redux Auth 2 should be.
 * Original Author name has been kept but that does not mean that the method has not been modified.
 *
 * Requirements: PHP5.6 or above
 *
 * @package    CodeIgniter-Ion-Auth
 * @author     Ben Edmunds
 * @link       http://github.com/benedmunds/CodeIgniter-Ion-Auth
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 | -------------------------------------------------------------------------
 | Database group name option.
 | -------------------------------------------------------------------------
 | Allows to select a specific group for the database connection
 |
 | Default is empty: uses default group defined in CI's configuration
 | (see application/config/database.php, $active_group variable)
 */
$config['database_group_name'] = '';

/*
| -------------------------------------------------------------------------
| Tables.
| -------------------------------------------------------------------------
| Database table names.
*/
$config['tables']['users']           = 'users';
$config['tables']['groups']          = 'groups';
$config['tables']['users_groups']    = 'users_groups';
$config['tables']['login_attempts']  = 'login_attempts';

/*
 | Users table column and Group table column you want to join WITH.
 |
 | Joins from users.id
 | Joins from groups.id
 */
$config['join']['users']  = 'user_id';
$config['join']['groups'] = 'group_id';

/*
 | -------------------------------------------------------------------------
 | Hash Method (bcrypt or argon2)
 | -------------------------------------------------------------------------
 | Bcrypt is available in PHP 5.3+
 | Argon2 is available in PHP 7.2
 |
 | Argon2 is recommended by expert (it is actually the winner of the Password Hashing Competition
 | for more information see https://password-hashing.net). So if you can (PHP 7.2), go for it.
 |
 | Bcrypt specific:
 | 		bcrypt_default_cost settings:  This defines how strong the encryption will be.
 | 		However, higher the cost, longer it will take to hash (CPU usage) So adjust
 | 		this based on your server hardware.
 |
 | 		You can (and should!) benchmark your server. This can be done easily with this little script:
 | 		https://gist.github.com/Indigo744/24062e07477e937a279bc97b378c3402
 |
 | 		With bcrypt, an example hash of "password" is:
 | 		$2y$08$200Z6ZZbp3RAEXoaWcMA6uJOFicwNZaqk4oDhqTUiFXFe63MG.Daa
 |
 |		A specific parameter bcrypt_admin_cost is available for user in admin group.
 |		It is recommended to have a stronger hashing for administrators.
 |
 | Argon2 specific:
 | 		argon2_default_params settings:  This is an array containing the options for the Argon2 algorithm.
 | 		You can define 3 differents keys:
 | 			memory_cost (default 4096 kB)
 |				Maximum memory (in kBytes) that may be used to compute the Argon2 hash
 |				The spec recommends setting the memory cost to a power of 2.
 | 			time_cost (default 2)
 |				Number of iterations (used to tune the running time independently of the memory size).
                This defines how strong the encryption will be.
 | 			threads (default 2)
 |				Number of threads to use for computing the Argon2 hash
 |				The spec recommends setting the number of threads to a power of 2.
 |
 | 		You can (and should!) benchmark your server. This can be done easily with this little script:
 | 		https://gist.github.com/Indigo744/e92356282eb808b94d08d9cc6e37884c
 |
 | 		With argon2, an example hash of "password" is:
 | 		$argon2i$v=19$m=1024,t=2,p=2$VEFSSU4wSzh3cllVdE1JZQ$PDeks/7JoKekQrJa9HlfkXIk8dAeZXOzUxLBwNFbZ44
 |
 |		A specific parameter argon2_admin_params is available for user in admin group.
 |		It is recommended to have a stronger hashing for administrators.
 |
 | For more information, check the password_hash function help: http://php.net/manual/en/function.password-hash.php
 |
 */
$config['hash_method']				= 'bcrypt';	// bcrypt or argon2
$config['bcrypt_default_cost']		= 10;		// Set cost according to your server benchmark - but no lower than 10 (default PHP value)
$config['bcrypt_admin_cost']		= 12;		// Cost for user in admin group
$config['argon2_default_params']	= [
	'memory_cost'	=> 1 << 12,	// 4MB
	'time_cost'		=> 2,
	'threads'		=> 2
];
$config['argon2_admin_params']		= [
	'memory_cost'	=> 1 << 14,	// 16MB
	'time_cost'		=> 4,
	'threads'		=> 2
];

/*
 | -------------------------------------------------------------------------
 | Authentication options.
 | -------------------------------------------------------------------------
 | maximum_login_attempts: 	This maximum is not enforced by the library, but is used by
 | 							is_max_login_attempts_exceeded().
 | 							The controller should check this function and act appropriately.
 | 							If this variable set to 0, there is no maximum.
 | min_password_length:		This minimum is not enforced directly by the library.
 | 							The controller should define a validation rule to enforce it.
 | 							See the Auth controller for an example implementation.
 |
 | The library will fail for empty password or password size above 4096 bytes.
 | This is an arbitrary (long) value to protect against DOS attack.
 */
$config['site_title']                 = "Example.com";       // Site Title, example.com
$config['admin_email']                = "admin@example.com"; // Admin Email, admin@example.com
$config['default_group']              = 'members';           // Default group, use name
$config['admin_group']                = 'admin';             // Default administrators group, use name
$config['identity']                   = 'email';             /* You can use any unique column in your table as identity column.
															    The values in this column, alongside password, will be used for login purposes
															    IMPORTANT: If you are changing it from the default (email),
															    		   update the UNIQUE constraint in your DB */
$config['min_password_length']        = 8;                   // Minimum Required Length of Password (not enforced by lib - see note above)
$config['email_activation']           = FALSE;               // Email Activation for registration
$config['manual_activation']          = FALSE;               // Manual Activation for registration
$config['remember_users']             = TRUE;                // Allow users to be remembered and enable auto-login
$config['user_expire']                = 86500;               // How long to remember the user (seconds). Set to zero for no expiration - see sess_expiration in CodeIgniter Session Config for session expiration
$config['user_extend_on_login']       = FALSE;               // Extend the users cookies every time they auto-login
$config['track_login_attempts']       = TRUE;                // Track the number of failed login attempts for each user or ip.
$config['track_login_ip_address']     = TRUE;                // Track login attempts by IP Address, if FALSE will track based on identity. (Default: TRUE)
$config['maximum_login_attempts']     = 3;                   // The maximum number of failed login attempts.
$config['lockout_time']               = 600;                 /* The number of seconds to lockout an account due to exceeded attempts
																You should not use a value below 60 (1 minute) */
$config['forgot_password_expiration'] = 1800;                /* The number of seconds after which a forgot password request will expire. If set to 0, forgot password requests will not expire.
                   												30 minutes to 1 hour are good values (enough for a user to receive the email and reset its password)
                   												You should not set a value too high, as it would be a security issue! */
$config['recheck_timer']              = 0;                   /* The number of seconds after which the session is checked again against database to see if the user still exists and is active.
																Leave 0 if you don't want session recheck. if you really think you need to recheck the session against database, we would
																recommend a higher value, as this would affect performance */

/*
 | -------------------------------------------------------------------------
 | Cookie options.
 | -------------------------------------------------------------------------
 | remember_cookie_name Default: remember_code
 */
$config['remember_cookie_name'] = 'remember_code';

/*
 | -------------------------------------------------------------------------
 | Email options.
 | -------------------------------------------------------------------------
 | email_config:
 | 	  'file' = Use the default CI config or use from a config file
 | 	  array  = Manually set your email config settings
 */
$config['use_ci_email'] = FALSE; // Send Email using the builtin CI email class, if false it will return the code and the identity
$config['email_config'] = [
	'mailtype' => 'html',
];

/*
 | -------------------------------------------------------------------------
 | Email templates.
 | -------------------------------------------------------------------------
 | Folder where email templates are stored.
 | Default: auth/
 */
$config['email_templates'] = 'auth/email/';

/*
 | -------------------------------------------------------------------------
 | Activate Account Email Template
 | -------------------------------------------------------------------------
 | Default: activate.tpl.php
 */
$config['email_activate'] = 'activate.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Forgot Password Email Template
 | -------------------------------------------------------------------------
 | Default: forgot_password.tpl.php
 */
$config['email_forgot_password'] = 'forgot_password.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Message Delimiters.
 | -------------------------------------------------------------------------
 */
$config['delimiters_source']       = 'config'; 	// "config" = use the settings defined here, "form_validation" = use the settings defined in CI's form validation library
$config['message_start_delimiter'] = '<p>'; 	// Message start delimiter
$config['message_end_delimiter']   = '</p>'; 	// Message end delimiter
$config['error_start_delimiter']   = '<p>';		// Error message start delimiter
$config['error_end_delimiter']     = '</p>';	// Error message end delimiter
