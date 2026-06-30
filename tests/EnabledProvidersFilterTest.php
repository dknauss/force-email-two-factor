<?php

namespace Force2FA\Tests;

use Force2FA\TestCase;

/**
 * The enforcement filter callback: force_2fa_filter_enabled_providers().
 */
final class EnabledProvidersFilterTest extends TestCase {

	public function test_appends_email_for_a_normal_user(): void {
		$this->user( 3, 'editoruser', array( 'editor' ) );
		$result = force_2fa_filter_enabled_providers( array(), 3 );
		$this->assertSame( array( 'Two_Factor_Email' ), $result );
	}

	public function test_preserves_existing_stronger_factor(): void {
		$this->user( 3, 'editoruser', array( 'editor' ) );
		$result = force_2fa_filter_enabled_providers( array( 'Two_Factor_Totp' ), 3 );
		$this->assertSame( array( 'Two_Factor_Totp', 'Two_Factor_Email' ), $result );
	}

	public function test_does_not_duplicate_email_when_already_present(): void {
		$this->user( 3, 'editoruser', array( 'editor' ) );
		$result = force_2fa_filter_enabled_providers( array( 'Two_Factor_Email' ), 3 );
		$this->assertSame( array( 'Two_Factor_Email' ), $result );
	}

	public function test_malformed_non_array_input_becomes_array_with_email(): void {
		$this->user( 3, 'editoruser', array( 'editor' ) );
		$result = force_2fa_filter_enabled_providers( null, 3 );
		$this->assertSame( array( 'Two_Factor_Email' ), $result );
	}

	public function test_excluded_user_list_is_left_untouched(): void {
		$this->excludeRoles( array( 'subscriber' ) );
		$this->user( 4, 'subuser', array( 'subscriber' ) );
		$result = force_2fa_filter_enabled_providers( array(), 4 );
		$this->assertSame( array(), $result );
	}

	public function test_unknown_user_still_gets_email_floor(): void {
		// get_userdata() returns false (no such user registered) → not exempt,
		// so the email floor is still applied.
		$result = force_2fa_filter_enabled_providers( array(), 999 );
		$this->assertSame( array( 'Two_Factor_Email' ), $result );
	}
}
