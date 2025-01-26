<?php

/**
 * Employee Data Uninstall
 *
 * Uninstalling Employee Data plugin deletes all posts and meta information from WordPress.
 *
 * @package Employee Data
 */

// Exit if accessed directly
defined('WP_UNINSTALL_PLUGIN') || exit;

// Delete all meta information for employees
function delete_employee_meta_data()
{
    global $wpdb;

    $employee_posts = $wpdb->get_col("
        SELECT ID 
        FROM {$wpdb->posts} 
        WHERE post_type = 'employee'
    ");

    if (!empty($employee_posts)) {
        foreach ($employee_posts as $post_id) {
            $wpdb->delete($wpdb->postmeta, ['post_id' => $post_id], ['%d']);
        }

        $wpdb->delete($wpdb->posts, ['post_type' => 'employee'], ['%s']);
    }
}

delete_employee_meta_data();
