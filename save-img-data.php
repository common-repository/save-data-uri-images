<?php
/*
Plugin Name: Save Data URI scheme
Plugin URI: http://www.networks.by/
Description: This plugin converts Data:URL image in PNG files format. Automation of copy paste images.
Author: BitMan <zhidrons@tut.by>
Contributor: BitMan <zhidrons@tut.by>
Author URI: http://www.networks.by/
Version: 0.1
*/ 
 
ini_set("pcre.backtrack_limit",10000000);	
function convert_img_data($content)
	{
		
		if(preg_match_all("/<img[^>]+>/is", $content, $imgfound))
		{
		$replacesearch=Array();
		$replaceimg=Array();
		
			for ($i=0; $i < count($imgfound[0]); $i++)
				{
					if(preg_match("#src[\s]*=[\s]*[\\\\]*[\"'](data.*?)[\\\\\]*[\"']#is", $imgfound[0][$i], $imgfoundsrc))
					{
						
						$replacesearch[]=$imgfoundsrc[1];
						
						$newimgfoundsrc=explode(";",$imgfoundsrc[1]);
						$typenewimg=$newimgfoundsrc[0]; ///тип файла
						$datanewimgfoundsrc=explode(",",$newimgfoundsrc[1]);
						$imgrow=base64_decode($datanewimgfoundsrc[1]); ///содержимое

						
						$filename=creat_file_name($imgfound[0][$i]);
						$urlimg = save_my_file($filename,$imgrow);
						
						$replaceimg[]=$urlimg;

					}
					
					
				}
		}
		

	$content = str_replace($replacesearch, $replaceimg, $content);

	return $content;
	}	
	
function save_my_file($filename,$filebody)
	{
		$uploads = wp_upload_dir($time = null);
		$handle = fopen ($uploads['path'].'/'.$filename, 'w+'); fwrite($handle, $filebody); fclose($handle);
		$stat = stat( dirname( $new_file )); 	$perms = $stat['mode'] & 0000666; @ chmod( $new_file, $perms );
		$url = $uploads['url'] .'/'. $filename;

		return $url;
	}	

function creat_file_name($rowfile)
{
	$uploads = wp_upload_dir($time = null); 

	if(preg_match("#src[\s]*=[\s]*[\\\\]*[\"'](.*?)[\\\\\]*[\"']#is", $rowfile, $imgfoundsrc))
		{
		
			$newimgfoundsrc=explode(";",$imgfoundsrc[1]);
			$typenewimg=$newimgfoundsrc[0]; ///тип файла
			$datanewimgfoundsrc=explode(",",$newimgfoundsrc[1]);
			$imgrow=base64_decode($datanewimgfoundsrc[1]); ///содержимое
			$mimetype = getImageMimeType($imgrow);
			
			if(preg_match("#title[\s]*=[\s]*[\\\\]*[\"'](.*?)[\\\\\]*[\"']#is", $rowfile, $imgtitle))
				{
					$imgtitle=strip_tags(trim($imgtitle[1]));
				}
			else
				{
					$imgtitle=rand(111,999);
				}

			$imgtitle = str_replace('?','-', $imgtitle);
			$imgtitle = str_replace('&','-', $imgtitle);
			$imgtitle = $imgtitle.'.'.$mimetype;
			
			$filename = wp_unique_filename( $uploads['path'], $imgtitle);
			return $filename;	
		}	
	
}	
	
function getImageMimeType($imagedata){  $imagemimetypes = array(     "jpeg" => "FFD8",     "png" => "89504E470D0A1A0A",     "gif" => "474946",    "bmp" => "424D",     "tiff" => "4949",    "tiff" => "4D4D"  );   foreach ($imagemimetypes as $mime => $hexbytes)  {    $bytes = getBytesFromHexString($hexbytes);  if (substr($imagedata, 0, strlen($bytes)) == $bytes){   return $mime;}  }   return NULL;}
function getBytesFromHexString($hexdata){  for($count = 0; $count < strlen($hexdata); $count+=2){ $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));} return implode($bytes); }
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////
	

add_filter('content_save_pre','convert_img_data'); 
?>