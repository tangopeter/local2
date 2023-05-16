<?php

/**
 * Selective's Image Type Library Functions of the Plugin
 *
 * This file contains functions related to image type checking using Selective's
 * Image Type library.
 *
 * @link /lib/wfu_imagetype.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 4.15.0
 */

require_once ABSWPFILEUPLOAD_DIR . WFU_AUTOLOADER_PHP70100;
use Selective\ImageType\ImageTypeDetector;
use Selective\ImageType\MimeType;
use Selective\ImageType\Provider\CompoundProvider;
use Selective\ImageType\Provider\RasterProvider;
use Selective\ImageType\Provider\HdrProvider;
use Selective\ImageType\Provider\RawProvider;
use Selective\ImageType\Provider\VectorProvider;

/**
 * Check Image Type Extension
 *
 * This function checks whether the extension of a file is within the extensions
 * supported by Image Type library.
 *
 * @since 4.15.0
 *
 * @param string $filename The filename of the file to check.
 *
 * @return bool True if the extension is supported by Image Type library, false
 *         otherwise.
 */
function wfu_file_extension_imagetypelib($filename) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	$filename = strtolower($filename);
	return ( 
		substr($filename, -4) == ".3fr" ||
		substr($filename, -3) == ".ai" ||
		substr($filename, -4) == ".ani" ||
		substr($filename, -4) == ".bmp" ||
		substr($filename, -4) == ".cin" ||
		substr($filename, -4) == ".cr2" ||
		substr($filename, -4) == ".cr3" ||
		substr($filename, -4) == ".cur" ||
		substr($filename, -4) == ".dcm" ||
		substr($filename, -4) == ".dng" ||
		substr($filename, -4) == ".dpx" ||
		substr($filename, -4) == ".emf" ||
		substr($filename, -4) == ".exr" ||
		substr($filename, -4) == ".gif" ||
		substr($filename, -4) == ".hdr" ||
		substr($filename, -5) == ".heic" ||
		substr($filename, -4) == ".ico" ||
		substr($filename, -4) == ".iiq" ||
		substr($filename, -4) == ".jp2" ||
		substr($filename, -4) == ".jpg" ||
		substr($filename, -5) == ".jpeg" ||
		substr($filename, -4) == ".jpm" ||
		substr($filename, -4) == ".mng" ||
		substr($filename, -4) == ".orf" ||
		substr($filename, -4) == ".pbm" ||
		substr($filename, -4) == ".pdn" ||
		substr($filename, -4) == ".pef" ||
		substr($filename, -4) == ".pfm" ||
		substr($filename, -4) == ".pgm" ||
		substr($filename, -4) == ".png" ||
		substr($filename, -4) == ".ppm" ||
		substr($filename, -4) == ".psb" ||
		substr($filename, -4) == ".psd" ||
		substr($filename, -4) == ".rw2" ||
		substr($filename, -4) == ".svg" ||
		substr($filename, -4) == ".swf" ||
		substr($filename, -4) == ".tif" ||
		substr($filename, -5) == ".webp" ||
		substr($filename, -4) == ".wmf" ||
		substr($filename, -4) == ".xcf"
	);
}

/**
 * Check Image Validity
 *
 * This function checks whether a file is a valid image.
 *
 * @since 4.15.0
 *
 * @param string $filepath The path of the file to check.
 *
 * @return bool True if the file is a valid image, false otherwise.
 */
function wfu_filecheck_imagetypelib($filepath) {
	$a = func_get_args(); $a = WFU_FUNCTION_HOOK(__FUNCTION__, $a, $out); if (isset($out['vars'])) foreach($out['vars'] as $p => $v) $$p = $v; switch($a) { case 'R': return $out['output']; break; case 'D': die($out['output']); }
	//Image Type works for PHP version 7.1 and higher, so return true if the
	//current PHP version is lower
	if ( version_compare(PHP_VERSION, '7.1.0', '<') ) return true;
	$detector = new ImageTypeDetector();
	$detector->addProvider(new CompoundProvider());
	$detector->addProvider(new VectorProvider());
	$detector->addProvider(new HdrProvider());
	$detector->addProvider(new RawProvider());
	$detector->addProvider(new RasterProvider());
	$file = new SplFileObject($filepath);
	$ret = false;
	try {
		$actual = $detector->getImageTypeFromFile($file);
		$ret = true;
	}
	catch(Exception $ex) {
		wfu_debug_log_obj($ex);
	}

	return $ret;
}