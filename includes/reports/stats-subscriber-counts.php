<?php
$nativealerts_subscriberCounts = new NativeAlertsPush_Report(
  'Subscriber Counts',
  'stats_subscriber',
  '{"id": "date", "header": "Date", "type":"date"},
   {"id": "platform", "header": "Platform", "type":"string"},
   {"id": "browser", "header": "Browser", "type":"string"},
   {"id": "active", "header": "Active", "type" : "number",  "formatter": ABUtils.formatNumber, "aggregate": "SUM"},
   {"id": "inactive", "header": "Inactive", "type" : "number",  "formatter": ABUtils.formatNumber, "aggregate": "SUM"},
   {"id": "total", "header": "Total", "type" : "number",  "formatter": ABUtils.formatNumber, "aggregate": "SUM"}',
  '{
      rowsPerPage : 10,
      hasPageIndex: true,
      dataSource : dataSource,
      columns: cols,
      className: "nativealerts_stats",
      containerElement: document.getElementById("nativealerts_stats_subscriber_container")
    }',
  $nativealertsPush->getSubscriberCounts(),
  $nativealertsPush->getToken(),
  $nativealertsPush->getApiUrl($nativealertsPush::ENDPOINT_SUBSCRIBER_COUNTS));

echo $nativealerts_subscriberCounts->render();
