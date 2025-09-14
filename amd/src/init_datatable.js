define(['jquery'], function($) {
    return {
        DTinit: function(selector, options) {
            // Dynamically load the DataTables library
            require(['js/datatables.min.js'], function() {
                // Initialize the DataTable
                $(document).ready(function() {
                    if ($.fn.DataTable) {
                        $(selector).DataTable(options);
                    } else {
                        console.error('DataTables library failed to load.');
                    }
                });
            });
        }
    };
});