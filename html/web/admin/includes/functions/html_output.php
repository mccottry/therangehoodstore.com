<?php

/*

  $Id: html_output.php,v 1.29 2003/06/25 20:32:44 hpdl Exp $



  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2003 osCommerce



  Released under the GNU General Public License

*/



////

require("fckeditor/fckeditor.php");

// The HTML href link wrapper function

  function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL') {

    if ($page == '') {

      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine the page link!<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');

    }

    if ($connection == 'NONSSL') {

      $link = HTTP_SERVER . DIR_WS_ADMIN;

    } elseif ($connection == 'SSL') {

      if (ENABLE_SSL == 'true') {

        $link = HTTPS_SERVER . DIR_WS_ADMIN;

      } else {

        $link = HTTP_SERVER . DIR_WS_ADMIN;

      }

    } else {

      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');

    }

    if ($parameters == '') {

      $link = $link . $page . '?' . SID;

    } else {

      $link = $link . $page . '?' . $parameters . '&' . SID;

    }



    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);



    return $link;

  }



  function tep_catalog_href_link($page = '', $parameters = '', $connection = 'NONSSL') {

    if ($connection == 'NONSSL') {

      $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;

    } elseif ($connection == 'SSL') {

      if (ENABLE_SSL_CATALOG == 'true') {

        $link = HTTPS_CATALOG_SERVER . DIR_WS_CATALOG;

      } else {

        $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;

      }

    } else {

      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');

    }

    if ($parameters == '') {

      $link .= $page;

    } else {

      $link .= $page . '?' . $parameters;

    }



    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);



    return $link;

  }



////

// The HTML image wrapper function

  function tep_default_image($src, $alt = '', $width = '', $height = '', $parameters = '') {

    $image = '<img src="' . tep_output_string($src) . '" border="0" alt="' . tep_output_string($alt) . '"';



    if (tep_not_null($alt)) {

      $image .= ' title=" ' . tep_output_string($alt) . ' "';

    }



    if (tep_not_null($width) && tep_not_null($height)) {

      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';

    }



    if (tep_not_null($parameters)) $image .= ' ' . $parameters;



    $image .= '>';



    return $image;

  }



////

// The HTML form submit button wrapper function

// Outputs a button in the selected language

  function tep_image_submit($image, $alt = '', $parameters = '') {

    global $language;



    $image_submit = '<input type="image" src="' . tep_output_string(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image) . '" border="0" alt="' . tep_output_string($alt) . '"';



    if (tep_not_null($alt)) $image_submit .= ' title=" ' . tep_output_string($alt) . ' "';



    if (tep_not_null($parameters)) $image_submit .= ' ' . $parameters;



    $image_submit .= '>';



    return $image_submit;

  }



////

// Draw a 1 pixel black line

  function tep_black_line() {

    return tep_image(DIR_WS_IMAGES . 'pixel_black.gif', '', '100%', '1');

  }



////

// Output a separator either through whitespace, or with an image

  function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {

    return tep_image(DIR_WS_IMAGES . $image, '', $width, $height);

  }



////

// Output a function button in the selected language

  function tep_image_button($image, $alt = '', $params = '') {

    global $language;



    return tep_image(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, $alt, '', '', $params);

  }



////

// javascript to dynamically update the states/provinces list when the country is changed

// TABLES: zones

  function tep_js_zone_list($country, $form, $field) {

    $countries_query = tep_db_query("select distinct zone_country_id from " . TABLE_ZONES . " order by zone_country_id");

    $num_country = 1;

    $output_string = '';

    while ($countries = tep_db_fetch_array($countries_query)) {

      if ($num_country == 1) {

        $output_string .= '  if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";

      } else {

        $output_string .= '  } else if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";

      }



      $states_query = tep_db_query("select zone_name, zone_id from " . TABLE_ZONES . " where zone_country_id = '" . $countries['zone_country_id'] . "' order by zone_name");



      $num_state = 1;

      while ($states = tep_db_fetch_array($states_query)) {

        if ($num_state == '1') $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . PLEASE_SELECT . '", "");' . "\n";

        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";

        $num_state++;

      }

      $num_country++;

    }

    $output_string .= '  } else {' . "\n" .

                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . TYPE_BELOW . '", "");' . "\n" .

                      '  }' . "\n";



    return $output_string;

  }



////

