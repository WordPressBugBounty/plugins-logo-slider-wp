<?php
if (!defined('WPINC')) {
    die;
}
?>
<div id="postbox-container-1" class="postbox-container">
    <div class="meta-box-sortables">
        <div class="lgx-card" style="margin-bottom: 20px;">
            <div class="lgx-card__header">
                <h3 style="margin:0;">Plugin Info</h3>
            </div>
            <div class="lgx-card__body">
                <p><strong><?php _e('Plugin Name:', 'logo-slider-wp'); ?></strong> <?php echo wp_kses_post( $plugin_data['Title'] ); ?> <?php echo esc_html( $plugin_data['Version'] ); ?></p>
                <p><strong><?php _e('Author:', 'logo-slider-wp'); ?></strong> <?php echo wp_kses_post( $plugin_data['Author'] ); ?></p>
                <p><strong>Website:</strong> <a href="https://logichunt.com" target="_blank">logichunt.com</a></p>
                <p><strong>Email:</strong> <a href="mailto:info@logichunt.com" target="_blank">info@logichunt.com</a></p>
                <p><strong>Twitter:</strong> @<a href="https://twitter.com/logichunt" target="_blank">logichunt</a></p>
                <p><strong>Facebook:</strong> <a href="https://facebook.com/logichunt" target="_blank">LogicHunt</a></p>
            </div>
        </div>
        
        <div class="lgx-card" style="margin-bottom: 20px;">
            <div class="lgx-card__header">
                <h3 style="margin:0;">Help & Supports</h3>
            </div>
            <div class="lgx-card__body">
                <p style="margin-bottom: 15px;"><a class="lgx-btn lgx-btn-secondary" style="width: 100%; text-align: center; display: inline-block; box-sizing: border-box;" href="https://logichunt.com/support/" target="_blank">Get Support</a></p>
                <p style="margin-bottom: 15px;"><a class="lgx-btn lgx-btn-secondary" style="width: 100%; text-align: center; display: inline-block; box-sizing: border-box;" href="https://logichunt.com/" target="_blank">Website</a></p>
                <p style="margin-bottom: 15px;"><a class="lgx-btn lgx-btn--primary" style="width: 100%; text-align: center; display: inline-block; box-sizing: border-box;" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=vaspal%2ekt%40gmail%2ecom&lc=US&item_name=LogicHunt&item_number=wp&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted" target="_blank">Donate Now</a></p>
                <p style="color: #646970; font-size: 13px;">Your contribution always helps us to be more serious and supportive.</p>
            </div>
        </div>
    
        <div class="lgx-card" style="margin-bottom: 20px;">
            <div class="lgx-card__header">
                <h3 style="margin:0;"><?php _e('LogicHunt Networks', 'logo-slider-wp') ?></h3>
            </div>
            <div class="lgx-card__body">
                <p><a target="_blank" href="https://logichunt.com">LogicHunt</a>: Joomla and Worpress Plugin, Extensions, Theme.</p>
	            <p><a target="_blank" href="https://themearth.com">ThemEarth</a>: Themes & Templates.</p>
	            <p><a target="_blank" href="https://logichunt.com/joomlademo/">JoomlaHunt</a>: Joomla Extensions Demo.</p>
            </div>
        </div>

    </div> <!-- .meta-box-sortables -->

</div> <!-- #postbox-container-1 .postbox-container -->