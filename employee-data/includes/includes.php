<?php

// Register the 'Employee' post type when the plugin is activated.
function employee_register_post_type()
{
    $labels = [
        'name' => __('Employees', 'employee-data'),
        'singular_name' => __('Employee', 'employee-data'),
        'menu_name' => __('Employees', 'employee-data'),
        'add_new' => __('Add New', 'employee-data'),
        'add_new_item' => __('Add New Employee', 'employee-data'),
        'edit_item' => __('Edit Employee', 'employee-data'),
        'new_item' => __('New Employee', 'employee-data'),
        'view_item' => __('View Employee', 'employee-data'),
        'search_items' => __('Search Employees', 'employee-data'),
        'not_found' => __('No Employees Found', 'employee-data'),
        'not_found_in_trash' => __('No Employees Found in Trash', 'employee-data'),
    ];

    register_post_type('employee', [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-admin-users',
        'supports' => ['title', 'custom-fields'],
        'has_archive' => false,
    ]);
}
add_action('init', 'employee_register_post_type');


// Activate the plugin and flush rewrite rules.
function employee_plugin_activate()
{
    employee_register_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'employee_plugin_activate');


// Deactivate the plugin and flush rewrite rules.
function employee_plugin_deactivate()
{
    unregister_post_type('employee');
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'employee_plugin_deactivate');


// Add custom fields to the "Employee" post type.
function employee_custom_fields()
{
    add_meta_box(
        'employee_details',
        __('Employee Details', 'employee-data'),
        'employee_custom_fields_callback',
        'employee',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'employee_custom_fields');


function employee_custom_fields_callback($post)
{
    $name = get_post_meta($post->ID, 'employee_name', true);
    $position = get_post_meta($post->ID, 'employee_position', true);
    $email = get_post_meta($post->ID, 'employee_email', true);
    $date_of_hire = get_post_meta($post->ID, 'employee_date_of_hire', true);
    $salary = get_post_meta($post->ID, 'employee_salary', true);
    ?>
    <div class="employee-fields-wrapper">
        <div class="form-group">
            <label for="employee_name">Name:</label>
            <input type="text" id="employee_name" name="employee_name" value="<?php echo esc_attr($name); ?>">
        </div>
        <div class="form-group">
            <label for="employee_position">Position:</label>
            <input type="text" id="employee_position" name="employee_position" value="<?php echo esc_attr($position); ?>">
        </div>

        <div class="form-group">
            <label for="employee_email">Email:</label>
            <input type="email" id="employee_email" name="employee_email" value="<?php echo esc_attr($email); ?>">
        </div>

        <div class="form-group">
            <label for="employee_date_of_hire">Date of Hire:</label>
            <input type="date" id="employee_date_of_hire" name="employee_date_of_hire"
                value="<?php echo esc_attr($date_of_hire); ?>">
        </div>

        <div class="form-group">
            <label for="employee_salary">Salary:</label>
            <input type="number" id="employee_salary" name="employee_salary" value="<?php echo esc_attr($salary); ?>">
        </div>
    </div>
    <?php
}


// Save custom fields data.
function employee_save_custom_fields($post_id)
{
    if (array_key_exists('employee_name', $_POST)) {
        update_post_meta($post_id, 'employee_name', sanitize_text_field($_POST['employee_name']));
    }
    if (array_key_exists('employee_position', $_POST)) {
        update_post_meta($post_id, 'employee_position', sanitize_text_field($_POST['employee_position']));
    }
    if (array_key_exists('employee_email', $_POST)) {
        update_post_meta($post_id, 'employee_email', sanitize_email($_POST['employee_email']));
    }
    if (array_key_exists('employee_date_of_hire', $_POST)) {
        update_post_meta($post_id, 'employee_date_of_hire', sanitize_text_field($_POST['employee_date_of_hire']));
    }
    if (array_key_exists('employee_salary', $_POST)) {
        update_post_meta($post_id, 'employee_salary', floatval($_POST['employee_salary']));
    }
}
add_action('save_post', 'employee_save_custom_fields');


// Add 'Employee List' sub menu for listing employees
function employee_admin_menu()
{
    add_submenu_page(
        'edit.php?post_type=employee',
        __('Employee List', 'employee-data'),
        __('Employee List', 'employee-data'),
        'manage_options',
        'employee-list',
        'employee_list_page_callback'
    );
}
add_action('admin_menu', 'employee_admin_menu');


// Function to handle CSV export
function employee_export_csv()
{
    if (isset($_GET['export_csv']) && current_user_can('manage_options')) {
        $orderby = isset($_GET['orderby']) && in_array($_GET['orderby'], ['date_of_hire', 'salary']) ? $_GET['orderby'] : 'date_of_hire';
        $order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

        $args = [
            'post_type' => 'employee',
            'posts_per_page' => -1,
            'meta_key' => "employee_{$orderby}",
            'orderby' => ($orderby === 'salary' ? 'meta_value_num' : 'meta_value'),
            'order' => $order,
        ];
        $query = new WP_Query($args);

        // Set CSV headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="employees.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add CSV column headings
        fputcsv($output, ['Name', 'Position', 'Email', 'Date of Hire', 'Salary']);

        // Add employee data rows
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $name = get_post_meta(get_the_ID(), 'employee_name', true);
                $position = get_post_meta(get_the_ID(), 'employee_position', true);
                $email = get_post_meta(get_the_ID(), 'employee_email', true);
                $date_of_hire = get_post_meta(get_the_ID(), 'employee_date_of_hire', true);
                $salary = get_post_meta(get_the_ID(), 'employee_salary', true);
                fputcsv($output, [$name, $position, $email, $date_of_hire, $salary]);
            }
        }

        // Close output stream and exit
        fclose($output);
        exit;
    }
}
add_action('admin_init', 'employee_export_csv');


