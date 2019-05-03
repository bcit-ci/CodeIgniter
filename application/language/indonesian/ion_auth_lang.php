<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Indonesian
*
* Author: Toni Haryanto
* 		  toha.samba@gmail.com
*         @yllumi
*
* Author: Daeng Muhammad Feisal
*         daengdoang@gmail.com
*         @daengdoang
*
* Author: Suhindra
*         suhindra@hotmail.co.id
*         @suhindra
*
* Location: https://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  11.15.2011
* Last-Edit: 28.04.2016
*
* Description:  Indonesian language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']				= 'Akun Berhasil Dibuat';
$lang['account_creation_unsuccessful']				= 'Tidak Dapat Membuat Akun';
$lang['account_creation_duplicate_email']			= 'Email Sudah Digunakan atau Tidak Valid';
$lang['account_creation_duplicate_identity']	    = 'Identitas Sudah Digunakan atau Tidak Valid';

// TODO Please Translate
$lang['account_creation_missing_default_group']		= 'Standar grup tidak diatur';
$lang['account_creation_invalid_default_group']		= 'Pengaturan Nama Grup Standar Tidak Valid';


// Password
$lang['password_change_successful']					= 'Kata Sandi Berhasil Diubah';
$lang['password_change_unsuccessful']				= 'Tidak Dapat Mengganti Kata Sandi';
$lang['forgot_password_successful']					= 'Email untuk Set Ulang Kata Sandi Telah Dikirim';
$lang['forgot_password_unsuccessful']				= 'Tidak Dapat Set Ulang Kata Sandi';

// Activation
$lang['activate_successful']						= 'Akun Telah Diaktifkan';
$lang['activate_unsuccessful']						= 'Tidak Dapat Mengaktifkan Akun';
$lang['deactivate_successful']						= 'Akun Telah Dinonaktifkan';
$lang['deactivate_unsuccessful']					= 'Tidak Dapat Menonaktifkan Akun';
$lang['activation_email_successful']			    = 'Email untuk Aktivasi Telah Dikirim. Silahkan cek inbox atau spam';
$lang['activation_email_unsuccessful']		        = 'Tidak Dapat Mengirimkan Email Aktivasi';
$lang['deactivate_current_user_unsuccessful']		= 'Anda tidak dapat Nonaktifkan akun anda sendiri.';

// Login / Logout
$lang['login_successful']							= 'Log In Berhasil';
$lang['login_unsuccessful']							= 'Log In Gagal';
$lang['login_unsuccessful_not_active']	            = 'Akun Tidak Aktif';
$lang['login_timeout']								= 'Sementara Terkunci. Coba Beberapa Saat Lagi.';
$lang['logout_successful']							= 'Log Out Berhasil';

// Account Changes
$lang['update_successful']							= 'Informasi Akun Berhasil Diperbaharui';
$lang['update_unsuccessful']						= 'Gagal Memperbaharui Informasi Akun';
$lang['delete_successful']							= 'Pengguna Telah Dihapus';
$lang['delete_unsuccessful']						= 'Gagal Menghapus Pengguna';

// Groups
$lang['group_creation_successful']				    = 'Grup Berhasil Dibuat';
$lang['group_already_exists']						= 'Nama Grup Sudah Digunakan';
$lang['group_update_successful']					= 'Rincian Grup Berhasil Diubah';
$lang['group_delete_successful']					= 'Grup Berhasil Dihapus';
$lang['group_delete_unsuccessful']				    = 'Gagal Menghapus Grup';
$lang['group_delete_notallowed']					= 'Tidak Dapat menghapus Grup Administrator';
$lang['group_name_required']						= 'Nama Grup Tidak Boleh Kosong';
$lang['group_name_admin_not_alter']			    	= 'Nama Grup Admin Tidak Bisa Diubah';

// Activation Email
$lang['email_activation_subject']					= 'Aktivasi Akun';
$lang['email_activate_heading']						= 'Aktifkan Akun dari %s';
$lang['email_activate_subheading']				    = 'Silahkan klik tautan berikut untuk %s.';
$lang['email_activate_link']						= 'Aktifkan Akun Anda';

// Forgot Password Email
$lang['email_forgotten_password_subject']			= 'Verifikasi Lupa Password';
$lang['email_forgot_password_heading']				= 'Setel Ulang Kata Sandi untuk %s';
$lang['email_forgot_password_subheading']			= 'Silahkan klik tautan berikut untuk %s.';
$lang['email_forgot_password_link']					= 'Setel Ulang Kata Sandi';

