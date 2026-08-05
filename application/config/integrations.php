<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Third-party integration credentials and link signing
| -------------------------------------------------------------------------
|
| Values are read from the environment first so that production secrets never
| have to live in the repository. The literals below are only fallbacks for
| local development.
|
| IMPORTANT: the WhatsApp and SMS keys previously sat in application code and
| are therefore still readable in git history. They must be ROTATED at the
| provider — moving them here does not invalidate the old values.
|
| Set these in the server environment (or an untracked local override):
|   AJAYFRUITS_WHATSAPP_PHONE_ID
|   AJAYFRUITS_WHATSAPP_APIKEY
|   AJAYFRUITS_SMS_USERNAME
|   AJAYFRUITS_SMS_APIKEY
|   AJAYFRUITS_LINK_SECRET
|   AJAYFRUITS_API_TOKEN_TTL   (seconds, optional)
|   AJAYFRUITS_PASSWORD_ENC_KEY
*/

$config['whatsapp_phone_number_id'] = getenv('AJAYFRUITS_WHATSAPP_PHONE_ID') ?: '';
$config['whatsapp_apikey']          = getenv('AJAYFRUITS_WHATSAPP_APIKEY') ?: '';

$config['sms_username'] = getenv('AJAYFRUITS_SMS_USERNAME') ?: '';
$config['sms_apikey']   = getenv('AJAYFRUITS_SMS_APIKEY') ?: '';

/*
| Secret used to sign customer statement links (BUG-016). Any non-empty value
| works, but it must be kept private and must not change casually — changing it
| invalidates every link already sent to a customer.
*/
$config['link_secret'] = getenv('AJAYFRUITS_LINK_SECRET') ?: '';

/* How long a signed statement link stays valid, in seconds (default 30 days). */
$config['link_ttl'] = (int) (getenv('AJAYFRUITS_LINK_TTL') ?: 2592000);

/* How long a mobile API token stays valid, in seconds (default 30 days). */
$config['api_token_ttl'] = (int) (getenv('AJAYFRUITS_API_TOKEN_TTL') ?: 2592000);

/*
| Key used to reversibly encrypt staff/branch login passwords so superadmin can
| view them (alongside the one-way bcrypt hash used for actual login). Keep
| this private - anyone with this key plus DB access can read every stored
| password. Rotating it makes previously-encrypted passwords undecryptable
| until the user's password is next changed.
*/
$config['password_enc_key'] = getenv('AJAYFRUITS_PASSWORD_ENC_KEY') ?: 'ajayfruits-local-dev-password-key';
