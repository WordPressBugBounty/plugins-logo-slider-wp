<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * internal source provider contract (ADR-018 Decision 1), no public hook.
 */
interface Logo_Slider_WP_Source_Provider {
    public function is_available();

    /**
     * Returns the items of the showcase. Two result shapes exist:
     * - a WP_Query object (Logo Library), whose posts the template loops with have_posts() and the_post(); it does not call the_post() and does not call wp_reset_postdata();
     * - a plain array with the keys 'items' and 'reason' (Brand), whose items the template sets one by one as the item context before it includes the card.
     */
    public function get_items( $showcase_settings );
}
