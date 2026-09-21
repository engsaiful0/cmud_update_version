<?php
$widget = (is_superadmin_loggedin() ? 3 : 4);
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
                <div class="row mb-sm">
                    <?php if (is_superadmin_loggedin()) : ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?= translate('branch') ?> <span class="required">*</span></label>
                                <?php
                                $arrayBranch = $this->app_lib->getSelectList('branch');
                                echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id' onchange='getClassByBranch(this.value)'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <script>
                        function getStudentByBatch(class_id) {
                            var branch_id = $('#branch_id').val();
                            var student_id = $('#student_id').val();
                            alert("Invoice List Js");
                            $.ajax({
                                url: base_url + 'ajax/getStudentByBatch',
                                type: 'POST',
                                data: {
                                    class_id: class_id,
                                    branch_id: branch_id,
                                    student_id: student_id,
                                },
                                success: function(response) {
                                    //alert(response);
                                    console.log("response", response);
                                    $('#student_id_show').html(response);
                                }
                            });
                        }
                    </script>
                    <div class="col-md-<?php echo $widget; ?> mb-sm">
                        <div class="form-group">
                            <label class="control-label">Batch <span class="required">*</span></label>
                            <?php
                            $arrayClass = $this->app_lib->getClass($branch_id);
                            echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getStudentByBatch(this.value)'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
                            ?>
                        </div>
                    </div>
                    <div class="col-md-<?php echo $widget; ?> mb-sm">
                        <div class="form-group">
                            <label class="control-label">Student</label>
                            <?php
                            $arrayClass = $this->app_lib->getClass($branch_id);
                            echo form_dropdown("student_id", '', set_value('student_id'), "class='form-control' data-plugin-selectTwo id='student_id_show' 
								 data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
                            ?>
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
                        <div class="export_title"><?= translate('fees_payment_history') ?></div>
                        <table class="table invoice-items" id="paymentHistory">
                            <thead>
                                <tr class="h5 text-dark">
                                    <th id="cell-count" class="text-weight-semibold">#</th>


                                    <th id="cell-item" class="text-weight-semibold"><?= translate('date') ?></th>
                                    <th id="cell-item" class="text-weight-semibold hidden-print"><?= translate('student_name') ?></th>
                                    <th id="cell-desc" class="text-weight-semibold"><?= translate('remarks') ?></th>
                                    <th id="cell-qty" class="text-weight-semibold"><?= translate('method') ?></th>
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('amount') ?></th>
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('discount') ?></th>
                                  
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('paid') ?></th>
                                    <th id="cell-price" class="text-weight-semibold"><?= translate('action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $serial = 1;
                                foreach ($payments as $row) {
                                    $payment_type = $this->db->select('*')->where('id', $row['pay_via'])->get('payment_types')->row();
                                ?>
                                    <tr>
                                        <td><?php echo $serial++ ?></td>


                                        <td><?php echo _d($row['date']); ?></td>
                                        <td><?php echo $row['student_name']; ?></td>
                                        <td><?php echo $row['remarks']; ?></td>
                                        <td><?php echo $payment_type->name; ?></td>
                                        <td><?php echo $currency_symbol . ($row['amount'] + $row['discount']); ?></td>
                                        <td><?php echo $currency_symbol . $row['discount']; ?></td>
                               
                                        <td><?php echo $currency_symbol . $row['amount']; ?></td>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>