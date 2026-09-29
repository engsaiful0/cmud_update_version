<?php

$currency_symbol = $global_config['currency_symbol'];
?>
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h4 class="panel-title"><?= translate('select_ground') ?></h4>
            </header>
            <?php echo form_open($this->uri->uri_string(), array('class' => 'validate')); ?>
            <div class="panel-body">
                <?php if (!empty($report_error)): ?>
                    <div class="alert alert-danger"><?= html_escape($report_error) ?></div>
                <?php endif; ?>
                <div class="row">
                    <?php if (is_superadmin_loggedin()) : ?>
                        <div class="col-md-3">
                            <div class="form-group mb-sm">
                                <label class="control-label"><?= translate('branch') ?> <span class="required">*</span></label>
                                <?php
                                $arrayBranch = $this->app_lib->getSelectList('branch');
                                echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='0'");
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label">Batch <span class="required">*</span></label>
                            <?php
                            $arrayClass = $this->app_lib->getClass($branch_id);
                            echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' required
								data-width='100%' data-minimum-results-for-search='0'");
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label">Student</label>
                            <?php
                            echo form_dropdown("student_id", array('' => 'Select batch first'), set_value('student_id'), "class='form-control' id='student_id_show'
								 data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='0' ");
                            ?>
                        </div>
                    </div>

                </div>
                <p class="help-block">Leave Student unselected to include all students in the batch.</p>
            </div>
            <footer class="panel-footer">
                <div class="row">
                    <div class="col-md-offset-10 col-md-2">
                        <button type="submit" name="search" value="1" class="btn btn-default btn-block"> <i class="fas fa-filter"></i> <?= translate('filter') ?></button>
                    </div>
                </div>
            </footer>
            <?php echo form_close(); ?>
        </section>
        <?php if (isset($invoicelist)) : ?>
            <?php if (empty($invoicelist)): ?>
                <div class="alert alert-info">No students found for the selected batch and student.</div>
            <?php endif; ?>
            <style type="text/css">
                tr.group {
                    font-weight: 600 !important;
                }

                tr.group {
                    color: #000;
                    background: #f5f5f5 !important;
                }

                html.dark tr.group {
                    color: #fff;
                    background: #383838 !important;
                }

                tr.odd td:first-child,
                tr.even td:first-child {
                    padding-left: 18px;
                }
            </style>
            <section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations']; ?>" data-appear-animation-delay="100">
                <header class="panel-heading">
                    <h4 class="panel-title"><i class="fas fa-list-ol"></i> <?= translate('student_fees_reports'); ?></h4>
                </header>
                <div class="panel-body">
                    <div class="mb-md mt-md">
                        <div class="export_title"><?= translate('student_fees_reports') ?></div>
                        <table class="table table-bordered tbr-top" id="rowGroup">
                            <thead>
                                <tr>
                                    <th><?= translate('student') ?></th>
                                    <th><?= translate('Student ID') ?></th>
                                    <th><?= translate('register_no') ?></th>
                                    <th><?= translate('Course Fee') ?></th>
                                    <th><?= translate('Discount') ?></th>
                                    <th><?= translate('Adjusted Amount') ?></th>
                                    <th><?= translate('paid') ?></th>
                                    <th><?= translate('due') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Ensure these variables are initialized
                                $count = 1;
                                $total_course_price = 0;
                                $total_adjusted = 0;
                                $totaldiscount = 0;
                                $total_due = 0;
                                $grand_paid=0;

                                foreach ($invoicelist as $row) :
                                    $deposit_through_office_accounting = $this->fees_model->getStudentFeeDepositThroughOfficeAccount($row['roll']);
                                    $deposit_through_office_accounting_value = (float) ($deposit_through_office_accounting['total_amount'] ?? 0);

                                    // Update totals
                                    $total_course_price += $row['course_price'];
                                    $total_adjusted += $row['adjusted_course_price'];
                                    $totaldiscount += $row['course_price_discount'];
                                    $due = $row['adjusted_course_price'] - $row['total_amount']-$deposit_through_office_accounting_value;
                                    $total_due += $due;
                                    $grand_paid+=$row['total_amount']+$deposit_through_office_accounting_value;

                                
                                ?>
                                    <tr>
                                        <td><?= html_escape(trim($row['first_name'] . ' ' . $row['last_name'])); ?></td>
                                        <td><?= htmlspecialchars($row['roll']); ?></td>
                                        <td><?= htmlspecialchars($row['register_no']); ?></td>
                                        <td>
                                            <?= !empty($row['course_price']) ? htmlspecialchars($currency_symbol . number_format($row['course_price'], 2, '.', '')) : ''; ?>
                                        </td>
                                        <td>
                                            <?= !empty($row['course_price_discount'])
                                                ? htmlspecialchars($currency_symbol . number_format($row['course_price_discount'], 2, '.', ''))
                                                : ''; ?>
                                        </td>
                                        <td>
                                            <?= !empty($row['adjusted_course_price'])
                                                ? htmlspecialchars($currency_symbol . number_format($row['adjusted_course_price'], 2, '.', ''))
                                                : ''; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($currency_symbol . number_format($row['total_amount']+$deposit_through_office_accounting_value, 2, '.', '')); ?>
                                        </td>
                                        <td>
                                            <?= isset($due) && $due !== ''
                                                ? htmlspecialchars($currency_symbol . number_format($due, 2, '.', ''))
                                                : ''; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th><?= translate('Total') ?></th>
                                    <th><?= htmlspecialchars($currency_symbol . number_format($total_course_price, 2, '.', '')); ?></th>
                                    <th><?= htmlspecialchars($currency_symbol . number_format($totaldiscount, 2, '.', '')); ?></th>
                                    <th><?= htmlspecialchars($currency_symbol . number_format($total_adjusted, 2, '.', '')); ?></th>
                                    <th><?= htmlspecialchars($currency_symbol . number_format($grand_paid, 2, '.', '')); ?></th>
                                    <th><?= htmlspecialchars($currency_symbol . number_format($total_due, 2, '.', '')); ?></th>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#class_id').select2({
            theme: 'bootstrap',
            width: '100%',
            minimumResultsForSearch: 0
        });
        if ($('#rowGroup').length) {
        $('#rowGroup').DataTable({
            dom: '<"row"<"col-sm-6 mb-xs"B><"col-sm-6"f>><"table-responsive"t>p',
            autoWidth: false,
            pageLength: 25,
            order: [
                [0, 'asc']
            ],
            "buttons": [{
                    extend: 'copyHtml5',
                    text: '<i class="far fa-copy"></i>',
                    titleAttr: 'Copy',
                    title: $('.export_title').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i>',
                    titleAttr: 'Excel',
                    title: $('.export_title').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-alt"></i>',
                    titleAttr: 'CSV',
                    title: $('.export_title').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf"></i>',
                    titleAttr: 'PDF',
                    title: $('.export_title').html(),
                    footer: true,
                    customize: function(win) {
                        win.styles.tableHeader.fontSize = 10;
                        win.styles.tableFooter.fontSize = 10;
                        win.styles.tableHeader.alignment = 'left';
                    },
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.export_title').html(),
                    customize: function(win) {
                        $(win.document.body)
                            .css('font-size', '9pt');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');

                        $(win.document.body).find('h1')
                            .css('font-size', '14pt');
                    },
                    footer: true,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.export_title').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
        });


        }
        var studentRequest, batchRequest;
		var $filter = $('button[name="search"]');
		loadReportStudents();

		$('#branch_id').on('change', function() {
			if (studentRequest) studentRequest.abort();
			if (batchRequest) batchRequest.abort();
			$('#student_id_show').empty().trigger('change');
			$('#class_id').empty().prop('disabled', true).trigger('change.select2');
			$filter.prop('disabled', true);
			batchRequest = $.ajax({
				url: base_url + 'ajax/getClassByBranch',
				type: 'POST',
				data: {branch_id: $(this).val()},
				success: function(data) {
					$('#class_id').html(data).prop('disabled', false).trigger('change');
				},
				error: function(xhr, status) {
					if (status !== 'abort') {
						$('#class_id').append($('<option>', {value: '', text: 'Unable to load batches. Select the branch again.'})).prop('disabled', false).trigger('change.select2');
					}
				}
			});
		});

		$('#class_id').on('change', function() {
			loadReportStudents('');
		});


		function loadReportStudents(studentID) {
			if (studentRequest) studentRequest.abort();
			var $students = $('#student_id_show');
			$students.empty().prop('disabled', true).trigger('change');
			$filter.prop('disabled', true);
			studentRequest = $.ajax({
				url: base_url + 'ajax/getStudentByBatch',
				type: 'POST',
				dataType: 'html',
				data: {
					branch_id: $('#branch_id').length ? $('#branch_id').val() : <?= json_encode($branch_id) ?>,
					class_id: $('#class_id').val(),
					student_id: typeof studentID === 'undefined' ? <?= json_encode(set_value('student_id')) ?> : studentID
				},
				success: function(data) {
					$students.html(data).prop('disabled', false).trigger('change');
					$filter.prop('disabled', false);
				},
				error: function(xhr, status) {
					if (status !== 'abort') {
						$students.empty().append($('<option>', {value: '', text: 'Unable to load students. Please select the batch again.'})).prop('disabled', false).trigger('change');
					}
				}
			});
		}
	});
</script>
