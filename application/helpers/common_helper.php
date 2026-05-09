<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**

 * CodeIgniter

 *

 * An open source application development framework for PHP 5.1.6 or newer

 *

 * @package		CodeIgniter

 * @author		ExpressionEngine Dev Team

 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.

 * @license		http://codeigniter.com/user_guide/license.html

 * @link		http://codeigniter.com

 * @since		Version 1.0

 * @filesource

 */



if (! function_exists('get_settings')) {

  function get_settings($key = '')
  {
    $CI  = &get_instance();



    $CI->db->select($key);

    $sql = $CI->db->get('application_settings');



    if ($sql->num_rows() == 1) {
      return $sql->result()[0]->$key;
    } else {
      return '';
    }
  }
}



if (! function_exists('has_perm')) {


  function has_perm($userid, $module = '', $submodule = '', $field = '')
  {
    $CI  = &get_instance();
    if (!empty($module)) {
      $CI->db->where('m_userperm_module', $module);
      $CI->db->where('m_userperm_list', 1);
    }
    if (!empty($submodule)) {
      $CI->db->where('m_userperm_submodule', $submodule);
    }
    if (!empty($field)) {
      if ($field == 'Edit') {
        $CI->db->where('m_userperm_edit', 1);
      }
      if ($field == 'Delete') {
        $CI->db->where('m_userperm_delete', 1);
      }
      if ($field == 'Add') {
        $CI->db->where('m_userperm_add', 1);
      }
      if ($field == 'Filter') {
        $CI->db->where('m_userperm_filter', 1);
      }
      if ($field == 'Export') {
        $CI->db->where('m_userperm_export', 1);
      }
    }
    return $CI->db->select('m_userperm_id')->where('m_userperm_userId', $userid)->get('master_user_permission_tbl')->row();
  }
}


if (! function_exists('currency')) {

  function currency($price = "")
  {

    $CI  = &get_instance();

    $CI->load->database();

    if ($price != "") {

      $CI->db->where('key', 'system_currency');

      $currency_code = $CI->db->get('settings')->row()->value;



      $CI->db->where('code', $currency_code);

      $symbol = $CI->db->get('currency')->row()->symbol;



      $CI->db->where('key', 'currency_position');

      $position = $CI->db->get('settings')->row()->value;



      if ($position == 'right') {

        return $price . ' ' . $symbol;
      } elseif ($position == 'right-space') {

        return $price . ' ' . $symbol;
      } elseif ($position == 'left') {

        return $symbol . ' ' . $price;
      } elseif ($position == 'left-space') {

        return $symbol . ' ' . $price;
      }
    }
  }
}
if (! function_exists('IND_money_format')) {

  function IND_money_format($num)
  {
    if ($num === null || $num === '' || !is_numeric($num)) {
      return '0';
    }

    $num = (float)$num;
    $integer = (int)$num;
    $decimal = rtrim(sprintf('%02d', round(($num - $integer) * 100)), '0');

    $str = (string)$integer;
    if (strlen($str) > 3) {
      $lastThree  = substr($str, -3);
      $restUnits  = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($str, 0, -3));
      $str        = $restUnits . ',' . $lastThree;
    }

    return $str . ($decimal !== '' ? '.' . $decimal : '');
  }
}



if (! function_exists('currency_code_and_symbol')) {

  function currency_code_and_symbol($type = "")
  {

    $CI  = &get_instance();

    $CI->load->database();

    $CI->db->where('key', 'system_currency');

    $currency_code = $CI->db->get('settings')->row()->value;



    $CI->db->where('code', $currency_code);

    $symbol = $CI->db->get('currency')->row()->symbol;

    if ($type == "") {

      return $symbol;
    } else {

      return $currency_code;
    }
  }
}



if (! function_exists('get_frontend_settings')) {

  function get_frontend_settings($key = '')
  {

    $CI  = &get_instance();

    $CI->load->database();



    $CI->db->where('key', $key);

    $result = $CI->db->get('frontend_settings')->row()->value;

    return $result;
  }
}



if (! function_exists('slugify')) {

  function slugify($text)
  {

    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    $text = trim($text, '-');

    $text = strtolower($text);

    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text))

      return 'n-a';

    return $text;
  }
}



if (! function_exists('get_video_extension')) {

  // Checks if a video is youtube, vimeo or any other

  function get_video_extension($url)
  {

    if (strpos($url, '.mp4') > 0) {

      return 'mp4';
    } elseif (strpos($url, '.webm') > 0) {

      return 'webm';
    } else {

      return 'unknown';
    }
  }
}



if (! function_exists('ellipsis')) {

  // Checks if a video is youtube, vimeo or any other

  function ellipsis($long_string, $max_character = 30)
  {

    $short_string = strlen($long_string) > $max_character ? substr($long_string, 0, $max_character) . "..." : $long_string;

    return $short_string;
  }
}

if (! function_exists('get_sms_balance')) {
  function get_sms_balance()
  {
    $url = 'http://sms.bulksmsind.in/getSMSCredit?username=Ajay%20Kushwaha&apikey=3f2f1020-d3ac-4b71-8027-040e44b12f4b';
    $result = file_get_contents($url);
    return $result;
  }
}

// ------------------------------------------------------------------------

/* End of file user_helper.php */

/* Location: ./system/helpers/common.php */
