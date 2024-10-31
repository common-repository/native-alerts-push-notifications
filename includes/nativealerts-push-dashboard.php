<?php

  $nativealertsPush = NativeAlertsPush::getInstance();

?>
<h1>NativeAlerts Push Notification</h1>
<?php echo $nativealertsPush->getCurrentSettingsStatus(); ?>
<h2>Ads Statistics</h2>

<div id="nativealerts-tabs">
	<ul>
		<li><a href="#nativealerts-subscriber-counts">Subscriber Counts</a></li>
		<li><a href="#nativealerts-stats-permissions">Permission Statistics</a></li>
		<li><a href="#nativealerts-ad-push-activity">Ad Push Activity Summary</a></li>
		<li><a href="#nativealerts-ad-push-device">Ad Push Activity By Device</a></li>
		<li><a href="#nativealerts-ad-stats-revenue">Ad Push Revenue Statistics</a></li>
	</ul>
	<div id="nativealerts-subscriber-counts">
	<?php
	include NATIVEALERTS_PUSH_PATH . '/includes/reports/stats-subscriber-counts.php';
	?>
	</div>
	<div id="nativealerts-stats-permissions">
	<?php
	include NATIVEALERTS_PUSH_PATH . '/includes/reports/stats-permissions.php';
	?>
	</div>
	<div id="nativealerts-ad-push-activity">
	<?php
	include NATIVEALERTS_PUSH_PATH . '/includes/reports/stats-user-activity.php';
	?>
	</div>

	<div id="nativealerts-ad-push-device">
	<?php
	include NATIVEALERTS_PUSH_PATH . '/includes/reports/stats-devices.php';
	?>
	</div>

	<div id="nativealerts-ad-stats-revenue">
	<?php
	include NATIVEALERTS_PUSH_PATH . '/includes/reports/stats-revenue.php';
	?>
	</div>

</div>
</p>



<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#nativealerts-tabs').tabs()
});
</script>