(function ($) {
    $(function () {
        var form = $('#transactions-filters');
        var results = $('#transactions-results');
        var dates = $('#transactions-dates');
        var pending;
        var timer;
        var sequence = 0;

        dates.daterangepicker({
            autoUpdateInput: false,
            opens: 'left',
            locale: {format: 'YYYY/MM/DD', cancelLabel: 'Clear'},
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            }
        }).on('apply.daterangepicker', function (event, picker) {
            dates.val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
            load();
        }).on('cancel.daterangepicker', function () {
            dates.val('');
            load();
        });

        function load(url) {
            clearTimeout(timer);
            var request = ++sequence;
            if (pending) pending.abort();
            $('#transactions-filter-error').hide();
            $('#transactions-filter-loading').show();
            results.attr('aria-busy', 'true');
            pending = $.ajax({
                url: (url || form.attr('action')).split('?')[0],
                type: 'GET',
                data: form.serialize(),
                dataType: 'json'
            }).done(function (data) {
                if (request !== sequence) return;
                results.html(data.html);
                var head = $('#transactions-head');
                var selected = head.val();
                head.empty().append($('<option>', {value: '', text: 'All Voucher Heads'}));
                $.each(data.heads, function (index, item) {
                    head.append($('<option>', {value: item.id, text: item.name}));
                });
                head.val(selected || '');
                results.find('[data-toggle="tooltip"]').tooltip();
            }).fail(function (xhr, status) {
                if (status === 'abort' || request !== sequence) return;
                var message = xhr.responseJSON && xhr.responseJSON.error;
                $('#transactions-filter-error').text(message || 'Unable to load transactions. Please try again.').show();
            }).always(function () {
                if (request !== sequence) return;
                $('#transactions-filter-loading').hide();
                results.attr('aria-busy', 'false');
            });
        }

        form.on('submit', function (event) {
            event.preventDefault();
            load();
        });
        form.on('change', 'select', function () {
            if (this.id === 'transactions-branch' || this.id === 'transactions-type') $('#transactions-head').val('');
            load();
        });
        form.on('input', 'input', function () {
            clearTimeout(timer);
            // Invalidate outstanding responses as soon as the criteria change.
            sequence++;
            if (pending) pending.abort();
            timer = setTimeout(load, 350);
        });
        dates.on('change', function () { load(); });
        $('#transactions-reset').on('click', function () {
            form.find('input, select').val('');
            load();
        });
        results.on('click', '.pagination a', function (event) {
            event.preventDefault();
            load($(this).attr('href'));
        });
    });
})(jQuery);
