<?php

/**
 * Modules Main tpl File
 * 
 * PHP version 5.6.x | 7.x
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

 ?>

<?php echo $header;?>
<?php echo $column_left;?>

<div id="content">
 <div id="overlay" style="display:none"></div>
    <div class="page-header">
        <div class="container-fluid">
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
            <div class="alert alert-danger autoSlideUp"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
             <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success autoSlideUp"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <script>$('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"> <span><?php echo $heading_title; ?></span></h3><span id="status"></span>
                <div class="storeSwitcherWidget pull-right">
                	<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><?php echo $store['name']; if($store['store_id'] == 0) echo $text_default; ?>&nbsp;<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                	<ul class="dropdown-menu" role="menu">
                    	<?php foreach ($stores  as $st) { ?>
                    		<li><a href="index.php?route=<?php echo $_module_path ?>&store_id=<?php echo $st['store_id'];?>&token=<?php echo $token; ?>"><?php echo $st['name']; ?></a></li>
                    	<?php } ?>
                	</ul>
                </div>
            </div>
            <div class="panel-body">
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                        <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
                        <div class="tabbable">
                            <div class="tab-navigation form-inline">
                                <ul class="nav nav-tabs mainMenuTabs" id="mainTabs">
                                    <li><a href="#app_info" data-toggle="tab"><?php echo $smsir_general; ?></a></li>
                                    <li><a href="#bulksmssend" data-toggle="tab"><?php echo $smsir_send_message; ?></a></li>
                                    <li><a href="#actions" data-toggle="tab"><?php echo $smsir_transactional_sms; ?></a></li>
                                    <li><a href="#main_settings" data-toggle="tab"><?php echo $smsir_settings; ?></a></li>
                                </ul>
                                <div class="tab-buttons">
                                    <button type="submit" class="btn btn-success save-changes"><i class="fa fa-check"></i>&nbsp;<?php echo $save_changes?></button>
                                    <a onclick="location = '<?php echo $cancel; ?>'" class="btn btn-warning"><i class="fa fa-times"></i>&nbsp;<?php echo $button_cancel?></a>
                                </div>
                            </div><!-- /.tab-navigation -->
                            <div class="tab-content">
                                <div id="bulksmssend" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$_module_path.'/tab_bulksmssend.php'); ?></div>
                                <div id="actions" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$_module_path.'/tab_actions.php'); ?></div>
                          	    <div id="main_settings" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$_module_path.'/tab_settings.php'); ?></div>
                                <div id="app_info" class="tab-pane fade"><?php require_once(DIR_APPLICATION.'view/template/'.$_module_path.'/tab_app.php'); ?></div>
                            </div> <!-- /.tab-content -->
                        </div><!-- /.tabbable -->
                    </form>

    			<div class="box-heading" style="text-align:center">
                    <h5>pejmankheyri@gmail.com <?php echo $smsir_version." ".$_version; ?></h5>
                </div>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
openSMSIrURL = function(verb, url, data, target) {
        var form = document.createElement("form");
        form.action = url;
        form.method = verb;
        form.target = target || "_self";
        if (data) {
          for (var key in data) {
            var input = document.createElement("textarea");
            input.name = key;
            input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
            form.appendChild(input);
          }
        }
        form.style.display = 'none';
        document.body.appendChild(form);
        form.submit();
      };


$(function() {
    $('.mainMenuTabs a:first').tab('show'); // Select first tab
     $('.mainMenuTabs a:first').click();
    if (window.localStorage && window.localStorage['currentTab']) {
        $('.mainMenuTabs a[href="'+window.localStorage['currentTab']+'"]').tab('show');
    }
    if (window.localStorage && window.localStorage['currentSubTab']) {
        $('a[href="'+window.localStorage['currentSubTab']+'"]').tab('show');
    }
    $('.fadeInOnLoad').css('visibility','visible');
    $('.mainMenuTabs a[data-toggle="tab"]').click(function() {
        if (window.localStorage) {
            window.localStorage['currentTab'] = $(this).attr('href');
        }
    });
    $('a[data-toggle="tab"]:not(.mainMenuTabs a[data-toggle="tab"], .app_info a[data-toggle="tab"])').click(function() {
        if (window.localStorage) {
            window.localStorage['currentSubTab'] = $(this).attr('href');
        }
    });
});


</script>
<?php echo $footer; ?>
