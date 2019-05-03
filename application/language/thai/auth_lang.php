<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Thai
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Daniel Davis
*         @ourmaninjapan
* Author: Rachasak Ragkamnerd
* 		  id513128@gmail.com
*         @itpcc
*
* Location: http://github.com/benedmunds/ion_auth/
*
* created:  03.09.2013
* modify :  10.11.2014
*
* Detail:  Thai language file for Ion Auth example views based from English version
*
*/

// Errors
$lang['error_csrf'] = 'POST ฟอร์มนี้ไม่ผ่านการตรวจสอบความปลอดภัย';

// Login
$lang['login_heading']         = 'เข้าสู่ระบบ';
$lang['login_subheading']      = 'โปรดเข้าสู่ระบบโดยกรอกชื่อผู้ใช้/อีเมล์ และรหัสผ่านที่ฟอร์มด้านล่าง';
$lang['login_identity_label']  = 'อีเมล์/ชื่อผู้ใช้:';
$lang['login_password_label']  = 'รหัสผ่าน:';
$lang['login_remember_label']  = 'ให้ฉันอยู่ในระบบต่อไป:';
$lang['login_submit_btn']      = 'เข้าสู่ระบบ';
$lang['login_forgot_password'] = 'ลืมรหัสผ่าน?';

// Index
$lang['index_heading']           = 'ผู้ใช้ทั้งหมด';
$lang['index_subheading']        = 'รายชื่อผุ้ใช้';
$lang['index_fname_th']          = 'ชื่อ';
$lang['index_lname_th']          = 'นามสกุล';
$lang['index_email_th']          = 'อีเมล์';
$lang['index_groups_th']         = 'กลุ่ม';
$lang['index_status_th']         = 'สถานะ';
$lang['index_action_th']         = 'การกระทำ';
$lang['index_active_link']       = 'กำลังทำงาน';
$lang['index_inactive_link']     = 'ไม่ทำงาน';
$lang['index_create_user_link']  = 'สร้างผู้ใช้ใหม่';
$lang['index_create_group_link'] = 'สร้างกลุ่มใหม่';

// เลิกใช้งานผู้ใช้
$lang['deactivate_heading']                  = 'เลิกใช้งานผู้ใช้';
$lang['deactivate_subheading']               = 'ยืนยันการเลิกใช้งานผู้ใช้ \'%s\'';
$lang['deactivate_confirm_y_label']          = 'ใช่:';
$lang['deactivate_confirm_n_label']          = 'ไม่:';
$lang['deactivate_submit_btn']               = 'ยอมรับ';
$lang['deactivate_validation_confirm_label'] = 'การยืนยัน';
$lang['deactivate_validation_user_id_label'] = 'รหัสผู้ใช้';

// สร้าง ผู้ใช้
$lang['create_user_heading']                           = 'สร้าง ผู้ใช้';
$lang['create_user_subheading']                        = 'กรุณากรอกรายละเอียดข้อมูลผู้ใช้';
$lang['create_user_fname_label']                       = 'ชื่อ:';
$lang['create_user_lname_label']                       = 'นามสกุล:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = 'ชื่อบริษัท:';
$lang['create_user_email_label']                       = 'อีเมล์:';
$lang['create_user_phone_label']                       = 'หมายเลขโทรศัพท์:';
$lang['create_user_password_label']                    = 'รหัสผ่าน:';
$lang['create_user_password_confirm_label']            = 'ยืนยันรหัสผ่าน:';
$lang['create_user_submit_btn']                        = 'สร้างผู้ใช้';
$lang['create_user_validation_fname_label']            = 'ชื่อ';
$lang['create_user_validation_lname_label']            = 'นามสกุล';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'ที่อยู่อีเมล์';
$lang['create_user_validation_phone1_label']           = 'หมายเลขโทรศัพท์ส่วนแรก';
$lang['create_user_validation_phone2_label']           = 'หมายเลขโทรศัพท์ส่วนที่สอง';
$lang['create_user_validation_phone3_label']           = 'หมายเลขโทรศัพท์ส่วนที่สาม';
$lang['create_user_validation_company_label']          = 'ชื่อบริษัท';
$lang['create_user_validation_password_label']         = 'รหัสผ่าน';
$lang['create_user_validation_password_confirm_label'] = 'ยืนยันรหัสผ่าน';

