<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://codewithabdessamad.ma
 * @since      1.0.0
 *
 * @package    Wp_Faker
 * @subpackage Wp_Faker/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1>
    <?php echo esc_html(get_admin_page_title()); ?>

</h1>

<form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
    <!-- Add nonce for security and authentication -->
    <?php wp_nonce_field('wp_faker_generate_posts_nonce'); ?>
    <!-- Add action parameter-->
    <input type="hidden" name="action" value="wp_faker_action">
    <?php
    // Display necessary hidden fields for settings
    // settings_fields('wp_faker_options');
    // Output settings sections and their fields
    // (sections are registered for "wp_faker_options", each field is registered to a specific section)
    do_settings_sections('wp_faker_options');
    // Output save settings button
    submit_button(__('Fake it!', 'wp-faker'));


    ?>
</form>
<hr>
<!-- delete all posts -->
<form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
    <!-- Add nonce for security and authentication -->
    <?php wp_nonce_field('wp_faker_delete_posts_nonce'); ?>
    <!-- Add action parameter-->
    <input type="hidden" name="action" value="wp_faker_delete_action">
    <?php
    // Output save settings button
    submit_button(__('Delete fake post', 'wp-faker'), 'delete');
    ?>
</form>
<!-- delete all posts -->