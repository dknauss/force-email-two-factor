<?php
/**
 * Zero-dependency test bootstrap.
 *
 * Defines just enough of the WordPress surface for the plugin's pure decision
 * logic to run under PHPUnit — no WP install, no Brain Monkey/Patchwork (which
 * are fragile on bleeding-edge PHP). Stub behaviour is driven by globals that
 * each test sets via Force2FA\TestCase helpers and resets in setUp().
 */

define( 'ABSPATH', __DIR__ . '/' ); // satisfies the plugin's `defined('ABSPATH') || exit`.

$GLOBALS['__force2fa_filters']    = array(); // hook => override return value
$GLOBALS['__force2fa_users']      = array(); // id => WP_User
$GLOBALS['__force2fa_did_action'] = array(); // hook => count

// --- WordPress function stubs -------------------------------------------------

// Registering hooks is a no-op here; we call the plugin's named callbacks directly.
function add_filter() {
	return true;
}

/**
 * Returns a per-hook override when a test set one, otherwise passes the value
 * through unchanged (WordPress's default behaviour with no filters attached).
 */
function apply_filters( $hook, $value = null ) {
	return array_key_exists( $hook, $GLOBALS['__force2fa_filters'] )
		? $GLOBALS['__force2fa_filters'][ $hook ]
		: $value;
}

function get_userdata( $user_id ) {
	return $GLOBALS['__force2fa_users'][ $user_id ] ?? false;
}

function did_action( $hook ) {
	return $GLOBALS['__force2fa_did_action'][ $hook ] ?? 0;
}

// --- WordPress class stubs ----------------------------------------------------

if ( ! class_exists( 'WP_User' ) ) {
	class WP_User {
		public $ID;
		public $user_login;
		public $roles;

		public function __construct( $id = 0, $user_login = '', array $roles = array() ) {
			$this->ID         = $id;
			$this->user_login = $user_login;
			$this->roles      = $roles;
		}
	}
}

// Present by default so the enforcement filter takes its append path; the
// class-absent guard is exercised manually rather than in unit tests.
if ( ! class_exists( 'Two_Factor_Email' ) ) {
	class Two_Factor_Email {}
}

// --- Load the plugin under test ----------------------------------------------

require dirname( __DIR__ ) . '/force-email-two-factor.php';

require __DIR__ . '/TestCase.php';
