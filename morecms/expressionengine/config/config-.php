<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// =========================
// = Moresoda Config Items =
// =========================

$config['preview_folder']			= 'PREVIEW-FOLDER';  // ATTENTION - Folder on the preview site where the site is located
$config['site_index'] 				= APPLICATION_ENV == 'live' ? '' : 'index.php';
$config['document_root']			= APPLICATION_ENV == 'preview' ? $_SERVER['DOCUMENT_ROOT'] . '/' . $config['preview_folder'] : $_SERVER['DOCUMENT_ROOT'];

$config['save_tmpl_files'] 			= 'y'; //Just in case..

//Paths
$config['tmpl_file_basepath'] 		= $config['document_root'] . "/templates/";
$config['avatar_path'] 				= $config['document_root'] . "/images/avatars/";
$config['photo_path'] 				= $config['document_root'] . "/images/member_photos/";
$config['sig_img_path'] 			= $config['document_root'] . "/images/signature_attachments/";
$config['prv_msg_upload_path'] 		= $config['document_root'] . "/images/pm_attachments/";
$config['captcha_path'] 			= $config['document_root'] . "/images/captchas/";
$config['emoticon_path'] 			= $config['document_root'] . "/images/smileys/";
$config['theme_folder_path'] 		= $config['document_root'] . "/themes/";

//Urls
$config['avatar_url'] 				= "http://" . $_SERVER['HTTP_HOST'] . "/images/avatars/";
$config['photo_url'] 				= "http://" . $_SERVER['HTTP_HOST'] . "/images/member_photos/";
$config['sig_img_url'] 				= "http://" . $_SERVER['HTTP_HOST'] . "/images/signature_attachments/";
$config['site_url'] 				= "http://" . $_SERVER['HTTP_HOST'] . "/";
$config['theme_folder_url'] 		= "http://" . $_SERVER['HTTP_HOST'] . "/themes/";
$config['captcha_url'] 				= "http://" . $_SERVER['HTTP_HOST'] . "/images/captchas/";
