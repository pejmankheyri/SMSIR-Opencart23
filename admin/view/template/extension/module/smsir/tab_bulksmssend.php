<?php

/**
 * Modules Bulk SMS Send tab page
 * 
 * PHP version 5.6.x | 7.x
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

?>
<table id="mail" class="table">
    <tr>
        <td class="col-xs-3">
            <h5><strong><?php echo $smsir_send_to; ?> : </strong></h5>
            <span class="help"><i class="fa fa-info-circle"></i> <?php echo $smsir_choose_users_for_send_sms; ?> </span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <select name="to" class="form-control">
                    <option value="customer_all"><?php echo $smsir_all_customers; ?></option>
                    <option value="customer"><?php echo $smsir_specific_customers; ?></option>
                    <option value="telephones"><?php echo $smsir_specific_mobiles; ?></option>
                    <option value="product"><?php echo $smsir_customers_ordered_specific_products; ?></option>
                    <option value="customer_group"><?php echo $smsir_customer_groups; ?></option>
                    <option value="AllCustomerClub"><?php echo $smsir_all_customer_club; ?></option>
                    <option value="newsletter"><?php echo $smsir_newsletter_users; ?></option>
                    <option value="affiliate_all"><?php echo $smsir_all_affiliates; ?></option>
                    <option value="affiliate"><?php echo $smsir_specific_affiliates; ?></option>
                </select>
            </div>
        </td>
    </tr>
    <tbody id="to-customer-group" class="to">
        <tr>
            <td class="col-xs-3"><h5><strong><?php echo $smsir_customer_groups; ?> : </strong></h5></td>
            <td class="col-xs-9">
                <div class="col-xs-4">
                    <select class="form-control" name="customer_group_id">
                        <?php foreach ($customer_groups as $customer_group) { ?>
                            <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </td>
        </tr>
    </tbody>
    <tbody id="to-telephones" class="to">
        <tr>
            <td class="col-xs-3"><h5><strong><?php echo $smsir_number; ?> : </strong></h5></td>
            <td class="col-xs-9">  
                <div class="col-xs-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="telephones"  />
                        <span class="input-group-btn">
                            <a class="btn btn-primary" onclick="addTelephone()"><?php echo $smsir_add; ?></a>
                        </span>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-xs-3">&nbsp;</td>
            <td class="col-xs-9">
                <div class="col-xs-4">
                    <div id="telephone" class="scrollbox form-control" ></div>
                </div>
            </td>
        </tr>
    </tbody>
    <tbody id="to-customer" class="to">
        <tr>
            <td class="col-xs-3">
                <h5><strong><?php echo $smsir_customer; ?>:</strong></h5>
                <span class="help"><i class="fa fa-info-circle"></i> <?php echo $smsir_auto_complete; ?> </span>
            </td>
            <td class="col-xs-9">
                <div class="col-xs-4">
                    <input type="text" name="customers" value="" class="form-control" />
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-xs-3">&nbsp;</td>
            <td class="col-xs-9">
                <div class="col-xs-4">
                    <div id="customer" class="scrollbox form-control" ></div>
                </div>
            </td>
        </tr>
    </tbody>
    <tbody id="to-affiliate" class="to">
        <tr>
            <td class="col-xs-3">
                <h5><strong><?php echo $smsir_affiliate; ?> : </strong></h5>
                <span class="help"><i class="fa fa-info-circle"></i> <?php echo $smsir_auto_complete; ?> </span>
            </td>
            <td class="col-xs-9">
                <div class="col-xs-4">
                    <input type="text" name="affiliates" value="" class="form-control" />
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-xs-3">&nbsp;</td>
            <td class="col-xs-9">
                <div class="col-xs-4">
                    <div id="affiliate" class="scrollbox form-control"></div>
                </div>
            </td>
        </tr>
    </tbody>
    <tbody id="to-product" class="to">
        <tr>
            <td class="col-xs-3">
                <h5><strong><?php echo $smsir_products; ?> : </strong></h5>
                    <span class="help">
                        <i class="fa fa-info-circle"></i> <?php echo $smsir_products_desc; ?><br/>
                        <i class="fa fa-info-circle"></i> <?php echo $smsir_auto_complete; ?>
                    </span>
                </h5>
            </td>
            <td class="col-xs-9">
                <div class="col-xs-4">
                    <input type="text" name="products" value="" class="form-control" />
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-xs-3">&nbsp;</td>
            <td class="col-xs-9">
                <div class="col-xs-4">
                    <div id="product" class="scrollbox form-control" ></div>
                </div>
            </td>
        </tr>
    </tbody>  
    <tr>
        <td class="col-xs-3">
            <h5><strong><span class="required">*</span> <?php echo $smsir_message; ?> : </strong></h5>
            <span class="help"><i class="fa fa-info-circle"></i> <?php echo $smsir_message_desc; ?> </span>
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <textarea name="message" id="count_me" class="form-control" rows="4"></textarea>
            </div>
        </td>
    </tr>
    <tr>
        <td class="col-xs-3"></td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <div class="buttons">
                    <a id="button-send" onclick="send('index.php?route=extension/module/smsir/send&token=<?php echo $token; ?>');"  class="btn btn-success btn-lg"><?php echo $smsir_send_message; ?></a>
                </div>
            </div>
            <div style="clear:both;"></div><br />
        </td>
    </tr>
</table>
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $smsir_sending_messages; ?></h4>
            </div>
            <div class="modal-body">
                <div id="modal-message"><h4><?php echo $smsir_dont_close_windows; ?></h4></div><br />
                <div id="progressbar-parent" class="progress progress-striped active">
                    <div class="progress-bar" id="progressbar" role="progressbar" style="width:0%"></div>
                </div>
                <div id="modal-message-sent"><h5><?php echo $smsir_last_sent_to; ?>: <span id="modal-telephone"> </span></h5></div>
                <div id="modal-message-senttotal"><h5><?php echo $smsir_sent_messages; ?>: <span id="modal-telephone-total">0</span></h5></div>
                <div id="modal-message-errors"><h5><?php echo $smsir_errors; ?> : <span id="modal-telephone-errors">0</span></h5></div>
                <div id="modal-message-errorsAll"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="myModalClose" data-dismiss="modal"><?php echo $smsir_close; ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    $("#count_me").characterCounter({
        counterFormat: '%1 <?php echo $smsir_written_chars; ?>'
    });
    $('select[name=\'to\']').bind('change', function() {
        $('#mail .to').hide();
        $('#mail #to-' + $(this).children('option:selected').attr('value').replace('_', '-')).show();
    });
    $('select[name=\'to\']').trigger('change');
    $('input[name=\'customers\']').autocomplete({
        delay: 500,
        source: function(request, response) {
            $.ajax({
                url: '<?php echo $customer_autocomplete_url ?>&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            category: item.customer_group,
                            label: item.name,
                            value: item.customer_id
                        }
                    }));
                }
            });
        },
        select: function(event, ui) {
            $('#to-customer #customer' + ui.item.value).remove();
            $('#to-customer #customer').append('<div id="customer' + ui.item.value + '">' + '<i class="fa fa-minus-circle"></i> ' + ui.item.label + '<input type="hidden" name="customer[]" value="' + ui.item.value + '" /></div>');
            $('#to-customer #customer div:odd').attr('class', 'odd');
            $('#to-customer #customer div:even').attr('class', 'even');
            return false;
        },
        focus: function(event, ui) {
            return false;
        }
    });
    $('#to-customer #customer').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();
        $('#to-customer #customer div:odd').attr('class', 'odd');
        $('#to-customer #customer div:even').attr('class', 'even');
    });
    $('input[name=\'affiliates\']').autocomplete({
        delay: 500,
        source: function(request, response) {
            $.ajax({
                url: 'index.php?route=marketing/affiliate/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item.name,
                            value: item.affiliate_id
                        }
                    }));
                }
            });
        },
        select: function(event, ui) {
            $('#affiliate' + ui.item.value).remove();
            $('#affiliate').append('<div id="affiliate' + ui.item.value + '"><i class="fa fa-minus-circle"></i> ' + ui.item.label + '<input type="hidden" name="affiliate[]" value="' + ui.item.value + '" /></div>');
            $('#affiliate div:odd').attr('class', 'odd');
            $('#affiliate div:even').attr('class', 'even');
            return false;
        },
        focus: function(event, ui) {
            return false;
        }
    });
    $('#affiliate').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();
        $('#affiliate div:odd').attr('class', 'odd');
        $('#affiliate div:even').attr('class', 'even');
    });
    $('input[name=\'products\']').autocomplete({
        delay: 500,
        source: function(request, response) {
            $.ajax({
                url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item.name,
                            value: item.product_id
                        }
                    }));
                }
            });
        }, 
        select: function(event, ui) {
            $('#product' + ui.item.value).remove();
            $('#product').append('<div id="product' + ui.item.value + '"><i class="fa fa-minus-circle"></i> ' + ui.item.label + '<input type="hidden" name="product[]" value="' + ui.item.value + '" /></div>');
            $('#product div:odd').attr('class', 'odd');
            $('#product div:even').attr('class', 'even');
            return false;
        },
        focus: function(event, ui) {
            return false;
        }
    });
    $('#product').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();
        $('#product div:odd').attr('class', 'odd');
        $('#product div:even').attr('class', 'even');
    });
    var number = 0;
    function addTelephone() {
        if ( $('input[name=\'telephones\']').val()) {
            var full_phone_number = $('input[name=\'telephones\']').val();
            $('#telephone').append('<div id="telephone' + number + '">' + '<i class="fa fa-minus-circle"></i> ' + full_phone_number + '<input type="hidden" name="phones[]" value="' + full_phone_number + '" /></div>');
            number++;
            $('#telephone div:odd').attr('class', 'odd');
            $('#telephone div:even').attr('class', 'even');
            $('input[name=\'telephones\']').val('');
        } else {
            alert('<?php echo $smsir_error_all_fiels_requied; ?>');
        }
    }
    $('#telephone').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();
        $('#telephone div:odd').attr('class', 'odd');
        $('#telephone div:even').attr('class', 'even');
    });
    function send(url) { 
        $('textarea[name="message"]').val();
        $('#modal-message-errorsAll').html("");
        $('#modal-telephone-errors').html("0");
        $.ajax({
            url: url,
            type: 'post',
            data: $('select, input, textarea'),
            dataType: 'json',
            beforeSend: function() {
                $('#button-send').attr('disabled', true);
            },
            complete: function() {
                $('#button-send').attr('disabled', false);
            },
            success: function(json) {
                $('.success, .warning, .error').remove();
                if (json['error']) {
                    if (json['error']['warning']) {
                        $('.box').before('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
                        $('.warning').fadeIn('slow');
                    }
                    if (json['error']['message']) {
                        $('textarea[name=\'message\']').parent().append('<span style="color:red;" class="error">' + json['error']['message'] + '</span>');
                    }
                }
                if (!json['error'] && !json['success']) {
                    alert("<?php echo $smsir_no_number_to_send; ?>");
                }
                if(json['AllCustomerClub']){
                    var type = 'AllCustomerClub';
                }
                if (json['success']) {
                    $('#myModal').modal({
                        backdrop: false,
                        keyboard: false
                    });
                    $('#myModalClose').attr('disabled', true);
                    $('#myModal').modal('show');
                    var errors = 0;
                    var catalogURL = '<?php echo $catalogURL; ?>image/';
                    $.smsir({
                        to: json['telephones'],
                        message: $('textarea[name="message"]').val(),
                        token: '<?php echo $token; ?>',
                        type: type,
                        success: function(resp) {
                            $('#progressbar').css('width', 100 + '%');
                            $('#progressbar').html(100 + '%');
                            $('#modal-telephone').html(resp['to'].join());
                            $('#modal-telephone-total').html(json['telephones'].length);
                            $('#progressbar-parent').removeClass('active');
                            $('#myModalClose').attr('disabled', false);
                            $('#modal-message').html('<h4><?php echo $smsir_message_sent_successfuly; ?></h4>');
                        },
                        error: function(resp) {
                            errors++;
                            $('#progressbar').css('width', 100 + '%');
                            $('#progressbar').html(100 + '%');
                            $('#progressbar').val(json['telephones'].length);
                            $('#modal-telephone-errors').html(errors);
                            $('#modal-message-errorsAll').append(resp['to'] + ": " + resp['message'] + "<br />");
                            $('#progressbar-parent').removeClass('active');
                            $('#myModalClose').attr('disabled', false);
                            $('#modal-message').html('<h4><?php echo $smsir_message_sent_with_some_errors; ?></h4>');
                        }
                    });
                }
            },
            error : function(data){
                alert(JSON.stringify(data));
                //$('textarea[name=\'message\']').parent().append('<span class="error">' + data.toSource() + '</span>');
            }
        });
    }
</script>