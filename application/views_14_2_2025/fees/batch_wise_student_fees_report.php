<?php
$widget = (is_superadmin_loggedin() ? 4 : 6);
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
                <div class="row">
                    <?php if (is_superadmin_loggedin()) : ?>
                        <div class="col-md-3">
                            <div class="form-group mb-sm">
                                <label class="control-label"><?= translate('branch') ?> <span class="required">*</span></label>
                                <?php
                                $arrayBranch = $this->app_lib->getSelectList('branch');
                                echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label">Batch <span class="required">*</span></label>
                            <?php
                            $arrayClass = $this->app_lib->getClass($branch_id);
                            echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getStudentByBatch(this.value)'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label">Student</label>
                            <?php
                            $arrayClass = $this->app_lib->getClass($branch_id);
                            echo form_dropdown("student_id", '', set_value('student_id'), "class='form-control' data-plugin-selectTwo id='student_id_show' 
								 data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
                            ?>
                        </div>
                    </div>

                    <div style="display: none;" class="col-md-<?php echo $widget; ?> mb-sm">
                        <div class="form-group">
                            <label class="control-label"><?= translate('section') ?> <span class="required">*</span></label>
                            <?php
                            $arraySection = $this->app_lib->getSections(set_value('class_id'), false);
                            echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' required
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
                            ?>
                        </div>
                    </div>
                </div>
                <script>
                    function getStudentByBatch(class_id) {
                        alert("Student Fees Report");
                        var branch_id = $('#branch_id').val();
                        var student_id = $('#student_id').val();
                        $.ajax({
                            url: base_url + 'ajax/getStudentByBatch',
                            type: 'POST',
                            data: {
                                class_id: class_id,
                                branch_id: branch_id,
                                student_id: student_id,
                            },
                            success: function(response) {
                                console.log("response fdfdf", response);
                                $('#student_id_show').html(response);
                            }
                        });
                    }
                </script>
                <div class="row mb-sm">
                    <div style="display: none;" class="col-md-4 mb-sm">
                        <div class="form-group">
                            <label class="control-label"><?= translate('fees_type') ?></label>
                            <select data-plugin-selectTwo class="form-control" name="fees_type" id="feesType">

                            </select>
                        </div>
                    </div>


                </div>
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
                        <table class="table table-bordered tbr-top">
                            <thead>
                                <tr>
                                    <th><?= translate('student') ?></th>
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
                                $totalamount = 0;
                                $totaldiscount = 0;
                                $total_due = 0;

                                foreach ($invoicelist as $row) :
                                    // Update totals
                                    $totalamount += $row['total_amount'];
                                    $totaldiscount += $row['course_price_discount'];
                                    $due = $row['adjusted_course_price'] - $row['total_amount'];
                                    $total_due += $due;
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['first_name']); ?></td>
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
                                            <?= !empty($row['total_amount'])
                                                ? htmlspecialchars($currency_symbol . number_format($row['total_amount'], 2, '.', ''))
                                                : ''; ?>
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
                                    <th colspan="2"></th>
                                    <th><?= translate('Total') ?></th>
                                    <th><?= htmlspecialchars($currency_symbol . number_format($totaldiscount, 2, '.', '')); ?></th>
                                    <th></th>
                                    <th><?= htmlspecialchars($currency_symbol . number_format($totalamount, 2, '.', '')); ?></th>
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
        $('#rowGroup').DataTable({
            dom: '<"row"<"col-sm-6 mb-xs"B><"col-sm-6"f>><"table-responsive"t>p',
            autoWidth: false,
            pageLength: 25,
            order: [
                [0, 'asc']
            ],
            rowGroup: {
                dataSrc: 0
            },
            columnDefs: [{
                targets: [0],
                visible: false
            }],
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


        var branchID = "<?= $branch_id ?>";
        var typeID = "<?= set_value('fees_type') ?>";
        var classID = "<?= set_value('class_id') ?>";
        var sectionID = "<?= set_value('section_id') ?>";
        getTypeByBranch(branchID, typeID);
        getStudentByClass(branchID, classID, sectionID);

        $('#branch_id').on('change', function() {
            var branchID = $(this).val();
            getClassByBranch(branchID);
            getTypeByBranch(branchID);

        });

        $('#section_id').on('change', function() {
            var section_id = $(this).val();
            var class_id = $('#class_id').val();
            var branch_id = ($("#branch_id").length ? $('#branch_id').val() : "");
            getStudentByClass(branch_id, class_id, section_id);
        });

        function getStudentByClass(branch_id, class_id, section_id) {
            var student_id = "<?= set_value('student_id') ?>";
            $.ajax({
                url: base_url + 'ajax/getStudentByClass',
                type: 'POST',
                data: {
                    branch_id: branch_id,
                    class_id: class_id,
                    section_id: section_id,
                    student_id: student_id
                },
                success: function(data) {
                    $('#student_id').html(data);
                }
            });
        }

        function getTypeByBranch(branchID, typeID) {
            $.ajax({
                url: base_url + 'fees/getTypeByBranch',
                type: 'POST',
                data: {
                    'branch_id': branchID,
                    'type_id': typeID
                },
                success: function(data) {
                    $('#feesType').html(data);
                }
            });
        }
    });
</script>