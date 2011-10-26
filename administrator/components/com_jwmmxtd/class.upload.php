<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * Copyright (c) Colin Verot 2003-2006. All rights reserved.
 * Version       0.21
 * Last modified 30/09/2006
 * Email         colin@verot.net
 * Web           http://www.verot.net
 **/

defined('_VALID_MOS') or die();
class upload {
	var $file_src_name;
	var $file_src_name_body;
	var $file_src_name_ext;
	var $file_src_mime;
	var $file_src_size;
	var $file_src_error;
	var $file_src_pathname;
	var $file_dst_path;
	var $file_dst_name;
	var $file_dst_name_body;
	var $file_dst_name_ext;
	var $file_dst_pathname;
	var $image_src_x;
	var $image_src_y;
	var $image_dst_x;
	var $image_dst_y;
	var $uploaded;
	var $no_upload_check;
	var $processed;
	var $error;
	var $log;
	var $file_new_name_body;
	var $file_name_body_add;
	var $file_new_name_ext;
	var $file_safe_name;
	var $mime_check;
	var $mime_magic_check;
	var $no_script;
	var $file_auto_rename;
	var $dir_auto_create;
	var $dir_auto_chmod;
	var $dir_chmod;
	var $file_overwrite;
	var $file_max_size;
	var $image_resize;
	var $image_convert;
	var $image_x;
	var $image_y;
	var $image_ratio;
	var $image_ratio_crop;
	var $image_ratio_no_zoom_in;
	var $image_ratio_no_zoom_out;
	var $image_ratio_x;
	var $image_ratio_y;
	var $jpeg_quality;
	var $jpeg_size;
	var $preserve_transparency;
	var $image_brightness;
	var $image_contrast;
	var $image_threshold;
	var $image_tint_color;
	var $image_overlay_color;
	var $image_overlay_percent;
	var $image_negative;
	var $image_greyscale;
	var $image_text;
	var $image_text_direction;
	var $image_text_color;
	var $image_text_percent;
	var $image_text_background;
	var $image_text_background_percent;
	var $image_text_font;
	var $image_text_position;
	var $image_text_x;
	var $image_text_y;
	var $image_text_padding;
	var $image_text_padding_x;
	var $image_text_padding_y;
	var $image_flip;
	var $image_rotate;
	var $image_crop;
	var $image_bevel;
	var $image_bevel_color1;
	var $image_bevel_color2;
	var $image_border;
	var $image_border_color;
	var $image_frame;
	var $image_frame_colors;
	var $image_watermark;
	var $image_watermark_position;
	var $image_watermark_x;
	var $image_watermark_y;
	var $allowed;
	function init() {
		$this->file_new_name_body = '';
		$this->file_name_body_add = '';
		$this->file_new_name_ext = '';
		$this->file_safe_name = true;
		$this->file_overwrite = false;
		$this->file_auto_rename = true;
		$this->dir_auto_create = true;
		$this->dir_auto_chmod = true;
		$this->dir_chmod = 0777;
		$this->mime_check = true;
		$this->mime_magic_check = false;
		$this->no_script = true;
		$val = trim(ini_get('upload_max_filesize'));
		$last = strtolower($val{strlen($val) - 1});
		switch($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		$this->file_max_size = $val;
		$this->image_resize = false;
		$this->image_convert = '';
		$this->image_x = 150;
		$this->image_y = 150;
		$this->image_ratio = false;
		$this->image_ratio_crop = false;
		$this->image_ratio_no_zoom_in = false;
		$this->image_ratio_no_zoom_out = false;
		$this->image_ratio_x = false;
		$this->image_ratio_y = false;
		$this->jpeg_quality = 75;
		$this->jpeg_size = null;
		$this->preserve_transparency = false;
		$this->image_brightness = null;
		$this->image_contrast = null;
		$this->image_threshold = null;
		$this->image_tint_color = null;
		$this->image_overlay_color = null;
		$this->image_overlay_percent = null;
		$this->image_negative = false;
		$this->image_greyscale = false;
		$this->image_text = null;
		$this->image_text_direction = null;
		$this->image_text_color = '#FFFFFF';
		$this->image_text_percent = 100;
		$this->image_text_background = null;
		$this->image_text_background_percent = 100;
		$this->image_text_font = 5;
		$this->image_text_x = null;
		$this->image_text_y = null;
		$this->image_text_position = null;
		$this->image_text_padding = 0;
		$this->image_text_padding_x = null;
		$this->image_text_padding_y = null;
		$this->image_watermark = null;
		$this->image_watermark_x = null;
		$this->image_watermark_y = null;
		$this->image_watermark_position = null;
		$this->image_flip = null;
		$this->image_rotate = null;
		$this->image_crop = null;
		$this->image_bevel = null;
		$this->image_bevel_color1 = '#FFFFFF';
		$this->image_bevel_color2 = '#000000';
		$this->image_border = null;
		$this->image_border_color = '#FFFFFF';
		$this->image_frame = null;
		$this->image_frame_colors = '#FFFFFF #999999 #666666 #000000';
		$this->allowed = array("flv-application/octet-stream", "application/arj",
				"application/excel", "application/gnutar", "application/msword",
				"application/mspowerpoint", "application/octet-stream", "application/pdf",
				"application/powerpoint", "application/postscript", "application/plain",
				"application/rtf", "application/vnd.ms-excel", "application/vocaltec-media-file",
				"application/wordperfect", "application/x-bzip", "application/x-bzip2",
				"application/x-compressed", "application/x-excel", "application/x-gzip",
				"application/x-latex", "application/x-midi", "application/x-msexcel",
				"application/x-rtf", "application/x-sit", "application/x-stuffit",
				"application/x-shockwave-flash", "application/x-troff-msvideo",
				"application/x-zip-compressed", "application/xml", "application/zip",
				"audio/aiff", "audio/basic", "audio/midi", "audio/mod", "audio/mpeg",
				"audio/mpeg3", "audio/wav", "audio/x-aiff", "audio/x-au", "audio/x-mid",
				"audio/x-midi", "audio/x-mod", "audio/x-mpeg-3", "audio/x-wav", "audio/xm",
				"image/bmp", "image/gif", "image/jpeg", "image/pjpeg", "image/png", "image/tiff",
				"image/x-tiff", "image/x-windows-bmp", "multipart/x-zip", "multipart/x-gzip",
				"music/crescendo", "text/richtext", "text/plain", "text/xml", "video/avi",
				"video/mpeg", "video/msvideo", "video/quicktime", "video/quicktime",
				"video/x-mpeg", "video/x-ms-asf", "video/x-ms-asf-plugin", "video/x-msvideo",
				"video/x-flv", "x-music/x-midi");
	}
	function upload($file) {
		$this->file_src_name = '';
		$this->file_src_name_body = '';
		$this->file_src_name_ext = '';
		$this->file_src_mime = '';
		$this->file_src_size = '';
		$this->file_src_error = '';
		$this->file_src_pathname = '';
		$this->file_dst_path = '';
		$this->file_dst_name = '';
		$this->file_dst_name_body = '';
		$this->file_dst_name_ext = '';
		$this->file_dst_pathname = '';
		$this->image_src_x = 0;
		$this->image_src_y = 0;
		$this->image_dst_type = '';
		$this->image_dst_x = 0;
		$this->image_dst_y = 0;
		$this->uploaded = true;
		$this->no_upload_check = false;
		$this->processed = true;
		$this->error = '';
		$this->log = '';
		$this->allowed = array();
		$this->init();
		if(!$file) {
			$this->uploaded = false;
			$this->error = _("File error. Please try again");
		}
		if(!is_array($file)) {
			if(empty($file)) {
				$this->uploaded = false;
				$this->error = _("File error. Please try again");
			} else {
				$this->no_upload_check = true;
				$this->log .= '<b>'._("source is a local file").' '.$file.'</b><br />';
				if($this->uploaded && !file_exists($file)) {
					$this->uploaded = false;
					$this->error = _("Local file doesn't exist");
				}
				if($this->uploaded && !is_readable($file)) {
					$this->uploaded = false;
					$this->error = _("Local file is not readable");
				}
				if($this->uploaded) {
					$this->file_src_pathname = $file;
					$this->file_src_name = basename($file);
					$this->log .= '- '._("local file name OK").'<br />';
					preg_match('/\.([^\.]*$/)', $this->file_src_name, $extension);
					if(is_array($extension)) {
						$this->file_src_name_ext = strtolower($extension[1]);
						$this->file_src_name_body = substr($this->file_src_name, 0, ((strlen($this->file_src_name) - strlen($this->file_src_name_ext))) - 1);
					} else {
						$this->file_src_name_ext = '';
						$this->file_src_name_body = $this->file_src_name;
					}
					$this->file_src_size = (file_exists($file)?filesize($file):0);
					$info = getimagesize($this->file_src_pathname);
					$this->file_src_mime = (array_key_exists('mime', $info)?$info['mime']:null);
					if(empty($this->file_src_mime)) {
						$mime = (array_key_exists(2, $info)?$info[2]:null);
						$this->file_src_mime = ($mime == 1?'image/gif':($mime == 2?'image/jpeg':($mime == 3?'image/png':null)));
					}
					if(empty($this->file_src_mime) && function_exists('mime_content_type')) {
						$this->file_src_mime = mime_content_type($this->file_src_pathname);
					}
					$this->file_src_error = 0;
				}
			}
		} else {
			$this->log .= '<b>'._("source is an uploaded file").'</b><br />';
			if($this->uploaded) {
				$this->file_src_error = $file['error'];
				switch($this->file_src_error) {
					case 0:
						$this->log .= '- '._("upload OK").'<br />';
						break;
					case 1:
						$this->uploaded = false;
						$this->error = _("File upload error (the uploaded file exceeds the upload_max_filesize directive in php.ini)");
						break;
					case 2:
						$this->uploaded = false;
						$this->error = _("File upload error (the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form)");
						break;
					case 3:
						$this->uploaded = false;
						$this->error = _("File upload error (the uploaded file was only partially uploaded)");
						break;
					case 4:
						$this->uploaded = false;
						$this->error = _("File upload error (no file was uploaded)");
						break;
					default:
						$this->uploaded = false;
						$this->error = _("File upload error (unknown error code)");
				}
			}
			if($this->uploaded) {
				$this->file_src_pathname = $file['tmp_name'];
				$this->file_src_name = $file['name'];
				if($this->file_src_name == '') {
					$this->uploaded = false;
					$this->error = _("File upload error. Please try again");
				}
			}
			if($this->uploaded) {
				$this->log .= '- '._("file name OK").'<br />';
				preg_match('/\.([^\.]*$)/', $this->file_src_name, $extension);
				if(is_array($extension)) {
					$this->file_src_name_ext = strtolower($extension[1]);
					$this->file_src_name_body = substr($this->file_src_name, 0, ((strlen($this->file_src_name) -
									strlen($this->file_src_name_ext))) - 1);
				} else {
					$this->file_src_name_ext = '';
					$this->file_src_name_body = $this->file_src_name;
				}
				$this->file_src_size = $file['size'];
				$this->file_src_mime = $file['type'];
			}
		}
		$this->log .= '- '._("source variables").'<br />';
		$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name         : '.$this->file_src_name.
				'<br />';
		$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name_body    : '.$this->file_src_name_body.
				'<br />';
		$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name_ext     : '.$this->file_src_name_ext.
				'<br />';
		$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_pathname     : '.$this->file_src_pathname.
				'<br />';
		$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_mime         : '.$this->file_src_mime.
				'<br />';
		$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_size         : '.$this->file_src_size.
				' (max= '.$this->file_max_size.')<br />';
		$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_error        : '.$this->file_src_error.
				'<br />';
	}
	function gd_version() {
		static $gd_version = null;
		if($gd_version === null) {
			if(function_exists('gd_info')) {
				$gd = gd_info();
				$gd = $gd["GD Version"];
				$regex = "/([\d\.]+)/i";
			} else {
				ob_start();
				phpinfo(8);
				$gd = ob_get_contents();
				ob_end_clean();
				$regex = "/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i";
			}
			if(preg_match($regex, $gd, $m)) {
				$gd_version = (float)$m[1];
			} else {
				$gd_version = 0;
			}
		}
		return $gd_version;
	}
	function r_mkdir($path, $mode = 0777) {
		return is_dir($path) || ($this->r_mkdir(dirname($path), $mode) && @mkdir($path,
						$mode));
	}
	function process($server_path) {
		$this->error = '';
		$this->processed = true;
		if(strtolower(substr(PHP_OS, 0, 3)) === 'win') {
			if(substr($server_path, -1, 1) != '\\')
				$server_path = $server_path.'\\';
		} else {
			if(substr($server_path, -1, 1) != '/')
				$server_path = $server_path.'/';
		}
		$this->log .= '<b>'._("process file to").' '.$server_path.'</b><br />';
		if($this->uploaded) {
			if($this->file_src_size > $this->file_max_size) {
				$this->processed = false;
				$this->error = _("File too big");
			} else {
				$this->log .= '- '._("file size OK").'<br />';
			}
			if($this->no_script) {
				if(((substr($this->file_src_mime, 0, 5) == 'text/' || strpos($this->file_src_mime,
										'javascript') !== false) && (substr($this->file_src_name, -4) != '.txt')) ||
						preg_match('/\.(php|pl|py|cgi|asp)$/i', $this->file_src_name) || empty($this->file_src_name_ext)) {
					$this->file_src_mime = 'text/plain';
					$this->log .= '- '._("script").' '.$this->file_src_name.' '._("renamed as").' '.
							$this->file_src_name.'.txt!<br />';
					$this->file_src_name_ext .= (empty($this->file_src_name_ext)?'txt':'.txt');
				}
			}
			if($this->mime_magic_check && function_exists('mime_content_type')) {
				$detected_mime = mime_content_type($this->file_src_pathname);
				if($this->file_src_mime != $detected_mime) {
					$this->log .= '- '._("MIME type detected as").' '.$detected_mime.' '._("but given as").
							' '.$this->file_src_mime.'!<br />';
					$this->file_src_mime = $detected_mime;
				}
			}
			if($this->mime_check && empty($this->file_src_mime)) {
				$this->processed = false;
				$this->error = _("MIME type can't be detected!");
			} else
			if($this->mime_check && !empty($this->file_src_mime) && !array_key_exists($this->file_src_mime,
					array_flip($this->allowed))) {
				$this->processed = false;
				$this->error = _("Incorrect type of file");
			} else {
				$this->log .= '- '._("file mime OK").' : '.$this->file_src_mime.'<br />';
			}
		} else {
			$this->error = _("File not uploaded. Can't carry on a process");
			$this->processed = false;
		}
		if($this->processed) {
			$this->file_dst_path = $server_path;
			$this->file_dst_name = $this->file_src_name;
			$this->file_dst_name_body = $this->file_src_name_body;
			$this->file_dst_name_ext = $this->file_src_name_ext;
			if($this->file_new_name_body != '') {
				$this->file_dst_name_body = $this->file_new_name_body;
				$this->log .= '- '._("new file name body").' : '.$this->file_new_name_body.
						'<br />';
			}
			if($this->file_new_name_ext != '') {
				$this->file_dst_name_ext = $this->file_new_name_ext;
				$this->log .= '- '._("new file name ext").' : '.$this->file_new_name_ext.
						'<br />';
			}
			if($this->file_name_body_add != '') {
				$this->file_dst_name_body = $this->file_dst_name_body.$this->file_name_body_add;
				$this->log .= '- '._("file name body add").' : '.$this->file_name_body_add.
						'<br />';
			}
			if($this->file_safe_name) {
				$this->file_dst_name_body = str_replace(array(' ', '-'), array('_', '_'), $this->file_dst_name_body);
				$this->file_dst_name_body = preg_replace('/[^A-Za-z0-9_]/', '', $this->file_dst_name_body);
				$this->log .= '- '._("file name safe format").'<br />';
			}
			$this->log .= '- '._("destination variables").'<br />';
			$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_path         : '.$this->file_dst_path.
					'<br />';
			$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name_body    : '.$this->file_dst_name_body.
					'<br />';
			$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name_ext     : '.$this->file_dst_name_ext.
					'<br />';
			$image_manipulation = ($this->image_resize || $this->image_convert != '' ||
							is_numeric($this->image_brightness) || is_numeric($this->image_contrast) ||
							is_numeric($this->image_threshold) || !empty($this->image_tint_color) || !empty
							($this->image_overlay_color) || !empty($this->image_text) || $this->image_greyscale ||
							$this->image_negative || !empty($this->image_watermark) || is_numeric($this->image_rotate) ||
							is_numeric($this->jpeg_size) || !empty($this->image_flip) || !empty($this->image_crop) ||
							!empty($this->image_border) || $this->image_frame > 0 || $this->image_bevel > 0);
			if($image_manipulation) {
				if($this->image_convert == '') {
					$this->file_dst_name = $this->file_dst_name_body.(!empty($this->file_dst_name_ext)?
							'.'.$this->file_dst_name_ext:'');
					$this->log .= '- '._("image operation, keep extension").'<br />';
				} else {
					$this->file_dst_name = $this->file_dst_name_body.'.'.$this->image_convert;
					$this->log .= '- '._("image operation, change extension for conversion type").
							'<br />';
				}
			} else {
				$this->file_dst_name = $this->file_dst_name_body.(!empty($this->file_dst_name_ext)?
						'.'.$this->file_dst_name_ext:'');
				$this->log .= '- '._("no image operation, keep extension").'<br />';
			}
			if(!$this->file_auto_rename) {
				$this->log .= '- '._("no auto_rename if same filename exists").'<br />';
				$this->file_dst_pathname = $this->file_dst_path.$this->file_dst_name;
			} else {
				$this->log .= '- '._("checking for auto_rename").'<br />';
				$this->file_dst_pathname = $this->file_dst_path.$this->file_dst_name;
				$body = $this->file_dst_name_body;
				$cpt = 1;
				while(@file_exists($this->file_dst_pathname)) {
					$this->file_dst_name_body = $body.'_'.$cpt;
					$this->file_dst_name = $this->file_dst_name_body.(!empty($this->file_dst_name_ext)?
							'.'.$this->file_dst_name_ext:'');
					$cpt++;
					$this->file_dst_pathname = $this->file_dst_path.$this->file_dst_name;
				}
				if($cpt > 1)
					$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("auto_rename to").' '.$this->file_dst_name.
							'<br />';
			}
			$this->log .= '- '._("destination file details").'<br />';
			$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name         : '.$this->file_dst_name.
					'<br />';
			$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_pathname     : '.$this->file_dst_pathname.
					'<br />';
			if($this->file_overwrite) {
				$this->log .= '- '._("no overwrite checking").'<br />';
			} else {
				if(@file_exists($this->file_dst_pathname)) {
					$this->processed = false;
					$this->error = $this->file_dst_name.' '._("already exists. Please change the file name");
				} else {
					$this->log .= '- '.$this->file_dst_name.' '._("doesn't exist already").'<br />';
				}
			}
		} else {
			$this->processed = false;
		}
		if(!$this->no_upload_check && !is_uploaded_file($this->file_src_pathname)) {
			$this->processed = false;
			$this->error = _("No correct source file. Can't carry on a process");
		}
		if($this->processed && !file_exists($this->file_src_pathname)) {
			$this->processed = false;
			$this->error = _("No source file. Can't carry on a process");
		}
		if($this->processed && !($f = @fopen($this->file_src_pathname, 'r'))) {
			$this->processed = false;
			if(is_readable($this->file_src_pathname)) {
				$this->error = _("Source file is not readable. open_basedir restriction in place?");
			} else {
				$this->error = _("Source file is not readable. Can't carry on a process");
			}
		} else {
			@fclose($f);
		}
		if($this->processed && !file_exists($this->file_dst_path)) {
			if($this->dir_auto_create) {
				$this->log .= '- '.$this->file_dst_path.' '._("doesn't exist. Attempting creation:");
				if(!$this->r_mkdir($this->file_dst_path, $this->dir_chmod)) {
					$this->log .= ' '._("failed").'<br />';
					$this->processed = false;
					$this->error = _("Destination directory can't be created. Can't carry on a process");
				} else {
					$this->log .= ' '._("success").'<br />';
				}
			} else {
				$this->error = _("Destination directory doesn't exist. Can't carry on a process");
			}
		}
		if($this->processed && !is_dir($this->file_dst_path)) {
			$this->processed = false;
			$this->error = _("Destination path is not a directory. Can't carry on a process");
		}
		if($this->processed && !($f = @fopen($this->file_dst_pathname, 'w+'))) {
			if($this->dir_auto_chmod) {
				$this->log .= '- '.$this->file_dst_path.' '._("is not writeable. Attempting chmod:");
				if(!@chmod($this->file_dst_path, $this->dir_chmod)) {
					$this->log .= ' '._("failed").'<br />';
					$this->processed = false;
					$this->error = _("Destination directory can't be made writeable. Can't carry on a process");
				} else {
					$this->log .= ' '._("success").'<br />';
					if(!($f = @fopen($this->file_dst_pathname, 'w+'))) {
						$this->processed = false;
						$this->error = _("Destination directory is still not writeable. Can't carry on a process");
					} else {
						@fclose($f);
					}
				}
			} else {
				$this->processed = false;
				$this->error = _("Destination path is not a writeable. Can't carry on a process");
			}
		} else {
			@fclose($f);
		}
		if($this->processed) {
			if($image_manipulation) {
				$this->log .= '- '._("image resizing or conversion wanted").'<br />';
				if($this->gd_version()) {
					switch($this->file_src_mime) {
						case 'image/pjpeg':
						case 'image/jpeg':
						case 'image/jpg':
							if(!function_exists('imagecreatefromjpeg')) {
								$this->processed = false;
								$this->error = _("No create from JPEG support");
							} else {
								$image_src = @imagecreatefromjpeg($this->file_src_pathname);
								if(!$image_src) {
									$this->processed = false;
									$this->error = _("Error in creating JPEG image from source");
								} else {
									$this->log .= '- '._("source image is JPEG").'<br />';
								}
							}
							break;
						case 'image/png':
							if(!function_exists('imagecreatefrompng')) {
								$this->processed = false;
								$this->error = _("No create from PNG support");
							} else {
								$image_src = @imagecreatefrompng($this->file_src_pathname);
								if(!$image_src) {
									$this->processed = false;
									$this->error = _("Error in creating PNG image from source");
								} else {
									$this->log .= '- '._("source image is PNG").'<br />';
								}
							}
							break;
						case 'image/gif':
							if(!function_exists('imagecreatefromgif')) {
								$this->processed = false;
								$this->error = _("Error in creating GIF image from source");
							} else {
								$image_src = @imagecreatefromgif($this->file_src_pathname);
								if(!$image_src) {
									$this->processed = false;
									$this->error = _("No GIF read support");
								} else {
									$this->log .= '- '._("source image is GIF").'<br />';
								}
							}
							break;
						default:
							$this->processed = false;
							$this->error = _("Can't read image source. not an image?");
					}
				} else {
					$this->processed = false;
					$this->error = _("GD doesn't seem to be present");
				}
				if($this->processed && $image_src) {
					$this->image_src_x = imagesx($image_src);
					$this->image_src_y = imagesy($image_src);
					$this->image_dst_x = $this->image_src_x;
					$this->image_dst_y = $this->image_src_y;
					$gd_version = $this->gd_version();
					$ratio_crop = null;
					if($this->image_resize) {
						$this->log .= '- '._("resizing...").'<br />';
						if($this->image_ratio_x) {
							$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("calculate x size").'<br />';
							$this->image_dst_x = round(($this->image_src_x * $this->image_y) / $this->image_src_y);
							$this->image_dst_y = $this->image_y;
						} else
						if($this->image_ratio_y) {
							$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("calculate y size").'<br />';
							$this->image_dst_x = $this->image_x;
							$this->image_dst_y = round(($this->image_src_y * $this->image_x) / $this->image_src_x);
						} else
						if($this->image_ratio || $this->image_ratio_crop || $this->image_ratio_no_zoom_in ||
								$this->image_ratio_no_zoom_out) {
							$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("check x/y sizes").'<br />';
							if((!$this->image_ratio_no_zoom_in && !$this->image_ratio_no_zoom_out) || ($this->image_ratio_no_zoom_in &&
											($this->image_src_x > $this->image_x || $this->image_src_y > $this->image_y)) ||
									($this->image_ratio_no_zoom_out && $this->image_src_x < $this->image_x && $this->image_src_y <
											$this->image_y)) {
								$this->image_dst_x = $this->image_x;
								$this->image_dst_y = $this->image_y;
								if($this->image_ratio_crop) {
									if(($this->image_src_x / $this->image_x) > ($this->image_src_y / $this->image_y)) {
										$this->image_dst_y = $this->image_y;
										$this->image_dst_x = intval($this->image_src_x * ($this->image_y / $this->image_src_y));
										$ratio_crop = array();
										$ratio_crop['x'] = $this->image_dst_x - $this->image_x;
										$ratio_crop['l'] = round($ratio_crop['x'] / 2);
										$ratio_crop['r'] = $ratio_crop['x'] - $ratio_crop['l'];
										$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("ratio_crop_x").'         : '.$ratio_crop['x'].
												' ('.$ratio_crop['l'].';'.$ratio_crop['r'].')<br />';
										if(is_null($this->image_crop))
											$this->image_crop = array(0, 0, 0, 0);
									} else {
										$this->image_dst_x = $this->image_x;
										$this->image_dst_y = intval($this->image_src_y * ($this->image_x / $this->image_src_x));
										$ratio_crop = array();
										$ratio_crop['y'] = $this->image_dst_y - $this->image_y;
										$ratio_crop['t'] = round($ratio_crop['y'] / 2);
										$ratio_crop['b'] = $ratio_crop['y'] - $ratio_crop['t'];
										$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("ratio_crop_y").'         : '.$ratio_crop['y'].
												' ('.$ratio_crop['t'].';'.$ratio_crop['b'].')<br />';
										if(is_null($this->image_crop))
											$this->image_crop = array(0, 0, 0, 0);
									}
								} else {
									if(($this->image_src_x / $this->image_x) > ($this->image_src_y / $this->image_y)) {
										$this->image_dst_x = $this->image_x;
										$this->image_dst_y = intval($this->image_src_y * ($this->image_x / $this->image_src_x));
									} else {
										$this->image_dst_y = $this->image_y;
										$this->image_dst_x = intval($this->image_src_x * ($this->image_y / $this->image_src_y));
									}
								}
							} else {
								$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("doesn't calculate x/y sizes").
										'<br />';
								$this->image_dst_x = $this->image_src_x;
								$this->image_dst_y = $this->image_src_y;
							}
						} else {
							$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("use plain sizes").'<br />';
							$this->image_dst_x = $this->image_x;
							$this->image_dst_y = $this->image_y;
						}
						if($this->preserve_transparency && $this->file_src_mime != 'image/gif' && $this->file_src_mime !=
								'image/png')
							$this->preserve_transparency = false;
						if($gd_version >= 2 && !$this->preserve_transparency) {
							$image_dst = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						} else {
							$image_dst = imagecreate($this->image_dst_x, $this->image_dst_y);
						}
						if($this->preserve_transparency) {
							$this->log .= '- '._("preserve transparency").'<br />';
							$transparent_color = imagecolortransparent($image_src);
							imagepalettecopy($image_dst, $image_src);
							imagefill($image_dst, 0, 0, $transparent_color);
							imagecolortransparent($image_dst, $transparent_color);
						}
						if($gd_version >= 2 && !$this->preserve_transparency) {
							$res = imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, $this->image_dst_x,
									$this->image_dst_y, $this->image_src_x, $this->image_src_y);
						} else {
							$res = imagecopyresized($image_dst, $image_src, 0, 0, 0, 0, $this->image_dst_x,
									$this->image_dst_y, $this->image_src_x, $this->image_src_y);
						}
						$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("resized image object created").
								'<br />';
						$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;image_src_x y        : '.$this->image_src_x.
								' x '.$this->image_src_y.'<br />';
						$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;image_dst_x y        : '.$this->image_dst_x.
								' x '.$this->image_dst_y.'<br />';
					} else {
						$image_dst = &$image_src;
					}
					if(empty($this->image_convert)) {
						$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("setting destination file type to").
								' '.$this->file_src_name_ext.'<br />';
						$this->image_convert = $this->file_src_name_ext;
					}
					if($gd_version >= 2 && (!empty($this->image_crop) || !is_null($ratio_crop))) {
						if(is_array($this->image_crop)) {
							$vars = $this->image_crop;
						} else {
							$vars = explode(' ', $this->image_crop);
						}
						if(sizeof($vars) == 4) {
							$ct = $vars[0];
							$cr = $vars[1];
							$cb = $vars[2];
							$cl = $vars[3];
						} else
						if(sizeof($vars) == 2) {
							$ct = $vars[0];
							$cr = $vars[1];
							$cb = $vars[0];
							$cl = $vars[1];
						} else {
							$ct = $vars[0];
							$cr = $vars[0];
							$cb = $vars[0];
							$cl = $vars[0];
						}
						if(strpos($ct, '%') > 0)
							$ct = $this->image_dst_y * (str_replace('%', '', $ct) / 100);
						if(strpos($cr, '%') > 0)
							$cr = $this->image_dst_x * (str_replace('%', '', $cr) / 100);
						if(strpos($cb, '%') > 0)
							$cb = $this->image_dst_y * (str_replace('%', '', $cb) / 100);
						if(strpos($cl, '%') > 0)
							$cl = $this->image_dst_x * (str_replace('%', '', $cl) / 100);
						if(strpos($ct, 'px') > 0)
							$ct = str_replace('px', '', $ct);
						if(strpos($cr, 'px') > 0)
							$cr = str_replace('px', '', $cr);
						if(strpos($cb, 'px') > 0)
							$cb = str_replace('px', '', $cb);
						if(strpos($cl, 'px') > 0)
							$cl = str_replace('px', '', $cl);
						$ct = (int)$ct;
						$cr = (int)$cr;
						$cb = (int)$cb;
						$cl = (int)$cl;
						if(!is_null($ratio_crop)) {
							if(array_key_exists('t', $ratio_crop))
								$ct += $ratio_crop['t'];
							if(array_key_exists('r', $ratio_crop))
								$cr += $ratio_crop['r'];
							if(array_key_exists('b', $ratio_crop))
								$cb += $ratio_crop['b'];
							if(array_key_exists('l', $ratio_crop))
								$cl += $ratio_crop['l'];
						}
						$this->log .= '- '._("crop image").' : '.$ct.' '.$cr.' '.$cb.' '.$cl.' <br />';
						$this->image_dst_x = $this->image_dst_x - $cl - $cr;
						$this->image_dst_y = $this->image_dst_y - $ct - $cb;
						if($this->image_dst_x < 1)
							$this->image_dst_x = 1;
						if($this->image_dst_y < 1)
							$this->image_dst_y = 1;
						$tmp = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						imagecopy($tmp, $image_dst, 0, 0, $cl, $ct, $this->image_dst_x, $this->image_dst_y);
						imagedestroy($image_dst);
						$image_dst = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						imagecopy($image_dst, $tmp, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y);
						imagedestroy($tmp);
					}
					if($gd_version >= 2 && !empty($this->image_flip)) {
						$this->image_flip = strtolower($this->image_flip);
						$this->log .= '- '._("flip image").' : '.$this->image_flip.'<br />';
						$tmp = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						for($x = 0; $x < $this->image_dst_x; $x++) {
							for($y = 0; $y < $this->image_dst_y; $y++) {
								if(strpos($this->image_flip, 'v') !== false) {
									imagecopy($tmp, $image_dst, $this->image_dst_x - $x - 1, $y, $x, $y, 1, 1);
								} else {
									imagecopy($tmp, $image_dst, $x, $this->image_dst_y - $y - 1, $x, $y, 1, 1);
								}
							}
						}
						imagedestroy($image_dst);
						$image_dst = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						imagecopy($image_dst, $tmp, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y);
						imagedestroy($tmp);
					}
					if($gd_version >= 2 && is_numeric($this->image_rotate)) {
						if(!in_array($this->image_rotate, array(0, 90, 180, 270)))
							$this->image_rotate = 0;
						if($this->image_rotate != 0) {
							if($this->image_rotate == 90 || $this->image_rotate == 270) {
								$tmp = imagecreatetruecolor($this->image_dst_y, $this->image_dst_x);
							} else {
								$tmp = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
							}
							$this->log .= '- '._("rotate image").' : '.$this->image_rotate.'<br />';
							for($x = 0; $x < $this->image_dst_x; $x++) {
								for($y = 0; $y < $this->image_dst_y; $y++) {
									if($this->image_rotate == 90) {
										imagecopy($tmp, $image_dst, $y, $x, $x, $this->image_dst_y - $y - 1, 1, 1);
									} else
									if($this->image_rotate == 180) {
										imagecopy($tmp, $image_dst, $x, $y, $this->image_dst_x - $x - 1, $this->image_dst_y -
												$y - 1, 1, 1);
									} else
									if($this->image_rotate == 270) {
										imagecopy($tmp, $image_dst, $y, $x, $this->image_dst_x - $x - 1, $y, 1, 1);
									} else {
										imagecopy($tmp, $image_dst, $x, $y, $x, $y, 1, 1);
									}
								}
							}
							if($this->image_rotate == 90 || $this->image_rotate == 270) {
								$t = $this->image_dst_y;
								$this->image_dst_y = $this->image_dst_x;
								$this->image_dst_x = $t;
							}
							imagedestroy($image_dst);
							$image_dst = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
							imagecopy($image_dst, $tmp, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y);
							imagedestroy($tmp);
						}
					}
					if($gd_version >= 2 && (is_numeric($this->image_overlay_percent) && !empty($this->image_overlay_color))) {
						$this->log .= '- '._("apply color overlay").'<br />';
						sscanf($this->image_overlay_color, "#%2x%2x%2x", $red, $green, $blue);
						$filter = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						$color = imagecolorallocate($filter, $red, $green, $blue);
						imagefilledrectangle($filter, 0, 0, $this->image_dst_x, $this->image_dst_y, $color);
						imagecopymerge($image_dst, $filter, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y,
								$this->image_overlay_percent);
						imagedestroy($filter);
					}
					if($gd_version >= 2 && ($this->image_negative || $this->image_greyscale ||
									is_numeric($this->image_threshold) || is_numeric($this->image_brightness) ||
									is_numeric($this->image_contrast) || !empty($this->image_tint_color))) {
						$this->log .= '- '._("apply tint, light, contrast correction, negative, greyscale and threshold").
								'<br />';
						if(!empty($this->image_tint_color))
							sscanf($this->image_tint_color, "#%2x%2x%2x", $red, $green, $blue);
						$background = imagecolorallocatealpha($image_dst, 255, 255, 255, 0);
						imagefill($image_dst, 0, 0, $background);
						imagealphablending($image_dst, true);
						for($y = 0; $y < $this->image_dst_y; $y++) {
							for($x = 0; $x < $this->image_dst_x; $x++) {
								if($this->image_greyscale) {
									$rgb = imagecolorat($image_dst, $x, $y);
									$pixel = imagecolorsforindex($image_dst, $rgb);
									$r = $g = $b = round((0.2125 * $pixel['red']) + (0.7154 * $pixel['green']) + (0.0721 *
													$pixel['blue']));
									$a = $pixel['alpha'];
									$pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
									imagesetpixel($image_dst, $x, $y, $pixelcolor);
								}
								if(is_numeric($this->image_threshold)) {
									$rgb = imagecolorat($image_dst, $x, $y);
									$pixel = imagecolorsforindex($image_dst, $rgb);
									$c = (round($pixel['red'] + $pixel['green'] + $pixel['blue']) / 3) - 127;
									$r = $g = $b = ($c > $this->image_threshold?255:0);
									$a = $pixel['alpha'];
									$pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
									imagesetpixel($image_dst, $x, $y, $pixelcolor);
								}
								if(is_numeric($this->image_brightness)) {
									$rgb = imagecolorat($image_dst, $x, $y);
									$pixel = imagecolorsforindex($image_dst, $rgb);
									$r = max(min(round($pixel['red'] + (($this->image_brightness * 2))), 255), 0);
									$g = max(min(round($pixel['green'] + (($this->image_brightness * 2))), 255), 0);
									$b = max(min(round($pixel['blue'] + (($this->image_brightness * 2))), 255), 0);
									$a = $pixel['alpha'];
									$pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
									imagesetpixel($image_dst, $x, $y, $pixelcolor);
								}
								if(is_numeric($this->image_contrast)) {
									$rgb = imagecolorat($image_dst, $x, $y);
									$pixel = imagecolorsforindex($image_dst, $rgb);
									$r = max(min(round(($this->image_contrast + 128) * $pixel['red'] / 128), 255), 0);
									$g = max(min(round(($this->image_contrast + 128) * $pixel['green'] / 128), 255),
											0);
									$b = max(min(round(($this->image_contrast + 128) * $pixel['blue'] / 128), 255),
											0);
									$a = $pixel['alpha'];
									$pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
									imagesetpixel($image_dst, $x, $y, $pixelcolor);
								}
								if(!empty($this->image_tint_color)) {
									$rgb = imagecolorat($image_dst, $x, $y);
									$pixel = imagecolorsforindex($image_dst, $rgb);
									$r = min(round($red * $pixel['red'] / 169), 255);
									$g = min(round($green * $pixel['green'] / 169), 255);
									$b = min(round($blue * $pixel['blue'] / 169), 255);
									$a = $pixel['alpha'];
									$pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
									imagesetpixel($image_dst, $x, $y, $pixelcolor);
								}
								if(!empty($this->image_negative)) {
									$rgb = imagecolorat($image_dst, $x, $y);
									$pixel = imagecolorsforindex($image_dst, $rgb);
									$r = round(255 - $pixel['red']);
									$g = round(255 - $pixel['green']);
									$b = round(255 - $pixel['blue']);
									$a = $pixel['alpha'];
									$pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
									imagesetpixel($image_dst, $x, $y, $pixelcolor);
								}
							}
						}
					}
					if($gd_version >= 2 && !empty($this->image_border)) {
						if(is_array($this->image_border)) {
							$vars = $this->image_border;
							$this->log .= '- '._("add border").' : '.implode(' ', $this->image_border).
									'<br />';
						} else {
							$this->log .= '- '._("add border").' : '.$this->image_border.'<br />';
							$vars = explode(' ', $this->image_border);
						}
						if(sizeof($vars) == 4) {
							$ct = $vars[0];
							$cr = $vars[1];
							$cb = $vars[2];
							$cl = $vars[3];
						} else
						if(sizeof($vars) == 2) {
							$ct = $vars[0];
							$cr = $vars[1];
							$cb = $vars[0];
							$cl = $vars[1];
						} else {
							$ct = $vars[0];
							$cr = $vars[0];
							$cb = $vars[0];
							$cl = $vars[0];
						}
						if(strpos($ct, '%') > 0)
							$ct = $this->image_dst_y * (str_replace('%', '', $ct) / 100);
						if(strpos($cr, '%') > 0)
							$cr = $this->image_dst_x * (str_replace('%', '', $cr) / 100);
						if(strpos($cb, '%') > 0)
							$cb = $this->image_dst_y * (str_replace('%', '', $cb) / 100);
						if(strpos($cl, '%') > 0)
							$cl = $this->image_dst_x * (str_replace('%', '', $cl) / 100);
						if(strpos($ct, 'px') > 0)
							$ct = str_replace('px', '', $ct);
						if(strpos($cr, 'px') > 0)
							$cr = str_replace('px', '', $cr);
						if(strpos($cb, 'px') > 0)
							$cb = str_replace('px', '', $cb);
						if(strpos($cl, 'px') > 0)
							$cl = str_replace('px', '', $cl);
						$ct = (int)$ct;
						$cr = (int)$cr;
						$cb = (int)$cb;
						$cl = (int)$cl;
						$this->image_dst_x = $this->image_dst_x + $cl + $cr;
						$this->image_dst_y = $this->image_dst_y + $ct + $cb;
						if(!empty($this->image_border_color))
							sscanf($this->image_border_color, "#%2x%2x%2x", $red, $green, $blue);
						$tmp = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						$background = imagecolorallocatealpha($tmp, $red, $green, $blue, 0);
						imagefill($tmp, 0, 0, $background);
						imagecopy($tmp, $image_dst, $cl, $ct, 0, 0, $this->image_dst_x - $cr - $cl, $this->image_dst_y -
								$cb - $ct);
						imagedestroy($image_dst);
						$image_dst = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						imagecopy($image_dst, $tmp, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y);
						imagedestroy($tmp);
					}
					if(is_numeric($this->image_frame)) {
						if(is_array($this->image_frame_colors)) {
							$vars = $this->image_frame_colors;
							$this->log .= '- '._("add frame").' : '.implode(' ', $this->image_frame_colors).
									'<br />';
						} else {
							$this->log .= '- '._("add frame").' : '.$this->image_frame_colors.'<br />';
							$vars = explode(' ', $this->image_frame_colors);
						}
						$nb = sizeof($vars);
						$this->image_dst_x = $this->image_dst_x + ($nb * 2);
						$this->image_dst_y = $this->image_dst_y + ($nb * 2);
						$tmp = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						imagecopy($tmp, $image_dst, $nb, $nb, 0, 0, $this->image_dst_x - ($nb * 2), $this->image_dst_y -
								($nb * 2));
						for($i = 0; $i < $nb; $i++) {
							sscanf($vars[$i], "#%2x%2x%2x", $red, $green, $blue);
							$c = imagecolorallocate($tmp, $red, $green, $blue);
							if($this->image_frame == 1) {
								imageline($tmp, $i, $i, $this->image_dst_x - $i - 1, $i, $c);
								imageline($tmp, $this->image_dst_x - $i - 1, $this->image_dst_y - $i - 1, $this->image_dst_x -
										$i - 1, $i, $c);
								imageline($tmp, $this->image_dst_x - $i - 1, $this->image_dst_y - $i - 1, $i, $this->image_dst_y -
										$i - 1, $c);
								imageline($tmp, $i, $i, $i, $this->image_dst_y - $i - 1, $c);
							} else {
								imageline($tmp, $i, $i, $this->image_dst_x - $i - 1, $i, $c);
								imageline($tmp, $this->image_dst_x - $nb + $i, $this->image_dst_y - $nb + $i, $this->image_dst_x -
										$nb + $i, $nb - $i, $c);
								imageline($tmp, $this->image_dst_x - $nb + $i, $this->image_dst_y - $nb + $i, $nb -
										$i, $this->image_dst_y - $nb + $i, $c);
								imageline($tmp, $i, $i, $i, $this->image_dst_y - $i - 1, $c);
							}
						}
						imagedestroy($image_dst);
						$image_dst = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
						imagecopy($image_dst, $tmp, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y);
						imagedestroy($tmp);
					}
					if($this->image_bevel > 0) {
						if(empty($this->image_bevel_color1))
							$this->image_bevel_color1 = '#FFFFFF';
						if(empty($this->image_bevel_color2))
							$this->image_bevel_color2 = '#000000';
						sscanf($this->image_bevel_color1, "#%2x%2x%2x", $red1, $green1, $blue1);
						sscanf($this->image_bevel_color2, "#%2x%2x%2x", $red2, $green2, $blue2);
						imagealphablending($image_dst, true);
						for($i = 0; $i < $this->image_bevel; $i++) {
							$alpha = round(($i / $this->image_bevel) * 127);
							$c1 = imagecolorallocatealpha($image_dst, $red1, $green1, $blue1, $alpha);
							$c2 = imagecolorallocatealpha($image_dst, $red2, $green2, $blue2, $alpha);
							imageline($image_dst, $i, $i, $this->image_dst_x - $i - 1, $i, $c1);
							imageline($image_dst, $this->image_dst_x - $i - 1, $this->image_dst_y - $i, $this->image_dst_x -
									$i - 1, $i, $c2);
							imageline($image_dst, $this->image_dst_x - $i - 1, $this->image_dst_y - $i - 1,
									$i, $this->image_dst_y - $i - 1, $c2);
							imageline($image_dst, $i, $i, $i, $this->image_dst_y - $i - 1, $c1);
						}
					}
					if($this->image_watermark != '' && file_exists($this->image_watermark)) {
						$this->log .= '- '._("add watermark").'<br />';
						$this->image_watermark_position = strtolower($this->image_watermark_position);
						$watermark_info = getimagesize($this->image_watermark);
						$watermark_type = (array_key_exists(2, $watermark_info)?$watermark_info[2]:null);
						$watermark_checked = false;
						if($watermark_type == 1) {
							if(!function_exists('imagecreatefromgif')) {
								$this->error = _("No create from GIF support, can't read watermark");
							} else {
								$filter = @imagecreatefromgif($this->image_watermark);
								if(!$filter) {
									$this->error = _("No GIF read support, can't create watermark");
								} else {
									$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("watermark source image is GIF").
											'<br />';
									$watermark_checked = true;
								}
							}
						} else
						if($watermark_type == 2) {
							if(!function_exists('imagecreatefromjpeg')) {
								$this->error = _("No create from JPG support, can't read watermark");
							} else {
								$filter = @imagecreatefromjpeg($this->image_watermark);
								if(!$filter) {
									$this->error = _("No JPG read support, can't create watermark");
								} else {
									$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("watermark source image is JPG").
											'<br />';
									$watermark_checked = true;
								}
							}
						} else
						if($watermark_type == 3) {
							if(!function_exists('imagecreatefrompng')) {
								$this->error = _("No create from PNG support, can't read watermark");
							} else {
								$filter = @imagecreatefrompng($this->image_watermark);
								if(!$filter) {
									$this->error = _("No PNG read support, can't create watermark");
								} else {
									$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("watermark source image is PNG").
											'<br />';
									$watermark_checked = true;
								}
							}
						}
						if($watermark_checked) {
							$watermark_width = imagesx($filter);
							$watermark_height = imagesy($filter);
							$watermark_x = 0;
							$watermark_y = 0;
							if(is_numeric($this->image_watermark_x)) {
								if($this->image_watermark_x < 0) {
									$watermark_x = $this->image_dst_x - $watermark_width + $this->image_watermark_x;
								} else {
									$watermark_x = $this->image_watermark_x;
								}
							} else {
								if(strpos($this->image_watermark_position, 'r') !== false) {
									$watermark_x = $this->image_dst_x - $watermark_width;
								} else
								if(strpos($this->image_watermark_position, 'l') !== false) {
									$watermark_x = 0;
								} else {
									$watermark_x = ($this->image_dst_x - $watermark_width) / 2;
								}
							}
							if(is_numeric($this->image_watermark_y)) {
								if($this->image_watermark_y < 0) {
									$watermark_y = $this->image_dst_y - $watermark_height + $this->image_watermark_y;
								} else {
									$watermark_y = $this->image_watermark_y;
								}
							} else {
								if(strpos($this->image_watermark_position, 'b') !== false) {
									$watermark_y = $this->image_dst_y - $watermark_height;
								} else
								if(strpos($this->image_watermark_position, 't') !== false) {
									$watermark_y = 0;
								} else {
									$watermark_y = ($this->image_dst_y - $watermark_height) / 2;
								}
							}
							imagecopyresampled($image_dst, $filter, $watermark_x, $watermark_y, 0, 0, $watermark_width,
									$watermark_height, $watermark_width, $watermark_height);
						} else {
							$this->error = _("Watermark image is of unknown type");
						}
					}
					if(!empty($this->image_text)) {
						$this->log .= '- '._("add text").'<br />';
						if(!is_numeric($this->image_text_padding))
							$this->image_text_padding = 0;
						if(!is_numeric($this->image_text_padding_x))
							$this->image_text_padding_x = $this->image_text_padding;
						if(!is_numeric($this->image_text_padding_y))
							$this->image_text_padding_y = $this->image_text_padding;
						$this->image_text_position = strtolower($this->image_text_position);
						$this->image_text_direction = strtolower($this->image_text_direction);
						if($this->image_text_direction == 'v') {
							$text_height = (ImageFontWidth($this->image_text_font) * strlen($this->image_text)) + (2 *
											$this->image_text_padding_y);
							$text_width = ImageFontHeight($this->image_text_font) + (2 * $this->image_text_padding_x);
						} else {
							$text_width = (ImageFontWidth($this->image_text_font) * strlen($this->image_text)) + (2 *
											$this->image_text_padding_x);
							$text_height = ImageFontHeight($this->image_text_font) + (2 * $this->image_text_padding_y);
						}
						$text_x = 0;
						$text_y = 0;
						if(is_numeric($this->image_text_x)) {
							if($this->image_text_x < 0) {
								$text_x = $this->image_dst_x - $text_width + $this->image_text_x;
							} else {
								$text_x = $this->image_text_x;
							}
						} else {
							if(strpos($this->image_text_position, 'r') !== false) {
								$text_x = $this->image_dst_x - $text_width;
							} else
							if(strpos($this->image_text_position, 'l') !== false) {
								$text_x = 0;
							} else {
								$text_x = ($this->image_dst_x - $text_width) / 2;
							}
						}
						if(is_numeric($this->image_text_y)) {
							if($this->image_text_y < 0) {
								$text_y = $this->image_dst_y - $text_height + $this->image_text_y;
							} else {
								$text_y = $this->image_text_y;
							}
						} else {
							if(strpos($this->image_text_position, 'b') !== false) {
								$text_y = $this->image_dst_y - $text_height;
							} else
							if(strpos($this->image_text_position, 't') !== false) {
								$text_y = 0;
							} else {
								$text_y = ($this->image_dst_y - $text_height) / 2;
							}
						}
						if(!empty($this->image_text_background)) {
							sscanf($this->image_text_background, "#%2x%2x%2x", $red, $green, $blue);
							if($gd_version >= 2 && (is_numeric($this->image_text_background_percent)) && $this->image_text_background_percent >=
									0 && $this->image_text_background_percent <= 100) {
								$filter = imagecreatetruecolor($text_width, $text_height);
								$background_color = imagecolorallocate($filter, $red, $green, $blue);
								imagefilledrectangle($filter, 0, 0, $text_width, $text_height, $background_color);
								imagecopymerge($image_dst, $filter, $text_x, $text_y, 0, 0, $text_width, $text_height,
										$this->image_text_background_percent);
								imagedestroy($filter);
							} else {
								$background_color = imageColorAllocate($image_dst, $red, $green, $blue);
								imagefilledrectangle($image_dst, $text_x, $text_y, $text_x + $text_width, $text_y +
										$text_height, $background_color);
							}
						}
						$text_x += $this->image_text_padding_x;
						$text_y += $this->image_text_padding_y;
						sscanf($this->image_text_color, "#%2x%2x%2x", $red, $green, $blue);
						if($gd_version >= 2 && (is_numeric($this->image_text_percent)) && $this->image_text_percent >=
								0 && $this->image_text_percent <= 100) {
							$t_width = $text_width - (2 * $this->image_text_padding_x);
							$t_height = $text_height - (2 * $this->image_text_padding_y);
							if($t_width < 0)
								$t_width = 0;
							if($t_height < 0)
								$t_height = 0;
							$filter = imagecreatetruecolor($t_width, $t_height);
							$color = imagecolorallocate($filter, 0, 0, ($blue == 0?255:0));
							imagefill($filter, 0, 0, $color);
							$text_color = imagecolorallocate($filter, $red, $green, $blue);
							imagecolortransparent($filter, $color);
							if($this->image_text_direction == 'v') {
								imagestringup($filter, $this->image_text_font, 0, $text_height - (2 * $this->image_text_padding_y),
										$this->image_text, $text_color);
							} else {
								imagestring($filter, $this->image_text_font, 0, 0, $this->image_text, $text_color);
							}
							imagecopymerge($image_dst, $filter, $text_x, $text_y, 0, 0, $t_width, $t_height,
									$this->image_text_percent);
							imagedestroy($filter);
						} else {
							$text_color = imageColorAllocate($image_dst, $red, $green, $blue);
							if($this->image_text_direction == 'v') {
								imagestringup($image_dst, $this->image_text_font, $text_x, $text_y + $text_height -
										(2 * $this->image_text_padding_y), $this->image_text, $text_color);
							} else {
								imagestring($image_dst, $this->image_text_font, $text_x, $text_y, $this->image_text,
										$text_color);
							}
						}
					}
					if(is_numeric($this->jpeg_size) && $this->jpeg_size > 0 && ($this->image_convert ==
									'jpeg' || $this->image_convert == 'jpg')) {
						$this->log .= '- '._("JPEG desired file size").' : '.$this->jpeg_size.'<br />';
						ob_start();
						imagejpeg($image_dst, '', 75);
						$buffer = ob_get_contents();
						ob_end_clean();
						$size75 = strlen($buffer);
						ob_start();
						imagejpeg($image_dst, '', 50);
						$buffer = ob_get_contents();
						ob_end_clean();
						$size50 = strlen($buffer);
						ob_start();
						imagejpeg($image_dst, '', 25);
						$buffer = ob_get_contents();
						ob_end_clean();
						$size25 = strlen($buffer);
						$mgrad1 = 25 / ($size50 - $size25);
						$mgrad2 = 25 / ($size75 - $size50);
						$mgrad3 = 50 / ($size75 - $size25);
						$mgrad = ($mgrad1 + $mgrad2 + $mgrad3) / 3;
						$q_factor = round($mgrad * ($this->jpeg_size - $size50) + 50);
						if($q_factor < 1) {
							$this->jpeg_quality = 1;
						} elseif($q_factor > 100) {
							$this->jpeg_quality = 100;
						} else {
							$this->jpeg_quality = $q_factor;
						}
						$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("JPEG quality factor set to").' '.$this->jpeg_quality.'<br />';
					}
					$this->log .= '- '._("converting..").'<br />';
					switch($this->image_convert) {
						case 'jpeg':
						case 'jpg':
							$result = @imagejpeg($image_dst, $this->file_dst_pathname, $this->jpeg_quality);
							if(!$result) {
								$this->processed = false;
								$this->error = _("No JPEG create support");
							} else {
								$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("JPEG image created").'<br />';
							}
							break;
						case 'png':
							$result = @imagepng($image_dst, $this->file_dst_pathname);
							if(!$result) {
								$this->processed = false;
								$this->error = _("No PNG create support");
							} else {
								$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("PNG image created").'<br />';
							}
							break;
						case 'gif':
							$result = @imagegif($image_dst, $this->file_dst_pathname);
							if(!$result) {
								$this->processed = false;
								$this->error = _("No GIF create support");
							} else {
								$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("GIF image created").'<br />';
							}
							break;
						default:
							$this->processed = false;
							$this->error = _("No convertion type defined");
					}
					if($this->processed) {
						if(is_resource($image_src))
							imagedestroy($image_src);
						if(is_resource($image_dst))
							imagedestroy($image_dst);
						$this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;'._("image objects destroyed").'<br />';
					}
				}
			} else {
				$this->log .= '- '._("no image processing wanted").'<br />';
				if(!$this->no_upload_check) {
					$result = is_uploaded_file($this->file_src_pathname);
				} else {
					$result = true;
				}
				if($result) {
					$result = file_exists($this->file_src_pathname);
					if($result) {
						$result = copy($this->file_src_pathname, $this->file_dst_pathname);
						if(!$result) {
							$this->processed = false;
							$this->error = _("Error copying file on the server. Copy failed");
						}
					} else {
						$this->processed = false;
						$this->error = _("Error copying file on the server. Missing source file");
					}
				} else {
					$this->processed = false;
					$this->error = _("Error copying file on the server. Incorrect source file");
				}
			}
		}
		if($this->processed) {
			$this->log .= '- <b>'._("process OK").'</b><br />';
		}
		$this->init();
	}
	function clean() {
		@unlink($this->file_src_pathname);
	}
}
if(!function_exists("_")) {
	function _($str) {
		return $str;
	}
}