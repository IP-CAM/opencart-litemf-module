<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-information" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#default-setting">Default Settings</a></li>
                    <li><a data-toggle="tab" href="#unsend-orders">Unsend orders</a></li>
                    <li><a data-toggle="tab" href="#send-orders">Send orders</a></li>
                </ul>
                <div class="tab-content">
                    <div id="default-setting" class="tab-pane fade in active">
                        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-information" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-get"><?php echo $entry_get_order; ?></label>
                                <div class="col-sm-10">
                                    <select name="litemf_get_order">
                                        <?php foreach ($order_list as $order) { ?>
                                            <option value="<?php echo $order['order_status_id'] ?>" <?php if ($litemf_get_order == $order['order_status_id']) { echo 'selected'; }?>>
                                                <?php echo $order['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($error_litemf_get_order) { ?>
                                        <div class="text-danger"><?php echo $error_litemf_get_order; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-set"><?php echo $entry_set_order; ?></label>
                                <div class="col-sm-10">
                                    <select name="litemf_set_order">
                                        <?php foreach ($order_list as $order) { ?>
                                            <option value="<?php echo $order['order_status_id'] ?>" <?php if ($litemf_set_order == $order['order_status_id']) { echo 'selected'; }?>>
                                                <?php echo $order['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($error_litemf_set_order) { ?>
                                        <div class="text-danger"><?php echo $error_litemf_set_order; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-set"><?php echo $entry_delivery_order; ?></label>
                                <div class="col-sm-10">
                                    <select name="litemf_delivery_order">
                                        <?php foreach ($order_list as $order) { ?>
                                            <option value="<?php echo $order['order_status_id'] ?>" <?php if ($litemf_delivery_order == $order['order_status_id']) { echo 'selected'; }?>>
                                                <?php echo $order['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($error_litemf_delivery_order) { ?>
                                        <div class="text-danger"><?php echo $error_litemf_delivery_order; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-api"><?php echo $entry_api; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="litemf_api_key" value="<?php echo $litemf_api_key; ?>" placeholder="<?php echo $entry_api; ?>" id="input-api" class="form-control" />
                                    <?php if ($error_litemf_api_key) { ?>
                                        <div class="text-danger"><?php echo $error_litemf_api_key; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-time"><?php echo $entry_time; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="litemf_timer" value="<?php echo $litemf_timer; ?>" placeholder="<?php echo $entry_time; ?>" id="input-name" class="form-control" />
                                    <?php if ($error_litemf_timer) { ?>
                                        <div class="text-danger"><?php echo $error_litemf_timer; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="send"><?php echo $button_save; ?></button>
                            </div>
                        </form>
                    </div>
                    <div id="unsend-orders" class="tab-pane fade">
                        <h3>Unsend order list</h3>
                        <p>In this section you can see the parcels that have not yet been sent to the system LiteMf
                            You can also edit the order and send it by pressing the send button.</p>
                        <table class="table table-hover">
                            <thead>
                                <th>Id</th>
                                <th>Opencart order Id</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Isset passport</th>
                                <th>Isset tracking</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            <?php foreach($litemf_order_list as $order) { ?>
                                <tr>
                                    <td><?php echo $order['litemf_orders']; ?></td>
                                    <td><a href="/admin/index.php?route=sale/order/info&token=<?php echo $token; ?>&order_id=<?php echo $order['order_id']; ?>"><?php echo $order['order_id']; ?></a></td>
                                    <td><?php echo $order['city']; ?></td>
                                    <td><?php echo $order['status']; ?></td>
                                    <?php if(empty($order['first_name']) || empty($order['last_name']) || empty($order['middle_name']) || empty($order['street']) || empty($order['house']) || empty($order['region']) || empty($order['zip_code'])  || empty($order['phone']) || empty($order['series']) || empty($order['number']) || empty($order['issue_date']) || empty($order['issued_by'])) { ?>
                                        <td>Data is not completely filled!</td>
                                    <?php } else { ?>
                                        <td>The data are filled completely.</td>
                                    <?php } ?>
                                    <?php if(is_null($order['tracking'])) { ?>
                                        <td>Track number is empty</td>
                                    <?php } else { ?>
                                        <td><?php echo $order['tracking']; ?></td>
                                    <?php } ?>
                                    <td>
                                        <button class="edit" data-order-id="<?php echo $order['litemf_orders']; ?>">Edit</button>
                                        <button class="send-package" data-order-id="<?php echo $order['litemf_orders']; ?>">Send package</button>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="send-orders" class="tab-pane fade">
                        <h3>Send order list</h3>
                        <p>In this section you can see the parcels that have been sent to the system LiteMf</p>
                        <table class="table table-hover">
                            <thead>
                            <th>Id</th>
                            <th>Opencart order Id</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Isset passport</th>
                            <th>Isset tracking</th>
                            <th>Action</th>
                            </thead>
                            <tbody>
                            <?php foreach($litemf_order_list_send as $order) { ?>
                            <tr>
                                <td><?php echo $order['litemf_orders']; ?></td>
                                <td><a href="/admin/index.php?route=sale/order/info&token=<?php echo $token; ?>&order_id=<?php echo $order['order_id']; ?>"><?php echo $order['order_id']; ?></a></td>
                                <td><?php echo $order['city']; ?></td>
                                <td><?php echo $order['status']; ?></td>
                                <?php if(empty($order['first_name']) || empty($order['last_name']) || empty($order['middle_name']) || empty($order['street']) || empty($order['house']) || empty($order['region']) || empty($order['zip_code'])  || empty($order['phone']) || empty($order['series']) || empty($order['number']) || empty($order['issue_date']) || empty($order['issued_by'])) { ?>
                                <td>Data is not completely filled!</td>
                                <?php } else { ?>
                                <td>The data are filled completely.</td>
                                <?php } ?>
                                <?php if(is_null($order['tracking'])) { ?>
                                <td>Track number is empty</td>
                                <?php } else { ?>
                                <td><?php echo $order['tracking']; ?></td>
                                <?php } ?>
                                <td><button>Check status</button></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                <form id="order-form">
                    <h4> Litemf passport form </h4>
                    <div class="col-sm-6">
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-first_name">First name</label>
                            <input type="text" name="litemf[first_name]" placeholder="First name" id="input-litemf-first_name" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-payment-lastname">Last name</label>
                            <input type="text" name="litemf[last_name]" placeholder="Last name" id="input-litemf-last_name" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-middle_name">Middle name</label>
                            <input type="text" name="litemf[middle_name]" placeholder="Middle name" id="input-litemf-middle_name" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-phone">Phone</label>
                            <input type="text" name="litemf[phone]" placeholder="Phone" id="input-litemf-phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="input-litemf-zip_code">Zop code</label>
                            <input type="text" name="litemf[zip_code]" placeholder="Zop code" id="input-litemf-zip_code" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="input-litemf-region">Region</label>
                            <input type="text" name="litemf[region]" placeholder="Region" id="input-litemf-region" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="input-litemf-region">City</label>
                            <input type="text" name="litemf[city]" placeholder="City" id="input-litemf-city" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-street">Street</label>
                            <input type="text" name="litemf[street]" placeholder="Street" id="input-litemf-street" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-house">House</label>
                            <input type="text" name="litemf[house]" placeholder="House" id="input-litemf-house" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-series">Series</label>
                            <input type="text" name="litemf[series]" placeholder="Series" id="input-litemf-series" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-number">Number</label>
                            <input type="text" name="litemf[number]" placeholder="Number" id="input-litemf-number" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-issue_date">Issue date</label>
                            <input type="text" name="litemf[issue_date]" placeholder="Issue date" id="input-litemf-issue-date" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-issued_by">Issued by</label>
                            <input type="text" name="litemf[issued_by]" placeholder="Issued by" id="input-litemf-issued_by" class="form-control">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="input-litemf-issued_by">Tracking</label>
                            <input type="text" name="litemf[tracking]" placeholder="Issued by" id="input-litemf-tracking" class="form-control">
                        </div>
                        <input type="hidden" name="litemf[order_id]" placeholder="Issued by" id="input-litemf-order-id" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-order">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('.edit').click(function(){
        var orderId = $(this).data('order-id');
        $.ajax({
            url: 'index.php?route=module/litemf/getOrderDetails&token=<?php echo $token; ?>&order_id=' + orderId,
            dataType: 'json',
            success: function (json) {
                console.log(json.city);
                $.each(json, function( index, value ) {
                    if (index != 'id' && index != 'litemf_orders') {
                        $('[name="litemf[' + index + ']"]').val(value);
                    }
                });
                $('#input-litemf-order-id').val(orderId);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
        $('#myModal').modal('show')
    });
    $('#save-order').click(function(){
        var formData   = $('#order-form').serialize();
        $.ajax({
            url: 'index.php?route=module/litemf/saveOrderDetails&token=<?php echo $token; ?>',
            dataType: 'json',
            data: formData,
            success: function (json) {
                $('#myModal').modal('hide')
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    $('.send-package').click(function(){
        var orderId = $(this).data('order-id');
        $.ajax({
            url: 'index.php?route=module/litemf/sendPackage&token=<?php echo $token; ?>&order_id=' + orderId,
            dataType: 'json',
            success: function (json) {
                location.reload();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    })
</script>
<?php echo $footer; ?>