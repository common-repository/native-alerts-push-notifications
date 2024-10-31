<?php
  $nativealertsPush = NativeAlertsPush::getInstance();

  $nativealertsOptions = $nativealertsPush->getNativeAlertsOptions();

  if (isset($_POST['save_settings'])) {

    $errs = [];

    $nativealertsPushGetErrors = function ($item, $key) use (&$errs) {
      $field      = $item['name'];
      $desc       = $item['desc'];
      $isBlankOk  = $item['isBlankOk'];
      if ((!isset($_POST[$field]) && false===$isBlankOk) || (isset($_POST[$field]) && trim($_POST[$field]) === '' && false===$isBlankOk)) {
        $errs[] = $item['desc'] . " cannot be empty.";
      }
    };

    $updateOptions = function($item, $key) {
      $field = $item['name'];
      update_option($item['name'], sanitize_text_field($_POST[$field]));
    };

    array_walk($nativealertsOptions, $nativealertsPushGetErrors);

    if (count($errs) === 0) {
      array_walk($nativealertsOptions, $updateOptions);
      $nativealerts_push_success = 'Settings successfully saved.';
    } 
    $nativealertsPush->refresh();
  }

?>

<style type="text/css">
#settings_form ul li span.field {
  width: 100px;
  display: block;
  float: left;
}

#settings_form ul li input {
  width: 250px;
  display: block;
}

</style>

<h1>NativeAlerts Push Notification</h1>

<?php echo $nativealertsPush->getCurrentSettingsStatus(); ?>

<h2>Settings</h2>

<?php

if (isset($errs) && count($errs) > 0) {
  echo '<div class="nativealerts_push_error">Form errors. Please correct the following.</div>';
  echo '<ul><li>' . implode('</li><li>', $errs) . '</li></ul>';
}

if (isset($nativealerts_push_success)) {
  echo $nativealerts_push_success;
}


?>
<form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" name="settings_form" id="settings_form">

  <ul>
    <li>
    <span class="field">Your Domain</span><input type="text" name="nativealerts_push_pub_domain" value="<?php echo $nativealertsOptions['nativealerts_push_pub_domain']['value']?>"></input>
    </li>
    <li>
    <span class="field">Site ID</span><input type="text" name="nativealerts_push_site_id" value="<?php echo $nativealertsOptions['nativealerts_push_site_id']['value']?>"></input>
    </li>
    <li>
    <span class="field">API Login</span><input type="text" name="nativealerts_push_api_login" value="<?php echo $nativealertsOptions['nativealerts_push_api_login']['value']?>"></input>
    </li>
    <li>
    <span class="field">API Password</span><input type="text" name="nativealerts_push_api_password" value="<?php echo $nativealertsOptions['nativealerts_push_api_password']['value']?>"></input>
    </li>
    <li>
    <span class="field">API Domain</span> <input type="text" name="nativealerts_push_api_domain" value="<?php echo $nativealertsOptions['nativealerts_push_api_domain']['value']?>"></input>
    </li>

<!--    <li>Ad Rate %: the rate at which ads are sent relative to news. <input type="text" name="nativealerts_push_ad_rate" value="<?php echo $nativealertsOptions['nativealerts_push_ad_rate']['value']?>"></input></li>-->
  </ul>

  <input type="submit" class="button-primary" value = "Save Changes" name = "save_settings" />

</form>
