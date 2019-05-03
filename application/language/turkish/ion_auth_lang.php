<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Ion Auth Lang - Turkish (UTF-8)
 *
 * Author: Ben Edmunds
 * 	       ben.edmunds@gmail.com
 *         @benedmunds
 *
 * Translation: Acipayamli Ozi
 *
 * Modifications:
 *           Hüseyin Kozan @huseyinkozan posta@huseyinkozan.com.tr
 *           Burak Özdemir @ozdemirburak http://burakozdemir.co.uk
 *
 * Created:  05.01.2010
 * Updated:  03.14.2015
 * Description:  Turkish language file for Ion Auth messages and errors
 *
 */

// Account Creation
$lang['account_creation_successful'] 	  	    = 'Üyelik kaydınız başarıyla tamamlandı';
$lang['account_creation_unsuccessful'] 	 	    = 'Üyelik kaydınız yapılamadı';
$lang['account_creation_duplicate_email'] 	    = 'E-posta adresi geçersiz ya da daha önceden alınmış';
$lang['account_creation_duplicate_identity']    = 'Kullanıcı adı geçersiz ya da daha önceden alınmış';
$lang['account_creation_missing_default_group'] = 'Herhangi bir varsayılan grup ayarlanmamış';
$lang['account_creation_invalid_default_group'] = 'Geçersiz bir varsayılan grup seçimi';

// Password
$lang['password_change_successful'] 	 	    = 'Şifreniz değiştirildi';
$lang['password_change_unsuccessful'] 	  	    = 'Şifre değiştirme isteği gerçekleştirilemedi';
$lang['forgot_password_successful'] 	 	    = 'Yeni şifreniz e-posta adresinize gönderildi';
$lang['forgot_password_unsuccessful'] 	 	    = 'Şifre yenileme isteği gerçekleştirilemedi';

// Activation
$lang['activate_successful'] 		  	        = 'Hesap başarıyla etkinleştirildi';
$lang['activate_unsuccessful'] 		 	        = 'Hesap etkinleştirme başarısız';
$lang['deactivate_successful'] 		  	        = 'Hesap devre dışı bırakıldı';
$lang['deactivate_unsuccessful'] 	  	        = 'Hesap devre dışı bırakma isteğiniz gerçekleştirilemedi';
$lang['activation_email_successful'] 	        = 'Hesap etkinleştirme e-postası gönderildi';
$lang['activation_email_unsuccessful']          = 'Hesap etkinleştirme e-postası gönderilemedi';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	            = 'Giriş başarılı';
$lang['login_unsuccessful'] 		 	        = 'Giriş başarısız';
$lang['login_unsuccessful_not_active']          = 'Giriş başarısız, hesap aktif değil';
$lang['login_timeout']                          = 'Oturum zaman aşımı, daha sonra tekrar deneyiniz.';
$lang['logout_successful'] 		 	            = 'Çıkış başarılı';

// Account Changes
$lang['update_successful'] 		 	            = 'Üyelik bilgileri güncellendi';
$lang['update_unsuccessful'] 		 	        = 'Üyelik bilgileri güncellenemedi';
$lang['delete_successful'] 		 	            = 'Kullanıcı silindi';
$lang['delete_unsuccessful'] 			        = 'Kullanıcı silme başarısız';

// Groups
$lang['group_creation_successful']              = 'Grup başarıyla oluşturuldu';
$lang['group_already_exists']                   = 'Grup adı daha önceden oluşturulmuş';
$lang['group_update_successful']                = 'Grup detayları güncellendi';
$lang['group_delete_successful']                = 'Grup silindi ';
$lang['group_delete_unsuccessful'] 	            = 'Grup silinemedi';
$lang['group_delete_notallowed']                = 'Yönetici grup silinemez';
$lang['group_name_required'] 		            = 'Grup adı alanı gereklidir';
$lang['group_name_admin_not_alter']             = 'Yönetici grup adı değiştirilemez';

// Activation Email
$lang['email_activation_subject']               = 'Hesap Etkinleştirme';
$lang['email_activate_heading']                 = '%s için hesap etkinleştirme';
$lang['email_activate_subheading']              = 'Bu linke tıklayarak %s.';
$lang['email_activate_link']                    = 'hesabınızı etkinleştirin';

// Forgot Password Email
$lang['email_forgotten_password_subject']       = 'Şifremi Unuttum';
$lang['email_forgot_password_heading']          = '%s için şifre sıfırlama';
$lang['email_forgot_password_subheading']       = 'Bağlantıya tıklayarak %s.';
$lang['email_forgot_password_link']             = 'şifrenizi sıfırlayınız';

