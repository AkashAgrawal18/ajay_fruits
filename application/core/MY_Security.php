<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keeps the browser's CSRF token and the server's in sync at all times.
 *
 * The symptom this fixes: a page would 403 with a bare "not allowed" /
 * "Access Denied" error the *first* time an action was taken, and then work
 * after going back and retrying. It looked like a permissions problem
 * (superadmin/head office saw it most, because their screens - purchases,
 * branch transfers, branch-scoped dropdowns - fire the most AJAX POSTs) but
 * it was never a role check: every gate in the app already lets user_type 8
 * through. It was the CSRF token going stale mid-page.
 *
 * How it went stale: csrf_regenerate=TRUE rotated the token on every accepted
 * POST and handed the new value back only in an HttpOnly cookie that JS can't
 * read. Only three endpoints echoed the rotated token in their JSON body, so
 * on every other screen the 2nd POST of a page load still carried the token
 * the page was rendered with - already dead - and CI rejected it with a 403
 * before the controller ever ran. Reloading (i.e. "go back and retry") minted
 * a fresh token into the page, which is why the retry always worked.
 *
 * Two changes make it self-correcting:
 *   1. config.php now sets csrf_regenerate=FALSE, so the token is stable for
 *      the whole session and no amount of parallel or repeated POSTing can
 *      race it. Protection is unchanged - the token is still per-session,
 *      still unguessable, and still verified on every POST.
 *   2. Every response now carries the current token in X-CSRF-Token-* headers
 *      (see csrf_set_cookie below), and a rejected AJAX POST answers with a
 *      machine-readable JSON body instead of an HTML error page (see
 *      csrf_show_error). footer.php reads both, so even if a token does go
 *      stale - it expired, or another app on the same host overwrote the
 *      cookie - the client picks up the good one and replays the request
 *      itself, instead of showing the user a 403.
 */
class MY_Security extends CI_Security
{
	/**
	 * Called by csrf_verify() on every single request - on GETs (which return
	 * early into it) and on POSTs after the token has been resolved, including
	 * the rejected ones, since it runs before csrf_show_error(). That makes it
	 * the one place guaranteed to see the token the browser should be holding,
	 * so it is where the token gets published back to the client.
	 */
	public function csrf_set_cookie()
	{
		$result = parent::csrf_set_cookie();

		// Bootstrap runs before any controller output, so this is safe; the
		// guard is only for CLI/odd entry points that have already flushed.
		if ( ! headers_sent())
		{
			header('X-CSRF-Token-Name: '.$this->get_csrf_token_name());
			header('X-CSRF-Token-Value: '.$this->get_csrf_hash());
		}

		return $result;
	}

	/**
	 * A stale token is a recoverable client-side condition, not a permissions
	 * failure, so don't answer AJAX with CI's generic HTML error page - the
	 * caller can't parse it and the user just sees "not allowed".
	 *
	 * Answer with JSON carrying the good token and a csrf_error flag. The
	 * request was rejected during bootstrap, so no controller ran and nothing
	 * was written: replaying it with the fresh token is safe, and footer.php
	 * does exactly that, once.
	 */
	public function csrf_show_error()
	{
		if ($this->_is_ajax())
		{
			// csrf_verify() runs from CI_Input's constructor, long before the
			// controller (or even CI_Controller itself) is loaded, so there is
			// no get_instance() to reach $this->output through - use the
			// bootstrap-level helper from system/core/Common.php instead.
			set_status_header(403);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array(
				'status'           => 'error',
				'csrf_error'       => TRUE,
				'message'          => 'Your session security token had expired. Please try again.',
				'csrf_token_name'  => $this->get_csrf_token_name(),
				'csrf_token_value' => $this->get_csrf_hash(),
			));
			exit;
		}

		show_error(
			'Your session security token had expired, so this action was not submitted. '
			.'Please reload the page and try again.',
			403
		);
	}

	private function _is_ajax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}
}
