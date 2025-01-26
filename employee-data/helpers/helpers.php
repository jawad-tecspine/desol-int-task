<?php

class Helpers
{
    public function __construct()
    {
        add_action('wp_ajax_calculate_average_salaries_ajax', array($this, 'calculate_average_salaries_ajax'));
    }

    function calculate_average_salaries_ajax()
    {
        // Check for permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized access.', 'employee-data')]);
        }

        // Get all employees
        $args = [
            'post_type' => 'employee',
            'posts_per_page' => -1,
        ];
        $query = new WP_Query($args);

        $total_salary = 0;
        $employee_count = 0;

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $salary = get_post_meta(get_the_ID(), 'employee_salary', true);

                if (is_numeric($salary)) {
                    $total_salary += $salary;
                    $employee_count++;
                }
            }
            wp_reset_postdata();
        }

        if ($employee_count > 0) {
            $average_salary = $total_salary / $employee_count;
            wp_send_json_success(['average_salary' => number_format($average_salary, 2)]);
        } else {
            wp_send_json_error(['message' => __('No employees found.', 'employee-data')]);
        }
    }
}

new Helpers();