// แก้ไขผู้ใช้
$lang['edit_user_heading']                           = 'แก้ไขผู้ใช้';
$lang['edit_user_subheading']                        = 'กรุณากรอกรายละเอียดข้อมูลผู้ใช้';
$lang['edit_user_fname_label']                       = 'ชื่อ:';
$lang['edit_user_lname_label']                       = 'นามสกุล:';
$lang['edit_user_company_label']                     = 'ชื่อบริษัท:';
$lang['edit_user_email_label']                       = 'อีเมล์:';
$lang['edit_user_phone_label']                       = 'หมายเลขโทรศัพท์:';
$lang['edit_user_password_label']                    = 'รหัสผ่าน: (ถ้าจะแก้ไขรหัสผ่าน)';
$lang['edit_user_password_confirm_label']            = 'ยืนยันรหัสผ่าน: (ถ้าจะแก้ไขรหัสผ่าน)';
$lang['edit_user_groups_heading']                    = 'สมาชิกในกลุ่ม';
$lang['edit_user_submit_btn']                        = 'บันทึกผู้ใช้';
$lang['edit_user_validation_fname_label']            = 'ชื่อ';
$lang['edit_user_validation_lname_label']            = 'นามสกุล';
$lang['edit_user_validation_email_label']            = 'ที่อยู่อีเมล์';
$lang['edit_user_validation_phone1_label']           = 'หมายเลขโทรศัพท์ส่วนแรก';
$lang['edit_user_validation_phone2_label']           = 'หมายเลขโทรศัพท์ส่วนที่สอง';
$lang['edit_user_validation_phone3_label']           = 'หมายเลขโทรศัพท์ส่วนที่สาม';
$lang['edit_user_validation_company_label']          = 'ชื่อบริษัท';
$lang['edit_user_validation_groups_label']           = 'กลุ่ม';
$lang['edit_user_validation_password_label']         = 'รหัสผ่าน';
$lang['edit_user_validation_password_confirm_label'] = 'ยืนยันรหัสผ่าน';

// สร้างกลุ่ม
$lang['create_group_title']                  = 'สร้างกลุ่ม';
$lang['create_group_heading']                = 'สร้างกลุ่ม';
$lang['create_group_subheading']             = 'กรุณากรอกรายละเอียดกลุ่ม';
$lang['create_group_name_label']             = 'ชื่อกลุ่ม:';
$lang['create_group_desc_label']             = 'รายละเอียด:';
$lang['create_group_submit_btn']             = 'สร้างกลุ่ม';
$lang['create_group_validation_name_label']  = 'ชื่อกลุ่ม';
$lang['create_group_validation_desc_label']  = 'รายละเอียด';

// แก้ไขกลุ่ม
$lang['edit_group_title']                  = 'แก้ไขกลุ่ม';
$lang['edit_group_saved']                  = 'บันทึกกลุ่มเรียบร้อยแล้ว';
$lang['edit_group_heading']                = 'แก้ไขกลุ่ม';
$lang['edit_group_subheading']             = 'กรุณากรอกรายละเอียดกลุ่ม';
$lang['edit_group_name_label']             = 'ชื่อกลุ่ม:';
$lang['edit_group_desc_label']             = 'รายละเอียด:';
$lang['edit_group_submit_btn']             = 'บันทึกกลุ่ม';
$lang['edit_group_validation_name_label']  = 'ชื่อกลุ่ม';
$lang['edit_group_validation_desc_label']  = 'รายละเอียด';

// เปลี่ยนรหัสผ่าน
$lang['change_password_heading']                               = 'เปลี่ยนรหัสผ่าน';
$lang['change_password_old_password_label']                    = 'รหัสผ่านเดิม:';
$lang['change_password_new_password_label']                    = 'รหัสผ่านใหม่ (ต้องยาวอย่างอย่างน้อย %s ตัวอักษร):';
$lang['change_password_new_password_confirm_label']            = 'ยืนยันรหัสผ่านใหม่:';
$lang['change_password_submit_btn']                            = 'เปลี่ยน';
$lang['change_password_validation_old_password_label']         = 'รหัสผ่านเดิม';
$lang['change_password_validation_new_password_label']         = 'รหัสผ่านใหม่';
$lang['change_password_validation_new_password_confirm_label'] = 'ยืนยันรหัสผ่านใหม่';

// ลืมรหัสผ่าน
$lang['forgot_password_heading']                 = 'ลืมรหัสผ่าน';
$lang['forgot_password_subheading']              = 'กรุณากรอก%sของคุณเพื่อให้เราส่งอีเมล์ยืนยันรหัสผ่านใหม่ให้';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'ยอมรับ';
$lang['forgot_password_validation_email_label']  = 'ที่อยู่อีเมล์';
$lang['forgot_password_username_identity_label'] = 'ชื่อผู้ใช้';
$lang['forgot_password_email_identity_label']    = 'อีเมล์';
$lang['forgot_password_email_not_found']         = 'ไม่พบที่อยู่อีเมล์นี้ในสารบบ';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// ตั้งรหัสผ่านใหม่
$lang['reset_password_heading']                               = 'ตั้งรหัสผ่านใหม่';
$lang['reset_password_new_password_label']                    = 'รหัสผ่านใหม่ (ต้องยาวอย่างอย่างน้อย %s ตัวอักษร):';
$lang['reset_password_new_password_confirm_label']            = 'ยืนยันรหัสผ่านใหม่:';
$lang['reset_password_submit_btn']                            = 'เปลี่ยน';
$lang['reset_password_validation_new_password_label']         = 'รหัสผ่านใหม่';
$lang['reset_password_validation_new_password_confirm_label'] = 'ยืนยันรหัสผ่านใหม่';
