<?php

namespace Force2FA\Tests;

use Force2FA\TestCase;

/**
 * API-login allowlist matching: force_2fa_user_is_api_allowlisted().
 */
final class AllowlistTest extends TestCase {

	public function test_empty_allowlist_matches_nobody(): void {
		$user = $this->user( 5, 'svc', array( 'author' ) );
		$this->assertFalse( force_2fa_user_is_api_allowlisted( $user ) );
	}

	public function test_match_by_numeric_id(): void {
		$this->allowlist( array( 5 ) );
		$user = $this->user( 5, 'svc', array( 'author' ) );
		$this->assertTrue( force_2fa_user_is_api_allowlisted( $user ) );
	}

	public function test_match_by_numeric_string_id(): void {
		$this->allowlist( array( '5' ) );
		$user = $this->user( 5, 'svc', array( 'author' ) );
		$this->assertTrue( force_2fa_user_is_api_allowlisted( $user ) );
	}

	public function test_match_by_login_case_insensitive(): void {
		$this->allowlist( array( 'svc_headless' ) );
		$user = $this->user( 7, 'SVC_Headless', array( 'author' ) );
		$this->assertTrue( force_2fa_user_is_api_allowlisted( $user ) );
	}

	public function test_non_matching_user_is_rejected(): void {
		$this->allowlist( array( 5, 'svc_headless' ) );
		$user = $this->user( 3, 'editoruser', array( 'editor' ) );
		$this->assertFalse( force_2fa_user_is_api_allowlisted( $user ) );
	}

	public function test_login_entry_does_not_match_a_numeric_id(): void {
		// An allowlisted login string must not accidentally match by ID.
		$this->allowlist( array( 'svc_headless' ) );
		$user = $this->user( 5, 'someoneelse', array( 'author' ) );
		$this->assertFalse( force_2fa_user_is_api_allowlisted( $user ) );
	}
}
