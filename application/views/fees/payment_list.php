<?php
$widget = (is_superadmin_loggedin() ? 3 : 4);
$currency_symbol = $global_config['currency_symbol'];
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#roll").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: base_url + "student/roll_load",
                    data: {
                        parameter: request.term,
                        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        response(data);
                    }
                });

            },
            select: function(event, ui) {
                $('#roll').val(ui.item.label);
                return false;
            }
        });
    });

    function getStudentInfoByBatch(class_id) {
        var branch_id = $('#branch_id').val();
        
        // alert();
        $.ajax({
            url: base_url + "ajax/getStudentByBatch",
            type: 'POST',
            data: {
                class_id: class_id,
                branch_id: branch_id,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                //alert(response);
                console.log("response", response);
                $('#student_id_show').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching students:", xhr.responseText);
            }
        });
    }

    function getClassInfoByBranch(branchID) {
        if (!branchID) return; // Prevents AJAX call if no branch is selected

        $.ajax({
            url: base_url + "ajax/getClassByBranch",
            type: "POST",
            data: {
                branch_id: branchID,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(data) {
                console.log("Received data:", data);
                $("#class_id").html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching classes:", xhr.responseText);
            }
        });
    }
</script>
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h4 class="panel-title"><?= translate('select_ground') ?></h4>
            </header>
            <?php echo form_open('fees/payment_list', array('class' => 'validate', 'method' => 'get')); ?>
            <div class="panel-body">
                <div class="row mb-sm">
                    <?php if (is_superadmin_loggedin()): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><?= translate('branch') ?> <span class="required">*</span></label>
                                <?php
                                $arrayBranch = $this->app_lib->getSelectList('branch');
                                ?>
                                <select onchange="getClassInfoByBranch(this.value)" class="form-control" id="branch_id" data-plugin-selecttwo name="branch_id">

                                    <?php foreach ($arrayBranch as $key => $value): ?>
                                        <option value="<?= htmlspecialchars($key) ?>" <?= (string) $key === (string) $branch_id ? 'selected' : '' ?>><?= htmlspecialchars($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label"><?= translate('batch') ?> <span class="required">*</span></label>

                            <select onchange='getStudentInfoByBatch(this.value)' class="form-control" id="class_id" data-plugin-selecttwo name="class_id">

                                <option value=""><?= translate('select') ?></option>
                                <?php foreach ($filter_classes as $filter_class): ?>
                                    <option value="<?= html_escape($filter_class['id']) ?>" <?= (string) $filter_class['id'] === (string) $class_id ? 'selected' : '' ?>><?= html_escape($filter_class['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label">Student</label>
                            <select class="form-control" id="student_id_show" data-plugin-selecttwo name="student_id">
                                <option value="">Select Student</option>
                                <?php foreach ($filter_students as $filter_student): ?>
                                    <option value="<?= html_escape($filter_student['id']) ?>" <?= (string) $filter_student['id'] === (string) $student_id ? 'selected' : '' ?>><?= html_escape($filter_student['first_name'] . ' ' . $filter_student['last_name'] . ' ( S.ID : ' . $filter_student['roll'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-sm">
                        <div class="form-group">
                            <label class="control-label">Student ID</label>
                            <input placeholder="Type student ID.." type="text" name="roll" id="roll" class="form-control" value="<?= html_escape($roll) ?>">
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
        <?php if (isset($payments)) : ?>
            <section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations']; ?>" data-appear-animation-delay="100">
                <header class="panel-heading">
                    <h4 class="panel-title"><i class="fas fa-list-ol"></i> <?= translate('fees_payment_history'); ?></h4>
                </header>
                <div class="panel-body">
                    <div class="mb-md mt-md">
                        <div class="table-responsive">
                        <table class="table invoice-items" id="payment-list">
                            <thead>
                                <tr class="h5 text-dark">
                                    <th id="cell-count" class="text-weight-semibold">#</th>


                                    <th id="cell-item" class="text-weight-semibold"><?= translate('date') ?></th>
                                    <th id="cell-item" class="text-weight-semibold hidden-print"><?= translate('student_name') ?></th>
                                    <th id="cell-item" class="text-weight-semibold hidden-print"><?= translate('ID') ?></th>
                                    <th id="cell-desc" class="text-weight-semibold"><?= translate('remarks') ?></th>
                                    <th id="cell-qty" class="text-weight-semibold"><?= translate('method') ?></th>
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('amount') ?></th>
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('discount') ?></th>
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('By Office Accounting') ?></th>

                                    <th id="cell-price" class="text-weight-semibold"><?= translate('paid') ?></th>
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('Total Paid') ?></th>
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $serial = $payment_offset + 1;
                                foreach ($payments as $row) {
                                    $deposit_through_office_accounting = $this->fees_model->getStudentFeeDepositThroughOfficeAccount($row['roll']);
                                    $deposit_through_office_accounting_value = $deposit_through_office_accounting['total_amount'];
                                    $payment_type = $this->db->select('*')->where('id', $row['pay_via'])->get('payment_types')->row();
                                ?>
                                    <tr>
                                        <td><?php echo $serial++ ?></td>


                                        <td><?php echo _d($row['date']); ?></td>
                                        <td><?php echo $row['student_name']; ?></td>
                                        <td><?php echo $row['roll']; ?></td>
                                        <td><?php echo $row['remarks']; ?></td>
                                        <td><?php echo $payment_type->name; ?></td>
                                        <td><?php echo $currency_symbol . ($row['amount'] + $row['discount']); ?></td>
                                        <td><?php echo $currency_symbol . $row['discount']; ?></td>
                                        <td><?php echo $currency_symbol . (float)$deposit_through_office_accounting_value; ?></td>
                                        <td><?php echo $currency_symbol . $row['amount']; ?></td>

                                        <td>
                                            <?php
                                            $amount = is_numeric($row['amount']) ? (float)$row['amount'] : 0;
                                            $deposit = is_numeric($deposit_through_office_accounting_value) ? (float)$deposit_through_office_accounting_value : 0;
                                            echo $currency_symbol . ($amount + $deposit);
                                            ?>
                                        </td>
                                        <td>

                                            <?php if (get_permission('classes', 'is_edit')) : ?>
                                                <!--update link-->
                                                <a href="<?php echo base_url('fees/fee_edit/' . $row['id']); ?>" class="btn btn-default btn-circle icon">
                                                    <i class="fas fa-pen-nib"></i>
                                                </a>
                                            <?php endif;
                                            if (get_permission('colleges', 'is_delete')) : ?>
                                                <!--delete link-->
                                                <?php echo btn_delete('fees/payment_delete/' . $row['id']); ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                                <?php if (empty($payments)): ?>
                                    <tr><td colspan="12" class="text-center">No payments found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        </div>
                        <p class="text-muted">
                            Showing <?= $payment_total > 0 ? $payment_offset + 1 : 0 ?>
                            to <?= $payment_offset + count($payments) ?>
                            of <?= $payment_total ?> payments
                        </p>
                        <?= $pagination_links ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo base_url(); ?>assets/js/autocomplete_js.js"></script>
