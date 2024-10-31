<h1>NativeAlerts Push Notification</h1>

<?php
  $nativealertsPush = NativeAlertsPush::getInstance();
  echo $nativealertsPush->getCurrentSettingsStatus();
?>


<h2>Push Stats</h2>

<div id="nativealerts-tab">
	<ul>
		<li><a href="#nativealerts-unsent">Unsent Messages</a></li>
		<li><a href="#nativealerts-sent-content">Sent Content Push</a></li>
	</ul>
	<div id="nativealerts-unsent">
		<?php include NATIVEALERTS_PUSH_PATH . '/includes/reports/unsent-messages.php';?>
	</div>

	<div id="nativealerts-sent-content">
		<?php include NATIVEALERTS_PUSH_PATH . '/includes/reports/stats-messages.php';?>
	</div>
</div>


<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#nativealerts-tab').tabs();
});
</script>