// Output a form

  function tep_draw_form($name, $action, $parameters = '', $method = 'post', $params = '') {

    $form = '<form name="' . tep_output_string($name) . '" action="';

    if (tep_not_null($parameters)) {

      $form .= tep_href_link($action, $parameters);

    } else {

      $form .= tep_href_link($action);

    }

    $form .= '" method="' . tep_output_string($method) . '"';

    if (tep_not_null($params)) {

      $form .= ' ' . $params;

    }

    $form .= '>';



    return $form;

  }



////

// Output a form input field

  function tep_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {

    $field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';



    if (isset($GLOBALS[$name]) && ($reinsert_value == true) && is_string($GLOBALS[$name])) {

      $field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';

    } elseif (tep_not_null($value)) {

      $field .= ' value="' . tep_output_string($value) . '"';

    }



    if (tep_not_null($parameters)) $field .= ' ' . $parameters;



    $field .= ' size="50"  >';



    if ($required == true) $field .= TEXT_FIELD_REQUIRED;



    return $field;

  }



////

// Output a form password field

  function tep_draw_password_field($name, $value = '', $required = false) {

    $field = tep_draw_input_field($name, $value, 'maxlength="40"', $required, 'password', false);



    return $field;

  }



////

// Output a form filefield

  function tep_draw_file_field($name, $required = false) {

    $field = tep_draw_input_field($name, '', '', $required, 'file');



    return $field;

  }



////

// Output a selection field - alias function for tep_draw_checkbox_field() and tep_draw_radio_field()

  function tep_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '') {

    $selection = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';



    if (tep_not_null($value)) $selection .= ' value="' . tep_output_string($value) . '"';



    if ( ($checked == true) || (isset($GLOBALS[$name]) && is_string($GLOBALS[$name]) && ($GLOBALS[$name] == 'on')) || (isset($value) && isset($GLOBALS[$name]) && (stripslashes($GLOBALS[$name]) == $value)) || (tep_not_null($value) && tep_not_null($compare) && ($value == $compare)) ) {

      $selection .= ' CHECKED';

    }



    $selection .= '>';



    return $selection;

  }



////

// Output a form checkbox field

  function tep_draw_checkbox_field($name, $value = '', $checked = false, $compare = '') {

    return tep_draw_selection_field($name, 'checkbox', $value, $checked, $compare);

  }



////

// Output a form radio field

  function tep_draw_radio_field($name, $value = '', $checked = false, $compare = '') {

    return tep_draw_selection_field($name, 'radio', $value, $checked, $compare);

  }



////

// Output a form textarea field

  function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {

    $field = '<textarea name="' . tep_output_string($name) . '" wrap="' . tep_output_string($wrap) . '" cols="' . tep_output_string($width) . '" rows="' . tep_output_string($height) . '"';



    if (tep_not_null($parameters)) $field .= ' ' . $parameters;



    $field .= '>';



    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {

      $field .= tep_output_string_protected(stripslashes($GLOBALS[$name]));

    } elseif (tep_not_null($text)) {

      $field .= tep_output_string_protected($text);

    }



    $field .= '</textarea>';



    return $field;

  }



///FCKEDITOR function



 function tep_draw_fckeditor($name, $width, $height, $text) {

 

$page_cuenta_separa = array();

$url_cuenta_separa = $_SERVER['SERVER_NAME']; 

$separar = explode("/",$_SERVER['REQUEST_URI']);

$cuenta_separa = count($separar);

for ( $i_cuenta_separa = 0; $i_cuenta_separa < $cuenta_separa - 1 ;$i_cuenta_separa ++){

    $page_cuenta_separa[$i_cuenta_separa] = $separar[$i_cuenta_separa];

}

$comma_separated = implode("/", $page_cuenta_separa);



$url_deseado = "http://".$url_cuenta_separa.$comma_separated;





	$oFCKeditor = new FCKeditor($name);

	$oFCKeditor -> Width  = $width;

	$oFCKeditor -> Height = $height;

	$oFCKeditor -> BasePath	= $url_deseado.'/fckeditor/';

	$oFCKeditor -> ToolbarSet	= 'Basic';	

	$oFCKeditor -> Value = $text;



    $field = $oFCKeditor->Create($name);



    return $field;

  }





////

// Output a form hidden field

  function tep_draw_hidden_field($name, $value = '', $parameters = '') {

    $field = '<input type="hidden" name="' . tep_output_string($name) . '"';



    if (tep_not_null($value)) {

      $field .= ' value="' . tep_output_string($value) . '"';

    } elseif (isset($GLOBALS[$name]) && is_string($GLOBALS[$name])) {

      $field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';

    }



    if (tep_not_null($parameters)) $field .= ' ' . $parameters;



    $field .= '>';



    return $field;

  }



