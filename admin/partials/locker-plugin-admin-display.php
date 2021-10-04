<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Locker_Plugin
 * @subpackage Locker_Plugin/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h3 class="heading3">Locker Plugin Settings</h3>
<hr>

<div id="locker-wrapper">
    <form action="options.php" method="post" style="width: 90%">
        <table class="widefat">
            <?php
            settings_fields( 'locker-plugin-sections' );
            do_settings_fields( 'locker-plugin-page', 'locker-plugin-sections' );
            ?>
        </table>

        <?php echo get_submit_button( 'Save changes', 'primary', 'locker-settings-save' ) ?>
    </form>
</div>