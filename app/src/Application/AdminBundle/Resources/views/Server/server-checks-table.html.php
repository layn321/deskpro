<?php if (!defined('DP_ROOT')) exit('No access'); ?>
<?php $failed = false; ?>
<?php $did_create_db = false; ?>
<?php $skip_empty_check = true; ?>
<?php $run_context = 'admin'; ?>
<?php require(DP_ROOT.'/src/Application/InstallBundle/Resources/views/Install/server-checks-table.html.php') ?>