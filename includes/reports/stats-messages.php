<?php
$nativealerts_SentContentPush = new NativeAlertsPush_Report(
  'Sent Content Push',
  'stats_sentcontent',
  '{"id": "title", "header": "Title", "type" : "string"},
  {"id": "message", "header": "Message", "type" : "string"},
  {"id": "send_date_epoch", "header": "Send Date", "formatter" : ABUtils.formatEpochToYMD, "type" : "date"},
  {"id": "sent_date_epoch", "header": "Sent Date", "formatter" : ABUtils.formatEpochToYMD, "type" : "string"},
  {"id": "clicks", "header": "Clicks", "type":"number", "formatter" : ABUtils.formatNumber, "aggregate": "SUM"},
  {"id": "impressions", "header": "Impressions", "type" : "number", "formatter": ABUtils.formatNumber, "aggregate": "SUM"},
  {"id": "ctr", "header": "CTR", "type":"number", "formatter" : ABUtils.formatNumber}',
  "{
      rowsPerPage : 10,
      dataSource : dataSource,
      columns: cols,
      className: 'nativealerts_stats',
      containerElement: document.getElementById('nativealerts_stats_sentcontent_container')
    }",
  $nativealertsPush->getMessages(),
  $nativealertsPush->getToken(),
  $nativealertsPush->getApiUrl($nativealertsPush::ENDPOINT_STATS_MESSAGES));

echo $nativealerts_SentContentPush->render();