////

// Output a form pull down menu

  function tep_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {

    $field = '<select name="' . tep_output_string($name) . '"';



    if (tep_not_null($parameters)) $field .= ' ' . $parameters;



    $field .= '>';



    if (empty($default) && isset($GLOBALS[$name])) $default = stripslashes($GLOBALS[$name]);



    for ($i=0, $n=sizeof($values); $i<$n; $i++) {

      $field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';

      if ($default == $values[$i]['id']) {

        $field .= ' SELECTED';

      }



      $field .= '>' . tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';

    }

    $field .= '</select>';



    if ($required == true) $field .= TEXT_FIELD_REQUIRED;



    return $field;

  }

////

// Image Resample

  function tep_image($src, $alt = '', $width = '', $height = '', $parameters = '')

    {

	

	$dividir = explode(".",$src); 

   // if no width or height specified or file not found use default function

   if ((!$width) || (!$height) || (!is_file(DIR_FS_DOCUMENT_ROOT . $src))) 

      return tep_default_image($src, $alt, $width, $height, $parameters);



   // Name for the resampled image (always JPEG for decent results in size and quality

   $newName = eregi_replace( '\.([a-z]{3,4})', "-{$width}x{$height}.".$dividir[1], $src );



   // if resampled image exists, no need to create. Use existing one.

   // Check to determine whether thumbnail is older than main image. If it is, the main image has been updated. Generate a new thumbnail.

   if( is_file( DIR_FS_DOCUMENT_ROOT . $newName ) && filemtime( DIR_FS_DOCUMENT_ROOT . $src ) < filemtime ( DIR_FS_DOCUMENT_ROOT . $newName) )

      {

      $src = $newName;

      return tep_default_image($src, $alt, $width, $height, $parameters);

      }



   // get the size of the image.  if width or height=0, image is broken. No processing.

   $size = GetImageSize(DIR_FS_DOCUMENT_ROOT . $src);

   if (!$size[0] || !$size[1])

      return tep_default_image($src, $alt, $width, $height, $parameters);



   // Calculate Scaling Factor and x,y pos for centering the thumbnail

   // If scale = 1, image does not need to be resized.

   $scale = min($width/$size[0], $height/$size[1]);

   if ( $scale == 1 )

      return tep_default_image($src, $alt, $width, $height, $parameters);



   $newwidth = (int)($size[0]*$scale);

   $newheight = (int)($size[1]*$scale);

   $xpos = (int)(($width - $newwidth)/2);

   $ypos = (int)(($height - $newheight)/2);



   $destImg = imagecreatetruecolor($width, $height);

   imagealphablending($destImg, false);

   

   if(( $dividir[1] = "JPG") || ( $dividir[1] = "jpg") || ( $dividir[1] = "JPEG") || ( $dividir[1] = "jpeg")) {

   

    $colorr = imagecolorallocatealpha($destImg, 255, 255, 255, 127);

	 

   } else {

   

   $colorr = imagecolorallocatealpha($destImg, 0, 0, 0, 127);

   

   }

   

   imagefill($destImg, 0, 0, $colorr);

   imagesavealpha($destImg, true);



   // Check image format. Only process JPG or PNG. GIF NOT supported by PHP.

   // The results with gifs were no good anyway

   switch ( $size[2] )

      {

      case 2: // JPG

         $sourceImg = ImageCreateFromJPEG (DIR_FS_DOCUMENT_ROOT . $src);

         if (function_exists('ImageCopyResampled'))

            ImageCopyResampled($destImg, $sourceImg, $xpos, $ypos, 0, 0, $newwidth, $newheight, $size[0], $size[1]);

         else

            ImageCopyResized($destImg, $sourceImg, $xpos, $ypos, 0, 0, $newwidth, $newheight, $size[0], $size[1]);

         imagejpeg($destImg, DIR_FS_DOCUMENT_ROOT . $newName, 100);

         $src = $newName;           // Use the resampled image

         $width = $height = "";     // and it's own properties

         break;

      case 3: // PNG

        $sourceImg = ImageCreateFromPNG (DIR_FS_DOCUMENT_ROOT . $src);

        if (function_exists('ImageCopyResampled'))

           ImageCopyResampled($destImg, $sourceImg, $xpos, $ypos, 0, 0, $newwidth, $newheight, $size[0], $size[1]);

        else

           ImageCopyResized($destImg, $sourceImg, $xpos, $ypos, 0, 0, $newwidth, $newheight, $size[0], $size[1]);

        imagepng($destImg, DIR_FS_DOCUMENT_ROOT . $newName, 100);

        $src = $newName;

        $width = $height = "";

        break;

     }

     return tep_default_image($src, $alt, $width, $height, $parameters);

   }

   

   

 

 

