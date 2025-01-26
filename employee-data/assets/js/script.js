jQuery(document).ready(function ($) {
    $('#calculate-average-btn').on('click', function () {
        $('.loader-wrapper').css('display', 'flex');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'calculate_average_salaries_ajax'
            },
            success: function (response) {
                if (response.success) {
                    $('.loader-wrapper').hide();
                    $('#salary-average-result').html('<p><strong>Average Salary:</strong> ' + response.data.average_salary + '</p>');
                } else {
                    $('#salary-average-result').html('<p>An error occurred.</p>');
                }
            },
            error: function () {
                $('#salary-average-result').html('<p>An error occurred.</p>');
            }
        });
    });
});