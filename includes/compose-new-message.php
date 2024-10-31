<h1>NativeAlerts Push Notification</h1>

<?php
  $nativealertsPush = NativeAlertsPush::getInstance();
  echo $nativealertsPush->getCurrentSettingsStatus();

?>


<h2>Push Messages</h2>
<?php

  if (isset($_POST['nativealerts_push_send_message'])) {
    $nativeAlertsData = [
      'title'     => sanitize_text_field($_POST['nativealerts_push_title']),
      'message'   => sanitize_text_field($_POST['nativealerts_push_message']),
      'click_url' => sanitize_text_field($_POST['nativealerts_push_click_url']),
      'image_url' => sanitize_text_field($_POST['nativealerts_push_image_url']),
      'schedule'  => sanitize_text_field($_POST['nativealerts_push_schedule']),
      'hour'      => sanitize_text_field($_POST['nativealerts_push_schedule_hour']),
      'minute'    => sanitize_text_field($_POST['nativealerts_push_schedule_minute']),
      'part'      => sanitize_text_field($_POST['nativealerts_push_schedule_part']),
      'platforms' => isset($_POST['nativealerts_push_platform']) ? 
                      array_reduce($_POST['nativealerts_push_platform'], 
                      function ($total, $platformId) {$total += $platformId; return $total;}) 
                      : 0,
      'segments'  => isset($_POST['nativealerts_push_segment']) ? 
                      array_reduce($_POST['nativealerts_push_segment'], 
                      function ($total, $segmentId) {$total += $segmentId; return $total;}) 
                      : 0
    ];

    // validation
    $nativeAlertsUrlPattern = '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i';
    $nativeAlertsMessageFormIsValid = true;
    $nativeAlertsFormErrors = [];
    if ($nativeAlertsData['title'] == '') {
      $nativeAlertsMessageFormIsValid = false;
      $nativeAlertsFormErrors ['nativealerts_push_title'] = 'Must have a Title.';
    }

    if ($nativeAlertsData['message'] == '') {
      $nativeAlertsMessageFormIsValid = false;
      $nativeAlertsFormErrors ['nativealerts_push_message'] = 'Must have a Message.';
    }

    if ($nativeAlertsData['click_url'] == '') {
      $nativeAlertsMessageFormIsValid = false;
      $nativeAlertsFormErrors ['nativealerts_push_click_url'] = 'Must have a Click URL.';
    } else {
      if (!preg_match($nativeAlertsUrlPattern, $nativeAlertsData['click_url'])) {
        $nativeAlertsMessageFormIsValid = false;
        $nativeAlertsFormErrors ['nativealerts_push_click_url'] = 'Invalid Click URL';
      }
    }

    if ($nativeAlertsData['image_url'] == '') {
      $nativeAlertsMessageFormIsValid = false;
      $nativeAlertsFormErrors ['nativealerts_push_image_url'] = 'Must have an Image URL.';
    } else {
      if (!preg_match($nativeAlertsUrlPattern, $nativeAlertsData['image_url'])) {
        $nativeAlertsMessageFormIsValid = false;
        $nativeAlertsFormErrors ['nativealerts_push_click_url'] = 'Invalid Image URL';
      }
    }

    if ($nativeAlertsData['platforms'] == 0) {
      $nativeAlertsMessageFormIsValid = false;
      $nativeAlertsFormErrors ['nativealerts_push_platform'] = 'Must select at least one Platform';
    }

    if ($nativeAlertsData['segments'] == 0) {
      $nativeAlertsMessageFormIsValid = false;
      $nativeAlertsFormErrors ['nativealerts_push_segment'] = 'Must select at least on User Segment';
    }

    if ($nativeAlertsData['schedule'] == '') {
      $nativeAlertsMessageFormIsValid = false;
      $nativeAlertsFormErrors ['nativealerts_push_schedule'] = 'Must specify when to send the message';
    } else {
      if ($nativeAlertsData['schedule']) {
        $nativeAlertsScheduleDateTime = $nativealertsPush::formatSchedule($nativeAlertsData['schedule'],
        $nativeAlertsData['hour'],
        $nativeAlertsData['minute'],
        $nativeAlertsData['part']
        );
        
        $now = new DateTime();
        $now->setTimeZone(new DateTimeZone('America/New_York'));
        $schedDate = new DateTime($nativeAlertsScheduleDateTime, new DateTimeZone('America/New_York'));
        if ($schedDate < $now) {
          $nativeAlertsMessageFormIsValid = false;
          $nativeAlertsFormErrors ['nativealerts_push_schedule'] = 'Cannot specify a date that has passed.';
        } else {
          $nativeAlertsData['schedule'] = $nativeAlertsScheduleDateTime;
        }
      }
    }

    if (count($nativeAlertsFormErrors) == 0) {
      $nativealertsPushSendResult = $nativealertsPush->sendMessage($nativeAlertsData);
    }
  } else {
    $nativeAlertsData = [
      'title'     => '',
      'message'   => '',
      'click_url' => '',
      'image_url' => '',
      'schedule'  => date('Y-m-d'),
      'hour'      => '01',
      'minute'    => '00',
      'part'      => 'AM',
      'platforms' => 0,
      'segments'  => 0
    ];
  }

