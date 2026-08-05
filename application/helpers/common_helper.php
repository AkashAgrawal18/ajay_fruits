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



// Removed: currency(), currency_code_and_symbol() and get_frontend_settings().
// They queried `settings`, `currency` and `frontend_settings`, none of which exist
// in this schema, and nothing in the application called them (BUG-025).

// Reversible encryption for staff/branch login passwords, kept alongside the
// one-way password_hash() used for actual login (BUG-026 / superadmin
// "view password" feature). Only ever call decrypt_password_for_admin() from
// code already gated to superadmin - this deliberately undoes the hash.
if (! function_exists('encrypt_password_for_admin')) {

  function encrypt_password_for_admin($plaintext)
  {
    if ($plaintext === '' || $plaintext === null) {
      return '';
    }

    $CI = &get_instance();
    $CI->config->load('integrations', TRUE, TRUE);
    $secret = (string) $CI->config->item('password_enc_key', 'integrations');
    if ($secret === '') {
      return '';
    }

    $key    = hash('sha256', $secret, true);
    $ivlen  = openssl_cipher_iv_length('aes-256-cbc');
    $iv     = openssl_random_pseudo_bytes($ivlen);
    $cipher = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    if ($cipher === false) {
      return '';
    }

    return base64_encode($iv . $cipher);
  }
}

if (! function_exists('decrypt_password_for_admin')) {

  function decrypt_password_for_admin($encoded)
  {
    if (empty($encoded)) {
      return '';
    }

    $CI = &get_instance();
    $CI->config->load('integrations', TRUE, TRUE);
    $secret = (string) $CI->config->item('password_enc_key', 'integrations');
    if ($secret === '') {
      return '';
    }

    $raw = base64_decode($encoded, true);
    if ($raw === false) {
      return '';
    }

    $key   = hash('sha256', $secret, true);
    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
    if (strlen($raw) <= $ivlen) {
      return '';
    }

    $iv     = substr($raw, 0, $ivlen);
    $cipher = substr($raw, $ivlen);
    $plain  = openssl_decrypt($cipher, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    return $plain === false ? '' : $plain;
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
    // Credentials come from config/integrations.php (environment-backed).
    // They used to be inline here and remain in git history — rotate them at
    // the provider rather than relying on this move (BUG-022).
    $CI = &get_instance();
    $CI->config->load('integrations', TRUE, TRUE);

    $username = (string) $CI->config->item('sms_username', 'integrations');
    $apikey   = (string) $CI->config->item('sms_apikey', 'integrations');

    if ($username === '' || $apikey === '') {
      log_message('error', 'SMS credentials are not configured; skipping balance lookup.');
      return '';
    }

    $url = 'http://sms.bulksmsind.in/getSMSCredit?username=' . rawurlencode($username)
      . '&apikey=' . rawurlencode($apikey);

    return @file_get_contents($url);
  }
}

// ------------------------------------------------------------------------

/* End of file user_helper.php */

/* Location: ./system/helpers/common.php */
