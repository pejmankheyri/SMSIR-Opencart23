<?php

/**
 * Modules Bulk SMS Setting tab page
 * 
 * PHP version 5.6.x | 7.x
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

?>
<table class="table"> 
    <tr>
        <td class="col-xs-3">
            <h5><strong><?php echo $smsir_admins_mobiles; ?> : </strong></h5>
            <span class="help"><i class="fa fa-info-circle"></i> <?php echo $smsir_admins_mobiles_desc; ?> </span>
        </td>
        <td class="col-xs-9" id="storeOwnerInputs">   
            <div class="col-xs-4">
                <div class="col-xs-12 clearPadding number">
                    <div class="input-group">
                        <input type="text" class="form-control" id="input-store_owner_phone" value="" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" id="addStoreOwner"> <?php echo $smsir_add; ?> </button>
                        </span>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="col-xs-3">
        </td>
        <td class="col-xs-9">
            <div class="col-xs-4">
                <div id="storeOwnerTelephone" class="scrollbox form-control">
                    <?php if (!empty($data['SMSIr']['StoreOwnerPhoneNumber'])) { ?>
                        <?php $i = 0 ?>
                        <?php foreach ($data['SMSIr']['StoreOwnerPhoneNumber'] as $store_owner_number) { ?>
                            <div id="storeOwnerTelephone<?php echo $i ?>"><i class="fa fa-minus-circle"></i>&nbsp;<?php echo $store_owner_number ?><input type="hidden" name="SMSIr[StoreOwnerPhoneNumber][]" value="<?php echo $store_owner_number ?>" /></div>                            
                            <?php $i++; ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </td>
    </tr>
</table>
<script>
    var owner_number = <?php echo(!empty($data['SMSIr']['StoreOwnerPhoneNumber']) ? count($data['SMSIr']['StoreOwnerPhoneNumber']) : 0) ?>;
    $("#addStoreOwner").click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        if ($('input[id=\'input-store_owner_phone\']').val()) {
            var full_phone_number =  $('input[id=\'input-store_owner_phone\']').val();
            $('#storeOwnerTelephone').append('<div id="storeOwnerTelephone' + owner_number + '">' + '<i class="fa fa-minus-circle"></i>&nbsp;' + full_phone_number + '<input type="hidden" name="SMSIr[StoreOwnerPhoneNumber][]" value="' + full_phone_number + '" /></div>');
            owner_number++;
            $('#storeOwnerTelephone div:odd').attr('class', 'odd');
            $('#storeOwnerTelephone div:even').attr('class', 'even');
            $('input[id=\'input-store_owner_phone\']').val('');
        } else {
            alert('<?php echo $smsir_error_all_fiels_requied; ?>');
        }
    });
    $('#storeOwnerTelephone').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();

        $('#storeOwnerTelephone div:odd').attr('class', 'odd');
        $('#storeOwnerTelephone div:even').attr('class', 'even'); 
    });
</script>

