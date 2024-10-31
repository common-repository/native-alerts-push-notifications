<?php

  // delete handler
  if (isset($_POST['nativealerts_push_delete_message']) && isset($_POST['nativealerts_push_message_id'])) {
    foreach ($_POST['nativealerts_push_message_id'] as $nativealerts_push_message_id ) {
      if ($nativealertsPush->deleteMessage($nativealerts_push_message_id) !== '') {
        echo 'Message deletion failed.';
      };
    }
  }

?>

<h3>Unsent Content Push</h3>

<form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" name="nativealerts_push_send_message_form" id="nativealerts_push_delete_message_form" onsubmit="return confirm('Permanently Delete Selected Messages?');">
<?php
$nativealertsPushUnsentMessages = $nativealertsPush->getUnsentMessages();
echo '<table border="0" class="nativealerts_stats">';
echo '<tr><th>Select</th><th>Title</th><th>Message</th><th>Scheduled Delivery</th></tr>';
foreach ($nativealertsPushUnsentMessages as $nativealertsPushKey => $nativealertsPushMessage) {
  echo '<tr>';
  echo '<td><input type="checkbox" name="nativealerts_push_message_id[]" value="' . $nativealertsPushMessage->id .'"/></td>';
  echo '<td>' . $nativealertsPushMessage->title . '</td>';
  echo '<td>' . $nativealertsPushMessage->message . '</td>';
  echo '<td>' . (isset($nativealertsPushMessage->send_date_NYTime) ? $nativealertsPush::formatYMD($nativealertsPushMessage->send_date_NYTime) : '') . '</td>';
  echo '</tr>';
}
echo '</table>';

?>
<input type="submit" name="nativealerts_push_delete_message" value="Delete Selected"/>
</form>

