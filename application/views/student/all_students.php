<?php $widget = (is_superadmin_loggedin() ? 4 : 6); ?>
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
</script>
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h4 class="panel-title"><?= translate('select_ground') ?></h4>
            </header>
            <?php echo form_open('student/all_students', array('class' => 'validate', 'method' => 'get')); ?>
            <?php if (is_superadmin_loggedin() && $branch_id !== ''): ?>
                <input type="hidden" name="branch_id" value="<?= html_escape($branch_id) ?>">
            <?php endif; ?>
            <div class="panel-body">
                <div class="row mb-sm">
                    <?php if (is_superadmin_loggedin()) : ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?= translate('Subject') ?> <span class="required">*</span></label>
                                <select name="subject_id" class="form-control" data-plugin-selectTwo id="subject_holder" data-width="100%">
                                    <option value="">Select Subject</option>
                                    <?php
                                    $subjects = $this->db->select('*')->from('subject')->order_by('name')->get()->result();
                                    foreach ($subjects as $subject) :
                                    ?>
                                        <option value="<?= html_escape($subject->id) ?>" <?= (string) $subject->id === $subject_id ? 'selected' : '' ?>><?= html_escape($subject->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-<?php echo $widget; ?> mb-sm">
                        <div class="form-group">
                            <label class="control-label"><?= translate('batch') ?> <span class="required">*</span></label>
                            <select name="class_id" class="form-control" data-plugin-selectTwo id='class_id' data-width="100%">
                                <option value="">Select Batch</option>
                                <?php

                                $classes = $this->db->select('*')->from('class')->order_by('name')->get()->result();


                                foreach ($classes as $class) :
                                ?>
                                    <option value="<?= html_escape($class->id) ?>" <?= (string) $class->id === $class_id ? 'selected' : '' ?>><?= html_escape($class->name) ?></option>
                                <?php endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">

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

        <?php if (isset($students)) : ?>
            <section class="panel appear-animation" data-appear-animation="<?= $global_config['animations'] ?>" data-appear-animation-delay="100">
                <header class="panel-heading">

                    <h4 class="panel-title"><i class="fas fa-user-graduate"></i> <?php echo translate('student_list'); ?></h4>
                </header>
                <div class="panel-body mb-md">
                    <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>

                                <th class="no-sort"><?= translate('photo') ?></th>
                                <th><?= translate('name') ?></th>
                                <th>Batch</th>
                                <th><?= translate('Form Serial No') ?></th>
                                <th><?= translate('Student ID No ') ?></th>

                                <th><?= translate('subject_name') ?></th>
                                <th>Online/Offline</th>

                                <th><?= translate('Details') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($students as $row) :
                            ?>
                                <tr>

                                    <td class="center"><img src="<?php echo get_image_url('student', $row['photo']); ?>" height="50"></td>
                                    <td><?php echo $row['first_name']; ?></td>
                                    <td><?php echo $row['class_name']; ?></td>
                                    <td><?php echo $row['register_no']; ?></td>
                                    <td><?php echo $row['roll']; ?></td>
                                    <td><?php echo $row['subject_name']; ?></td>
                                    <td><?php echo $row['is_online_offline']; ?></td>



                                    <td class="action">
                                        <!-- quick view -->

                                        <?php if (get_permission('student', 'is_edit')) : ?>
                                            <!-- update link -->
                                            <a href="<?php echo base_url('student/profile/' . $row['id']); ?>" class="btn btn-default btn-circle icon" data-toggle="tooltip" data-original-title="<?= translate('details') ?>">
                                                <i class="far fa-arrow-alt-circle-right"></i>
                                            </a>
                                        <?php endif;
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($students)): ?>
                                <tr><td colspan="8" class="text-center">No students found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                    <div class="pagination-data">
                        <p class="text-muted">
                            Showing <?= $student_total > 0 ? $student_offset + 1 : 0 ?>
                            to <?= $student_offset + count($students) ?>
                            of <?= $student_total ?> students
                        </p>
                        <?= $pagination ?>
                    </div>

                </div>
            </section>
        <?php endif; ?>
    </div>
</div>

<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide" id="quickView">
    <section class="panel">
        <header class="panel-heading">
            <h4 class="panel-title">
                <i class="far fa-user-circle"></i> <?= translate('quick_view') ?>
            </h4>
        </header>
        <div class="panel-body">
            <div class="quick_image">
                <img alt="" class="user-img-circle" id="quick_image" src="<?= base_url('uploads/app_image/defualt.png') ?>" width="120" height="120">
            </div>
            <div class="text-center">
                <h4 class="text-weight-semibold mb-xs" id="quick_full_name"></h4>
                <p><?= translate('student') ?> / <span id="quick_category"></p>
            </div>
            <div class="table-responsive mt-md mb-md">
                <table class="table table-striped table-bordered table-condensed mb-none">
                    <tbody>
                        <tr>
                            <th><?= translate('register_no') ?></th>
                            <td><span id="quick_register_no"></span></td>
                            <th><?= translate('roll') ?></th>
                            <td><span id="quick_roll"></span></td>
                        </tr>
                        <tr>
                            <th><?= translate('admission_date') ?></th>
                            <td><span id="quick_admission_date"></span></td>
                            <th><?= translate('date_of_birth') ?></th>
                            <td><span id="quick_date_of_birth"></span></td>
                        </tr>
                        <tr>
                            <th><?= translate('blood_group') ?></th>
                            <td><span id="quick_blood_group"></span></td>
                            <th><?= translate('religion') ?></th>
                            <td><span id="quick_religion"></span></td>
                        </tr>
                        <tr>
                            <th><?= translate('email') ?></th>
                            <td colspan="3"><span id="quick_email"></span></td>
                        </tr>
                        <tr>
                            <th><?= translate('mobile_no') ?></th>
                            <td><span id="quick_mobile_no"></span></td>
                            <th><?= translate('state') ?></th>
                            <td><span id="quick_state"></span></td>
                        </tr>
                        <tr class="quick-address">
                            <th><?= translate('address') ?></th>
                            <td colspan="3" height="80px;"><span id="quick_address"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <footer class="panel-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-default modal-dismiss"><?= translate('close') ?></button>
                </div>
            </div>
        </footer>
    </section>
</div>
<?php if (get_permission('student', 'is_delete')) : ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#student_bulk_delete').on('click', function() {
                var btn = $(this);
                var arrayID = [];
                $("input[type='checkbox'].cb_bulkdelete").each(function(index) {
                    if (this.checked) {
                        arrayID.push($(this).attr('id'));
                    }
                });
                if (arrayID.length != 0) {
                    swal({
                        title: "<?php echo translate('are_you_sure') ?>",
                        text: "<?php echo translate('delete_this_information') ?>",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn btn-default swal2-btn-default",
                        cancelButtonClass: "btn btn-default swal2-btn-default",
                        confirmButtonText: "<?php echo translate('yes_continue') ?>",
                        cancelButtonText: "<?php echo translate('cancel') ?>",
                        buttonsStyling: false,
                        footer: "<?php echo translate('deleted_note') ?>"
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: base_url + "student/bulk_delete",
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    array_id: arrayID
                                },
                                success: function(data) {
                                    swal({
                                        title: "<?php echo translate('deleted') ?>",
                                        text: data.message,
                                        buttonsStyling: false,
                                        showCloseButton: true,
                                        focusConfirm: false,
                                        confirmButtonClass: "btn btn-default swal2-btn-default",
                                        type: data.status
                                    }).then((result) => {
                                        if (result.value) {
                                            location.reload();
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
<?php endif; ?>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo base_url(); ?>assets/js/autocomplete_js.js"></script>
