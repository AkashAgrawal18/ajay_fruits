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

// A field off a single-row lookup that may have returned null.
//
// Ledgers and reports read the display name / opening balance straight off a
// get_*_dtl() result. Those return null whenever the id is blank (the "All"
// option), is Head Office (which has no master_users_tbl row of its own), or
// belongs to another branch - and the bare read then prints "Attempt to read
// property on null" across the top of the report, as Branch Ledger did.
if (! function_exists('row_val')) {

  function row_val($row, $field, $fallback = '')
  {
    return (is_object($row) && isset($row->$field)) ? $row->$field : $fallback;
  }
}

if (! function_exists('money2')) {

  /**
   * A money amount as a plain string with exactly two decimals.
   *
   * Every amount column in this schema is a `double`, so sums drift (a customer
   * balance is stored as 53946.078, and float addition yields tails like
   * ...0000004). Use this wherever an amount is printed without thousands
   * separators - form values, CSV/Excel cells, PDF cells, JSON.
   */
  function money2($num)
  {
    if ($num === null || $num === '' || !is_numeric($num)) {
      return '0.00';
    }
    // +0 collapses "-0.00" to "0.00"
    return number_format(round((float) $num, 2) + 0, 2, '.', '');
  }
}

if (! function_exists('IND_money_format')) {

  /**
   * A money amount in Indian digit grouping (1,00,000.00) with exactly two
   * decimals.
   *
   * The previous implementation split the integer and fractional parts by hand
   * and got three things wrong:
   *   -1234.56  -> "-1,234.-56"  (sprintf('%02d') keeps the fraction's sign)
   *   -0.75     -> "0.-75"       ((int) of -0.75 is 0, so the sign vanished)
   *   1234.999  -> "1,234.1"     (round() carried to 100, then rtrim ate the 0)
   * and it trimmed trailing zeros, so amounts rendered with 0, 1 or 2 decimals
   * depending on their value. Negative balances are ordinary here (a customer
   * in credit), so the first two were visible in the balance and ledger pages.
   *
   * Formatting the rounded absolute value and re-applying the sign afterwards
   * avoids all four problems.
   */
  function IND_money_format($num)
  {
    if ($num === null || $num === '' || !is_numeric($num)) {
      return '0.00';
    }

    $num = round((float) $num, 2);
    $sign = ($num < 0) ? '-' : '';
    $abs  = abs($num);

    // split on the rounded string so the carry in 1234.999 -> 1235.00 is kept
    $fixed = number_format($abs, 2, '.', '');
    list($int_part, $decimal) = explode('.', $fixed);

    if (strlen($int_part) > 3) {
      $lastThree = substr($int_part, -3);
      $restUnits = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($int_part, 0, -3));
      $int_part  = $restUnits . ',' . $lastThree;
    }

    // "-0.00" reads wrong; a rounded-to-zero amount is just zero
    if ($int_part === '0' && $decimal === '00') {
      $sign = '';
    }

    return $sign . $int_part . '.' . $decimal;
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
