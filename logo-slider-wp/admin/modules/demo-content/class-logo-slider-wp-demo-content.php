<?php
/**
 * The Demo Content module controller.
 *
 * Seeds a small set of generic demo logo items on a genuinely fresh install so
 * the plugin does not look empty on first use (BACKLOG B87, "Approach D —
 * Logo-Only Seed"), tags what it created, releases ("graduates") any item the
 * user edits, and provides an explicit user-triggered removal action.
 *
 * BACKLOG B90 extends this with one demo *showcase*, seeded in the same gated
 * run immediately after the logos. It is created through the real save path —
 * `$_POST` + `wp_insert_post()` + `save_post_lgx_lsp_shortcodes` — so it passes
 * through the same nonce, capability and Free/Pro retain-on-reject enforcement
 * as any user save; no gating is bypassed or reimplemented here. Its field
 * values come entirely from the Showcase Builder registry's own declared
 * defaults, so this module holds no value list of its own.
 *
 * (The line that previously stood here — "Nothing here creates a showcase,
 * writes `_lgx_lsp_shortcodes_meta`, or touches any Free/Pro gating" — was
 * accurate for B87 and is superseded by B90.)
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/demo-content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Logo_Slider_WP_Demo_Content' ) ) {
class Logo_Slider_WP_Demo_Content {

	/**
	 * Post meta marking a `logosliderwp` item as still-untouched seeded demo
	 * content. Value is the slot identifier 1-6, which records which bundled
	 * asset the item came from (B87 D14) — not a bare boolean.
	 */
	const DEMO_LOGO_META = '_lgx_demo_logo';

	/**
	 * Attachment meta marking an attachment as created by the seed routine.
	 */
	const DEMO_ATTACHMENT_META = '_lgx_demo_attachment';

	/**
	 * Post meta marking the single `lgx_lsp_shortcodes` showcase created by the
	 * seed routine (B90 §3.3).
	 *
	 * Unlike DEMO_LOGO_META this carries no slot number — the seed creates
	 * exactly one showcase, never a set, so there is nothing to disambiguate.
	 */
	const DEMO_SHOWCASE_META = '_lgx_demo_showcase';

	/**
	 * One-shot option set by the Activator when a genuinely fresh install is
	 * detected. Deliberately separate from `_logosliderwp_activation_redirect`
	 * — the redirect and the seed are two concerns and must never share one
	 * switch (B87 §5.2).
	 */
	const SEED_PENDING_OPTION = '_logosliderwp_demo_seed_pending';

	/**
	 * Post statuses that count as "this site already has content."
	 *
	 * Deliberately an explicit list rather than the `'any'` shorthand: `'any'`
	 * silently excludes `trash` (and `auto-draft`), so a site whose only logo
	 * sits in the trash would look fresh and get re-seeded. Verified against
	 * real generated SQL in Phase 1 (B87 D11 / §5.3).
	 *
	 * `auto-draft` is intentionally NOT in this list — an auto-draft is an
	 * empty placeholder created by clicking "Add New" and never saving, purged
	 * by core after 7 days. It was never real content, so it must not block
	 * seeding.
	 */
	const EXISTING_CONTENT_STATUSES = array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' );

	/**
	 * Unique identifier of the plugin.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Version of the plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * True while the seed routine is running.
	 *
	 * Defense in depth only. `edit_post_{$post_type}` fires inside
	 * wp_insert_post() exclusively when `$update` is true (wp-includes/post.php,
	 * `if ( $update ) { do_action( "edit_post_{$post->post_type}", ... ); }`),
	 * so the seed's own inserts cannot reach the graduation handler in the
	 * first place. This flag guarantees it even if that ever changes.
	 *
	 * @var bool
	 */
	private $is_seeding = false;

	/**
	 * Initialize the module.
	 *
	 * Hooks are registered by Logo_Slider_WP::define_admin_hooks() through the
	 * shared loader, not here — see that file. (The onboarding module
	 * self-registers in its own constructor; that is a pre-existing
	 * inconsistency with this project's wiring standard, not a pattern this
	 * module extends. B87 §5.2.)
	 *
	 * @param string $plugin_name Plugin name identifier.
	 * @param string $version     Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/* ---------------------------------------------------------------------
	 * Graduation on user edit (B87 §8) — SAFETY-CRITICAL
	 * ------------------------------------------------------------------- */

	/**
	 * Release a seeded demo logo the moment the user genuinely edits it.
	 *
	 * Hooked to `edit_post_logosliderwp`. Once the `_lgx_demo_logo` marker is
	 * gone the item is ordinary user content and "Remove Demo Content" can
	 * never touch it again — this is the entire safety guarantee behind the
	 * removal action, so it runs before anything else in this module.
	 *
	 * `edit_post_{$post_type}` fires only on update, never on the initial
	 * insert, so the seed routine cannot un-tag what it just created.
	 *
	 * @param int $post_id Post ID being updated.
	 */
	public function maybe_graduate_demo_logo( $post_id ) {
		// Defense in depth — see $is_seeding.
		if ( $this->is_seeding ) {
			return;
		}

		$post_id = (int) $post_id;
		if ( $post_id <= 0 ) {
			return;
		}

		// Autosaves and revisions are not the user deciding to keep this item.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Not seeded demo content (or already graduated) — nothing to do.
		$slot = get_post_meta( $post_id, self::DEMO_LOGO_META, true );
		if ( '' === $slot || null === $slot || false === $slot ) {
			return;
		}

		// Graduate: the item becomes ordinary user content, permanently.
		delete_post_meta( $post_id, self::DEMO_LOGO_META );

		/*
		 * Release the attachment this item now points at, so a later "Remove
		 * Demo Content" can never delete an image a graduated item depends on.
		 *
		 * The brief phrases this as "if the featured image changed, delete
		 * _lgx_demo_attachment from the NEW thumbnail ID." edit_post carries no
		 * before/after thumbnail to compare, so this releases whatever the
		 * thumbnail is now. That is a strict superset of the brief's rule and
		 * strictly safer: it covers both the unchanged case (harmless — the
		 * removal query only reaches attachments owned by a still-tagged post)
		 * and the case the rule exists for (the user pointed this item at
		 * another seeded item's attachment, which removal would otherwise
		 * delete out from under it). Documented rather than folded in silently.
		 */
		$thumbnail_id = (int) get_post_thumbnail_id( $post_id );
		if ( $thumbnail_id > 0 ) {
			delete_post_meta( $thumbnail_id, self::DEMO_ATTACHMENT_META );
		}
	}

	/**
	 * Release the seeded demo showcase the moment the user genuinely edits it.
	 *
	 * Hooked to `edit_post_lgx_lsp_shortcodes` (B90 §4) — deliberately not
	 * `save_post_lgx_lsp_shortcodes`. `edit_post_{$post_type}` fires inside
	 * wp_insert_post() only when `$update` is true, so the seed routine's own
	 * initial insert cannot reach this and un-tag the showcase it just created.
	 * Same reasoning, same shape, and the same $is_seeding belt-and-braces as
	 * maybe_graduate_demo_logo() above.
	 *
	 * Once the marker is gone the showcase is ordinary user content and
	 * "Remove Demo Content" can never touch it again.
	 *
	 * @param int $post_id Post ID being updated.
	 */
	public function maybe_graduate_demo_showcase( $post_id ) {
		// Defense in depth — see $is_seeding.
		if ( $this->is_seeding ) {
			return;
		}

		$post_id = (int) $post_id;
		if ( $post_id <= 0 ) {
			return;
		}

		// Autosaves and revisions are not the user deciding to keep this.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Not the seeded showcase (or already graduated) — nothing to do.
		$tag = get_post_meta( $post_id, self::DEMO_SHOWCASE_META, true );
		if ( '' === $tag || null === $tag || false === $tag ) {
			return;
		}

		// Graduate: the showcase becomes ordinary user content, permanently.
		delete_post_meta( $post_id, self::DEMO_SHOWCASE_META );
	}

	/* ---------------------------------------------------------------------
	 * Seed trigger (B87 §5.2)
	 * ------------------------------------------------------------------- */

	/**
	 * Consume the pending-seed flag on an admin load and seed if still valid.
	 *
	 * Hooked to `admin_init`. The guard order deliberately mirrors
	 * Logo_Slider_WP_Onboarding::maybe_redirect_to_dashboard() step for step,
	 * including consuming the flag before any work so a failure or an
	 * unexpected second `admin_init` in the same request cannot loop or
	 * double-seed.
	 */
	public function maybe_seed_demo_content() {
		// 1. Nothing pending.
		if ( 'yes' !== get_option( self::SEED_PENDING_OPTION ) ) {
			return;
		}

		/*
		 * 2-7. Is this request even eligible to seed?
		 *
		 * These run BEFORE the flag is consumed (F-1). They previously ran
		 * after it, which meant any ineligible request destroyed the one-shot
		 * flag on its way out and the site could never seed again. WordPress's
		 * Heartbeat polls admin-ajax.php every 15-60s from any open wp-admin
		 * tab, so in practice a single background request landing between
		 * activation and the first real admin page load silently and
		 * permanently disabled demo seeding. Reproduced deterministically; it
		 * is also the most likely explanation for the B87 incident recorded as
		 * "consumed the seed flag without seeding, cause undetermined".
		 *
		 * Every one of these six guards had the same defect, not just the AJAX
		 * one — a bulk activation, or a low-privilege user simply loading
		 * wp-admin first, killed seeding site-wide just as effectively.
		 *
		 * These are all cheap in-memory checks with no database access, which
		 * is what keeps the get_option()/delete_option() window below as narrow
		 * as possible.
		 */
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( wp_doing_ajax() ) {
			return;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			return;
		}
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return; // B87 D9 — WP-CLI never seeds.
		}

		/*
		 * 8. Consume — this is a genuine, eligible admin page load.
		 *
		 * Still consumed BEFORE any seeding work, so B87's two original
		 * guarantees are unchanged: a failure inside seed() cannot loop on a
		 * later request, and an unexpected second admin_init in the same
		 * request is stopped by the flag check above (delete_option() also
		 * clears the options cache, so the re-entrant read returns false).
		 *
		 * Deliberately placed before the content re-verify rather than
		 * immediately before seed(): if a site turns out to already have
		 * content, the flag is spent exactly as it always was, so an
		 * established site cannot suddenly sprout demo content months later
		 * if its logos are deleted. It also keeps the window between reading
		 * and clearing the flag free of database queries.
		 */
		delete_option( self::SEED_PENDING_OPTION );

		// 9. Defensive re-verify: state may have changed between activation
		// and this admin load (another admin added a logo in the meantime).
		if ( self::site_has_existing_content() ) {
			return;
		}

		// 10. Seed.
		$this->seed();
	}

	/* ---------------------------------------------------------------------
	 * Seed routine (B87 §6 / §7)
	 * ------------------------------------------------------------------- */

	/**
	 * The six demo brand names, keyed by slot.
	 *
	 * Not translatable on purpose: these are invented proper nouns acting as
	 * replaceable placeholder labels, not UI copy. All six were checked
	 * against trademark registries and company registers before use (B87 §4).
	 *
	 * @return array<int,string>
	 */
	public static function get_brand_names() {
		return array(
			1 => 'Fernwick Studio',
			2 => 'Meridow Group',
			3 => 'Cobalt & Pine',
			4 => 'Verdanta Labs',
			5 => 'Kestrelane Media',
			6 => 'Thornbury Works',
		);
	}

	/**
	 * Absolute path to a bundled demo PNG for a given slot.
	 *
	 * @param int $slot Slot number 1-6.
	 * @return string
	 */
	private function get_asset_path( $slot ) {
		return plugin_dir_path( __FILE__ ) . 'assets/img/demo-logo-' . (int) $slot . '.png';
	}

	/**
	 * Create the six demo logo items and their attachments.
	 *
	 * Uses a local file copy only — never media_sideload_image() or any other
	 * HTTP-based helper — so seeding works offline, on any host, with no
	 * external request (B87 §6).
	 *
	 * @return int Number of slots successfully seeded.
	 */
	private function seed() {
		// wp_generate_attachment_metadata() lives in an admin include that
		// admin_init does not guarantee is already loaded.
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$this->is_seeding = true;
		$seeded = 0;

		foreach ( self::get_brand_names() as $slot => $brand_name ) {
			if ( $this->seed_slot( $slot, $brand_name ) ) {
				$seeded++;
			}
		}

		/*
		 * B90: the demo showcase, seeded after the logos so it has something to
		 * display, and inside the same single both-zero-gate check performed by
		 * maybe_seed_demo_content() — see seed_showcase()'s docblock and §6.
		 *
		 * Only attempted when at least one logo actually seeded: a showcase with
		 * no logos to show would be worse than no showcase at all.
		 */
		if ( $seeded > 0 ) {
			$this->seed_showcase();
		}

		$this->is_seeding = false;

		return $seeded;
	}

	/**
	 * The Showcase Builder registry's own defaults, shaped as a browser would
	 * actually submit them.
	 *
	 * Reads `config.php` live (B90 §3.1) using the same `include` contract
	 * builder-wrapper.php uses, so the four values changed in §2 are picked up
	 * with no special-casing here — the registry is the single source of truth
	 * and this method holds no value list of its own.
	 *
	 * `group2SelectText` declares two meta keys in one field entry and is
	 * unpacked accordingly; fields with no `'default'` key are skipped and fall
	 * to the save handler's own fallback (§3.1's table — all resolve benignly).
	 *
	 * ---------------------------------------------------------------------
	 * `switch` fields are PRESENCE-based, not value-based — do not "simplify"
	 * this away.
	 *
	 * The save handler stores every one of them as:
	 *
	 *     $savable_Data['x'] = isset( $postData['x'] ) ? 'yes' : 'no';
	 *
	 * It never reads the submitted *value*, only whether the key is there —
	 * because that is how a browser posts a checkbox: an unchecked control is
	 * absent from $_POST entirely. Sending `'x' => 'no'` therefore stores
	 * `'yes'`, the exact opposite of what the registry declares.
	 *
	 * Found during Phase 2b verification: the first working seed stored
	 * `lgx_brand_name_en = 'yes'` against a registry default of `'no'` — the
	 * one value `006b` §4 explicitly rejected — and would have wrongly switched
	 * on all 23 `'no'`-default switches. Verified live: the set of fields using
	 * the handler's presence pattern and the set of registry `type => 'switch'`
	 * fields are identical, 32 and 32, with zero difference in either
	 * direction. So the registry's own `type` drives this, with no second
	 * hardcoded list — which is the whole point of §3.1.
	 * ---------------------------------------------------------------------
	 *
	 * @return array<string,mixed>
	 */
	private function get_registry_defaults() {
		$config_path = plugin_dir_path( dirname( __FILE__ ) ) . 'showcase-builder/config/config.php';
		if ( ! file_exists( $config_path ) ) {
			return array();
		}

		$registry = include $config_path;
		if ( ! is_array( $registry ) ) {
			return array();
		}

		$defaults = array();

		foreach ( $registry as $tab ) {
			if ( empty( $tab['groups'] ) || ! is_array( $tab['groups'] ) ) {
				continue;
			}

			foreach ( $tab['groups'] as $group ) {
				if ( empty( $group['fields'] ) || ! is_array( $group['fields'] ) ) {
					continue;
				}

				foreach ( $group['fields'] as $field ) {
					if ( isset( $field['type'] ) && 'group2SelectText' === $field['type'] ) {
						if ( isset( $field['id_select'] ) ) {
							$defaults[ $field['id_select'] ] = $field['default_select'];
						}
						if ( isset( $field['id_text'] ) ) {
							$defaults[ $field['id_text'] ] = $field['default_text'];
						}
						continue;
					}

					if ( ! isset( $field['id'] ) || ! array_key_exists( 'default', $field ) ) {
						continue;
					}

					// Presence-based control: omit entirely when it should be
					// off, exactly as an unchecked box is absent from $_POST.
					if ( isset( $field['type'] ) && 'switch' === $field['type'] ) {
						if ( 'yes' === $field['default'] ) {
							$defaults[ $field['id'] ] = 'yes';
						}
						continue;
					}

					$defaults[ $field['id'] ] = $field['default'];
				}
			}
		}

		return $defaults;
	}

	/**
	 * Create the single demo showcase through the real save path (B90 §3).
	 *
	 * Deliberately does NOT re-check site_has_existing_content() (B90 §6): the
	 * both-zero gate is evaluated exactly once, in maybe_seed_demo_content()
	 * before this whole routine starts. Re-checking here would see the six
	 * logos this same run just created, return true, and silently skip the
	 * showcase forever — a bug that would look correct in review.
	 *
	 * @return int Showcase post ID, or 0 on failure.
	 */
	private function seed_showcase() {
		$payload = $this->get_registry_defaults();
		if ( empty( $payload ) ) {
			return 0;
		}

		/*
		 * The one field the registry cannot supply (B90 §3.1 step 3): it has no
		 * config.php entry at all, and the save handler reads it without an
		 * isset() guard (filed separately as B91). Left unset it would store an
		 * empty string, which renders a completely blank showcase — proven in
		 * 006b §9.6. Set explicitly, never sourced from the iteration.
		 */
		$payload['lgx_lswp_showcase_type'] = 'carousel';

		// Nested exactly where the handler reads it: $_POST['meta_lgx_lsp_shortcodes']['nonce'].
		$payload['nonce'] = wp_create_nonce( 'meta_lgx_lsp_shortcodes' );

		/*
		 * Populate $_POST *before* the insert (B90 §3.2): wp_insert_post() fires
		 * save_post_lgx_lsp_shortcodes synchronously, and the handler reads
		 * $_POST during that call. Nothing in the chain is bypassed — the seed
		 * goes through the same nonce check, capability check and Free/Pro
		 * retain-on-reject enforcement as any real user save.
		 */
		$had_post = isset( $_POST['meta_lgx_lsp_shortcodes'] );
		$previous = $had_post ? $_POST['meta_lgx_lsp_shortcodes'] : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- preserved and restored, not consumed.

		$_POST['meta_lgx_lsp_shortcodes'] = $payload;

		$showcase_id = wp_insert_post(
			array(
				'post_type'   => 'lgx_lsp_shortcodes',
				'post_title'  => 'Demo Showcase',
				'post_status' => 'publish',
			)
		);

		// Restore rather than blindly unset, so an admin_init running inside a
		// real form submission is left exactly as it was found.
		if ( $had_post ) {
			$_POST['meta_lgx_lsp_shortcodes'] = $previous;
		} else {
			unset( $_POST['meta_lgx_lsp_shortcodes'] );
		}

		if ( is_wp_error( $showcase_id ) || ! $showcase_id ) {
			return 0;
		}

		// Tag it (B90 §3.3) — single key, no slot number: there is only ever one.
		update_post_meta( $showcase_id, self::DEMO_SHOWCASE_META, 1 );

		return (int) $showcase_id;
	}

	/**
	 * Seed one slot: attachment, then logo item, then tags.
	 *
	 * A slot that cannot be created is skipped rather than aborting the run or
	 * raising an error — this executes on admin_init and must never break the
	 * admin screen over cosmetic demo content.
	 *
	 * @param int    $slot       Slot number 1-6.
	 * @param string $brand_name Display title for the item.
	 * @return bool Whether the slot was seeded.
	 */
	private function seed_slot( $slot, $brand_name ) {
		$source = $this->get_asset_path( $slot );
		if ( ! is_readable( $source ) ) {
			return false;
		}

		$contents = file_get_contents( $source ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- local bundled asset, not a remote request.
		if ( false === $contents ) {
			return false;
		}

		// 1. Copy into this site's own uploads directory.
		$upload = wp_upload_bits( 'demo-logo-' . (int) $slot . '.png', null, $contents );
		if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
			return false;
		}

		// 2. Real Media Library attachment.
		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/png',
				'post_title'     => $brand_name,
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$upload['file']
		);

		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			return false;
		}

		// 3. Generate the intermediate sizes WordPress expects.
		$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		if ( ! empty( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		// 4. Tag the attachment as ours.
		update_post_meta( $attachment_id, self::DEMO_ATTACHMENT_META, 1 );

		// 5. Accessibility hygiene — alt text defaults to the brand name.
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $brand_name );

		// 6. The logo item itself.
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'logosliderwp',
				'post_title'  => $brand_name,
				'post_status' => 'publish',
			)
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			// Don't leave the attachment orphaned if the item failed.
			wp_delete_attachment( $attachment_id, true );
			return false;
		}

		// 7. Link the image.
		set_post_thumbnail( $post_id, $attachment_id );

		// 8. Tag the item with its slot (B87 D14).
		update_post_meta( $post_id, self::DEMO_LOGO_META, (int) $slot );

		/*
		 * Deliberately NOT set (B87 §6.1): `_logosliderwpmeta` in any form,
		 * `company_url`, and any `logosliderwpcat` term. Tooltip and alt text
		 * already fall back to the brand name, an empty URL keeps the logo
		 * non-clickable, and no categories are seeded.
		 */

		return true;
	}

	/* ---------------------------------------------------------------------
	 * "Remove Demo Content" (B87 §9)
	 * ------------------------------------------------------------------- */

	/**
	 * AJAX handler for the Dashboard's "Remove Demo Content" action.
	 *
	 * Follows the shape of
	 * Logo_Slider_WP_Dashboard::handle_dismiss_onboarding_step_ajax().
	 */
	public function handle_remove_demo_content_ajax() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'lgx_ls_remove_demo_content' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'logo-slider-wp' ) ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'logo-slider-wp' ) ) );
		}

		$result   = $this->remove_demo_content();
		$logos    = (int) $result['logos'];
		$showcase = (int) $result['showcase'];

		/*
		 * B90 §5: removal is one action covering both the logos and the demo
		 * showcase, so it reports as one outcome rather than two unexplained
		 * numbers.
		 *
		 * F-3: the sentence is selected by *what was actually removed*, not
		 * pluralised on a logo count that may legitimately be zero. Building it
		 * from $logos alone produced "0 demo logos and the demo showcase
		 * removed." whenever the logos had already been graduated or deleted —
		 * grammatically valid, but it reads as a defect rather than a
		 * confirmation, and the all-zero case reported a removal that never
		 * happened. Both zero cases now get their own string; the two non-zero
		 * strings are unchanged so existing translations stay valid.
		 *
		 * NOTE, recorded so it is not rediscovered from scratch: this success
		 * message is currently never shown to the user. dashboard.js's success
		 * branch removes the whole card and never reads response.data.message —
		 * that value is read only on the failure branch, which uses different
		 * strings entirely. The message is kept correct regardless, because it
		 * still ships in the JSON payload and in the .pot for translators.
		 * Whether it should be displayed at all is a separate UX decision,
		 * deliberately not taken here.
		 */
		if ( $logos > 0 && $showcase > 0 ) {
			$message = sprintf(
				/* translators: %d: number of demo logo items removed. */
				_n(
					'%d demo logo and the demo showcase removed.',
					'%d demo logos and the demo showcase removed.',
					$logos,
					'logo-slider-wp'
				),
				$logos
			);
		} elseif ( $logos > 0 ) {
			$message = sprintf(
				/* translators: %d: number of demo logo items removed. */
				_n( '%d demo logo removed.', '%d demo logos removed.', $logos, 'logo-slider-wp' ),
				$logos
			);
		} elseif ( $showcase > 0 ) {
			$message = __( 'Demo showcase removed.', 'logo-slider-wp' );
		} else {
			$message = __( 'No demo content left to remove.', 'logo-slider-wp' );
		}

		wp_send_json_success(
			array(
				'removed'          => $logos,
				'removed_showcase' => $showcase,
				'message'          => $message,
			)
		);
	}

	/**
	 * Delete every still-tagged demo logo item and the attachments they own.
	 *
	 * The `_lgx_demo_logo` meta query is the entire safety guarantee: a post
	 * the user has edited no longer carries it (see maybe_graduate_demo_logo())
	 * and is therefore invisible here. Nothing without the tag is ever touched.
	 *
	 * @return int Number of demo logo items removed.
	 */
	private function remove_demo_content() {
		$demo_posts   = self::get_demo_logo_ids( -1 );
		$showcase_id  = self::get_demo_showcase_id();

		/*
		 * B90 §7.1 (finding F2 of the Phase 2a review). This early return
		 * previously tested `empty( $demo_posts )` alone. Once the demo
		 * showcase existed, that was a real bug: a user who had graduated or
		 * deleted all six demo logos while the showcase was still tagged would
		 * hit this return before any showcase-removal step below could run,
		 * making the showcase permanently unreachable through this handler.
		 * The condition must consider both kinds of tagged item.
		 */
		if ( empty( $demo_posts ) && $showcase_id <= 0 ) {
			return array(
				'logos'    => 0,
				'showcase' => 0,
			);
		}

		$removed = 0;

		foreach ( $demo_posts as $post_id ) {
			$post_id = (int) $post_id;

			// Re-read the tag defensively rather than trusting the query.
			if ( '' === (string) get_post_meta( $post_id, self::DEMO_LOGO_META, true ) ) {
				continue;
			}

			// Delete the attachment only when it is still one of ours.
			$thumbnail_id = (int) get_post_thumbnail_id( $post_id );
			if ( $thumbnail_id > 0 && '' !== (string) get_post_meta( $thumbnail_id, self::DEMO_ATTACHMENT_META, true ) ) {
				wp_delete_attachment( $thumbnail_id, true );
			}

			// Force delete — skip the trash entirely.
			if ( wp_delete_post( $post_id, true ) ) {
				$removed++;
			}
		}

		/*
		 * The seeded showcase (B90 §7). Same rule as the logos above: it is
		 * only ever touched while it still carries its tag, so a graduated
		 * showcase — one the user edited — is invisible here and survives.
		 */
		$showcase_removed = 0;
		if ( $showcase_id > 0
			&& '' !== (string) get_post_meta( $showcase_id, self::DEMO_SHOWCASE_META, true )
			&& wp_delete_post( $showcase_id, true )
		) {
			$showcase_removed = 1;
		}

		return array(
			'logos'    => $removed,
			'showcase' => $showcase_removed,
		);
	}

	/* ---------------------------------------------------------------------
	 * Shared helper
	 * ------------------------------------------------------------------- */

	/**
	 * Whether this site already has logo items or showcases of its own.
	 *
	 * Both post types are checked because seeding is gated on BOTH being empty
	 * (B87 D2). Query shape matches the Activator's existing style.
	 *
	 * @return bool
	 */
	public static function site_has_existing_content() {
		$logos = get_posts(
			array(
				'post_type'      => 'logosliderwp',
				'post_status'    => self::EXISTING_CONTENT_STATUSES,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		if ( ! empty( $logos ) ) {
			return true;
		}

		$showcases = get_posts(
			array(
				'post_type'      => 'lgx_lsp_shortcodes',
				'post_status'    => self::EXISTING_CONTENT_STATUSES,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		return ! empty( $showcases );
	}

	/**
	 * Whether any still-untouched seeded demo content remains on this site.
	 *
	 * Static so the Dashboard module can ask without holding an instance —
	 * this module is instantiated by the orchestrator, not by
	 * Logo_Slider_WP_Admin, so there is no getter to reach it through.
	 *
	 * B90 §7.2 (finding F2/F3 of the Phase 2a review): this deliberately checks
	 * a tagged **showcase** as well as tagged logos. The Dashboard gates the
	 * entire "Remove Demo Content" card on this result
	 * (class-logo-slider-wp-dashboard.php). Checking logos alone meant that once
	 * the last demo logo was graduated or deleted, the card vanished while a
	 * still-tagged demo showcase remained on the site — leaving no UI path to
	 * remove it. Either kind of tagged item must keep the card visible.
	 *
	 * @return bool
	 */
	public static function has_demo_content() {
		if ( ! empty( self::get_demo_logo_ids( 1 ) ) ) {
			return true;
		}

		return self::get_demo_showcase_id() > 0;
	}

	/**
	 * IDs of still-tagged seeded demo logo items.
	 *
	 * @param int $limit Maximum number to return; -1 for all.
	 * @return int[]
	 */
	private static function get_demo_logo_ids( $limit = -1 ) {
		return get_posts(
			array(
				'post_type'      => 'logosliderwp',
				'post_status'    => self::EXISTING_CONTENT_STATUSES,
				'posts_per_page' => (int) $limit,
				'fields'         => 'ids',
				'meta_key'       => self::DEMO_LOGO_META,
				'no_found_rows'  => true,
			)
		);
	}

	/**
	 * ID of the still-tagged seeded demo showcase, if one exists.
	 *
	 * Returns 0 when the showcase was never seeded, was already removed, or has
	 * been graduated (tag stripped by maybe_graduate_demo_showcase()).
	 *
	 * @return int
	 */
	private static function get_demo_showcase_id() {
		$found = get_posts(
			array(
				'post_type'      => 'lgx_lsp_shortcodes',
				'post_status'    => self::EXISTING_CONTENT_STATUSES,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::DEMO_SHOWCASE_META,
				'no_found_rows'  => true,
			)
		);

		return empty( $found ) ? 0 : (int) $found[0];
	}
}
}