// Callback for the employee list page
function employee_list_page_callback()
{
    $orderby = isset($_GET['orderby']) && in_array($_GET['orderby'], ['date_of_hire', 'salary']) ? $_GET['orderby'] : 'date_of_hire';
    $order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

    $args = [
        'post_type' => 'employee',
        'posts_per_page' => -1,
        'meta_key' => "employee_{$orderby}",
        'orderby' => ($orderby === 'salary' ? 'meta_value_num' : 'meta_value'),
        'order' => $order,
    ];
    $query = new WP_Query($args);

    echo '<div class="wrap">';
    echo '<h1>' . __('Employee List', 'employee-data') . '</h1>';

    // Export CSV Button
    echo '<a href="' . esc_url(add_query_arg(['export_csv' => 'true'])) . '" class="button button-primary export-button">' . __('Export CSV', 'employee-data') . '</a>';

    echo '<form class="filter-form" method="get">';
    echo '<input type="hidden" name="post_type" value="employee">';
    echo '<input type="hidden" name="page" value="employee-list">';
    echo '<select name="orderby">';
    echo '<option value="date_of_hire"' . selected($orderby, 'date_of_hire', false) . '>' . __('Date of Hire', 'employee-data') . '</option>';
    echo '<option value="salary"' . selected($orderby, 'salary', false) . '>' . __('Salary', 'employee-data') . '</option>';
    echo '</select>';
    echo '<select name="order">';
    echo '<option value="asc"' . selected($order, 'ASC', false) . '>' . __('Ascending', 'employee-data') . '</option>';
    echo '<option value="desc"' . selected($order, 'DESC', false) . '>' . __('Descending', 'employee-data') . '</option>';
    echo '</select>';
    echo '<button type="submit" class="button button-primary">' . __('Filter', 'employee-data') . '</button>';
    echo '</form>';

    echo '<table class="widefat fixed" cellspacing="0">';
    echo '<thead><tr>';
    echo '<th>' . __('Name', 'employee-data') . '</th>';
    echo '<th>' . __('Position', 'employee-data') . '</th>';
    echo '<th>' . __('Email', 'employee-data') . '</th>';
    echo '<th>' . __('Date of Hire', 'employee-data') . '</th>';
    echo '<th>' . __('Salary', 'employee-data') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $name = get_post_meta(get_the_ID(), 'employee_name', true);
            $position = get_post_meta(get_the_ID(), 'employee_position', true);
            $email = get_post_meta(get_the_ID(), 'employee_email', true);
            $date_of_hire = get_post_meta(get_the_ID(), 'employee_date_of_hire', true);
            $salary = get_post_meta(get_the_ID(), 'employee_salary', true);
            echo '<tr>';
            echo '<td>' . esc_html(text: $name) . '</td>';
            echo '<td>' . esc_html($position) . '</td>';
            echo '<td>' . esc_html($email) . '</td>';
            echo '<td>' . esc_html($date_of_hire) . '</td>';
            echo '<td>' . esc_html($salary) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">' . __('No employees found.', 'employee-data') . '</td></tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}


// creating 'Salary Average' sub menu for calculating salary average
function employee_salary_average_menu()
{
    add_submenu_page(
        'edit.php?post_type=employee',
        __('Salary Average', 'employee-data'),
        __('Salary Average', 'employee-data'),
        'manage_options',
        'salary-average',
        'salary_average_page_callback'
    );
}
add_action('admin_menu', 'employee_salary_average_menu');


// Callback function for 'Salary Average' sub menu
function salary_average_page_callback()
{
    ?>
    <div class="wrap">
        <h1><?php _e('Salary Average', 'employee-data'); ?></h1>
        <p><?php _e('Calculate the average of salaries', 'employee-data'); ?></p>
        <button id="calculate-average-btn"
            class="button button-primary"><?php _e('Calculate Average', 'employee-data'); ?></button>
        <div id="salary-average-result"></div>
    </div>
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>
    <?php
}