if (isset($nativealertsPushSendResult)) {
  echo $nativealertsPushSendResult;
}

if (isset($nativeAlertsFormErrors) && count($nativeAlertsFormErrors) > 0) {
  echo '<ul class="nativealerts_formerror"><b>The form is invalid. Please correct the following:</b>';
  foreach ($nativeAlertsFormErrors as $key => $val) {
    echo '<li>' . $val . '</li>';
  }
  echo '</ul>';
} else {
  $nativeAlertsData = [
    'title'     => '',
    'message'   => '',
    'click_url' => '',
    'image_url' => '',
    'schedule'  => date('Y-m-d'),
    'hour'      => '01',
    'minute'    => '00',
    'part'      => 'AM',
    'platforms' => 0,
    'segments'  => 0
  ];
}

?>

<div id="nativealerts_compose_container">
  <div id="nativealerts_form_section">
    <h2>Compose New Push Message Form</h2>
    <form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" name="nativealerts_push_send_message_form" id="nativealerts_push_send_message_form">

    <dl>
      <dt>Title</dt><dd><input type="text" id="nativealerts_push_title" name="nativealerts_push_title" placeholder="Title of your message" value="<?php echo $nativeAlertsData['title']?>"/></dd>
      <dt>Message</dt><dd><input type="text" id="nativealerts_push_message" name="nativealerts_push_message" placeholder="Body of your message" value="<?php echo $nativeAlertsData['message']?>"/></dd>
      <dt>Click URL</dt><dd><input type="text" id="nativealerts_push_click_url" name="nativealerts_push_click_url" placeholder="Landing page when message is clicked" value="<?php echo $nativeAlertsData['click_url']?>"/></dd>
      <dt>Image</dt><dd><input type="text" id="nativealerts_push_image_url" name="nativealerts_push_image_url" placeholder="URL of image" value="<?php echo $nativeAlertsData['image_url']?>"/></dd>
      <dt>Delivery Schedule</dt><dd><input type="text" id="nativealerts_push_schedule" name="nativealerts_push_schedule" value="<?php echo $nativeAlertsData['schedule']?>"/><?php 
        echo NativeAlertsPush::getHourSelect($nativeAlertsData['hour']); 
        echo NativeAlertsPush::getMinuteSelect($nativeAlertsData['minute']); 
        echo NativeAlertsPush::getAMPM($nativeAlertsData['part']); 
        ?>
      </dd>
      <dt>Target Platform</dt><dd>
      <br/>Select the platform to which the notifications will be sent.
      <?php
        $nativealertsPlatforms = $nativealertsPush->getPlatforms();
        echo '<ul>';
        foreach ($nativealertsPlatforms as $nativealertsPlatformKey => $nativealertsPlatformValue) {
          echo '<li><input type="checkbox" class="nativealerts_platforms" name="nativealerts_push_platform[]" value="' . $nativealertsPlatformValue->id . '"> ' .
            $nativealertsPlatformValue->name . '</li>';
        }
        echo '<li><input type="checkbox" name="nativealerts_push_all_platform" id="nativealerts_push_all_platform"/> All</li>';
        echo '</ul>';
      ?>
      </dd>
      <dt>User Segment</dt><dd>
      <br/>Select the users to whom the notification will be sent.
      <?php
        $nativealertsUserSegments = $nativealertsPush->getUserSegments();
        echo '<ul>';
        foreach ($nativealertsUserSegments as $nativealertsSegmentKey => $nativealertsSegmentValue) {
          echo '<li><input type="checkbox" class="nativealerts_segments" name="nativealerts_push_segment[]" value="' . $nativealertsSegmentValue->id . '"> ' .
            $nativealertsSegmentValue->name . ' (' . $nativealertsSegmentValue->description . ')</li>';
        }
        echo '<li><input type="checkbox" name="nativealerts_push_all_segment" id="nativealerts_push_all_segment"/> All</li>';
        echo '</ul>'
      ?>
      </dd>
    </dl>
    <input type="submit" name="nativealerts_push_send_message"/>
    </form>

  </div>

  <div id="nativealerts_recent_section">

    <?php
    $posts = $nativealertsPush->getRecentPosts();
    ?>

    <h2>Compose Message From Recent Posts</h2>

    <b>Pre-fill the Compose New Push Message Form by clicking on a post below:</b>
    <table class="nativealerts_excerpts">
    <thead><tr>
      <th>Post Date</th>
      <th>Title</th>
      <th>Excerpt</th>
      <th>Link</th>
      <th>Image</th>
    </tr></thead>
    <tbody>
    <?php
    /**
     * 
     *   [ID] => 10
     *   [post_author] => 1
     *   [post_date] => 2019-03-11 16:28:42
     *   [post_date_gmt] => 2019-03-11 16:28:42
     *   [post_content] => Third Post
     *   [post_title] => Third Post
     *   [post_excerpt] => 
     *   [post_status] => publish
     *   [comment_status] => open
     *   [ping_status] => open
     *   [post_password] => 
     *   [post_name] => third-post
     *   [to_ping] => 
     *   [pinged] => 
     *   [post_modified] => 2019-03-11 16:28:42
     *   [post_modified_gmt] => 2019-03-11 16:28:42
     *   [post_content_filtered] => 
     *   [post_parent] => 0
     *   [guid] => http://wp.scrollroll.com/?p=10
     *   [menu_order] => 0
     *   [post_type] => post
     *   [post_mime_type] => 
     *   [comment_count] => 0
     *   [filter] => raw  */

      foreach ($posts as $post) {
        echo '<tr class="nativealerts_post_row">';
        echo '<td>' . $post['post_date'] . '</td>';
        echo '<td>' . $post['post_title'] . '</td>';
        echo '<td>' . get_the_excerpt($post['ID']) . '</td>';
        echo '<td>' . get_permalink($post['ID']) . '</td>';
        echo '<td class="nativealerts_thumb">' . get_the_post_thumbnail($post['ID']) . '</td>';
        echo '</tr>';
      } 
    ?>
    </tbody>
    <tfooter></tfooter>
    </table>  
  </div>

</div>







<script type="text/javascript">
jQuery(document).ready(function() {
  var $ = jQuery;

  $('#nativealerts_push_schedule').datepicker({ dateFormat: "yy-mm-dd" });

  $('.nativealerts_post_row').on('click', function() {
      var tr = $(this).get(0);
      var tds = tr.getElementsByTagName('td');
      var img = $(tds[4]).get(0).getElementsByTagName('img')[0];
      $('#nativealerts_push_title').val('<?php echo get_bloginfo('name')?>');
      $('#nativealerts_push_message').val($(tds[1]).text());
      $('#nativealerts_push_click_url').val($(tds[3]).text());
      $('#nativealerts_push_image_url').val($(img).attr('src'));

  }); 

  $('#nativealerts_push_all_segment').on('click', function() {
    $('input[name="nativealerts_push_segment[]"]').each(function() {
      $(this).attr('checked', $('#nativealerts_push_all_segment').is(':checked'));
    });
  });

  $('#nativealerts_push_all_platform').on('click', function() {
    $('input[name="nativealerts_push_platform[]"]').each(function() {
      $(this).attr('checked', $('#nativealerts_push_all_platform').is(':checked'));
    });
  });  

});
</script>