////

// Image Resample2

  function tep_image2($src, $alt = '', $width = '', $height = '', $parameters = '')

    {



	$dividir = explode(".",$src); 

   // if no width or height specified or file not found use default function

   if ((!$width) || (!$height) || (!is_file(DIR_FS_DOCUMENT_ROOT . $src))) 

      return tep_default_image('../' .$src, $alt, $width, $height, $parameters);



   // Name for the resampled image (always JPEG for decent results in size and quality

   $newName = eregi_replace( '\.([a-z]{3,4})', "-{$width}x{$height}.".$dividir[1], $src );



   // if resampled image exists, no need to create. Use existing one.

   // Check to determine whether thumbnail is older than main image. If it is, the main image has been updated. Generate a new thumbnail.

   if( is_file( DIR_FS_DOCUMENT_ROOT . $newName ) && filemtime( DIR_FS_DOCUMENT_ROOT . $src ) < filemtime ( DIR_FS_DOCUMENT_ROOT . $newName) )

      {

      $src = $newName;

      return tep_default_image('../' .$src, $alt, $width, $height, $parameters);

      }



   // get the size of the image.  if width or height=0, image is broken. No processing.

   $size = GetImageSize(DIR_FS_DOCUMENT_ROOT . $src);

   if (!$size[0] || !$size[1])

      return tep_default_image('../' .$src, $alt, $width, $height, $parameters);



   // Calculate Scaling Factor and x,y pos for centering the thumbnail

   // If scale = 1, image does not need to be resized.

   $scale = min($width/$size[0], $height/$size[1]);

   if ( $scale == 1 )

      return tep_default_image('../' .$src, $alt, $width, $height, $parameters);



   $newwidth = (int)($size[0]*$scale);

   $newheight = (int)($size[1]*$scale);

   $xpos = (int)(($width - $newwidth)/2);

   $ypos = (int)(($height - $newheight)/2);

   

   





   $destImg = imagecreatetruecolor($width, $height);

   imagealphablending($destImg, false);

   

   if(( $dividir[1] = "JPG") || ( $dividir[1] = "jpg") || ( $dividir[1] = "JPEG") || ( $dividir[1] = "jpeg")) {

   

    $colorr = imagecolorallocatealpha($destImg, 255, 255, 255, 127);

	 

   } else {

   

   $colorr = imagecolorallocatealpha($destImg, 0, 0, 0, 127);

   

   }

   

   imagefill($destImg, 0, 0, $colorr);

   imagesavealpha($destImg, true);

   

   

   

   // Check image format. Only process JPG or PNG. GIF NOT supported by PHP.

   // The results with gifs were no good anyway

   switch ( $size[2] )

      {

      case 2: // JPG

         $sourceImg = ImageCreateFromJPEG (DIR_FS_DOCUMENT_ROOT . $src);

         if (function_exists('ImageCopyResampled'))

            ImageCopyResampled($destImg, $sourceImg, $xpos, $ypos, 0, 0, $newwidth, $newheight, $size[0], $size[1]);

         else

            ImageCopyResized($destImg, $sourceImg, $xpos, $ypos, 0, 0, $newwidth, $newheight, $size[0], $size[1]);

         imagejpeg($destImg, DIR_FS_DOCUMENT_ROOT . $newName, 100);

         $src = $newName;           // Use the resampled image

         $width = $height = "";     // and it's own properties

         break;

      case 3: // PNG

        $sourceImg = ImageCreateFromPNG (DIR_FS_DOCUMENT_ROOT . $src);

        if (function_exists('ImageCopyResampled'))

           ImageCopyResampled($destImg, $sourceImg, $xpos, $ypos, 0, 0, $newwidth, $newheight, $size[0], $size[1]);

        else

           ImageCopyResized($destImg, $sourceImg, $xpos, $ypos, 0, 0, $newwidth, $newheight, $size[0], $size[1]);

        imagepng($destImg, DIR_FS_DOCUMENT_ROOT . $newName, 100);

        $src = $newName;

        $width = $height = "";

        break;

     }

     return tep_default_image('../' .$src, $alt, $width, $height, $parameters);

   }

?>
