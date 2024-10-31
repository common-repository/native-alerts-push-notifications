<?php
$nativealerts_AdUserActivity = new NativeAlertsPush_Report(
  'Ad Push Activity Summary',
  'stats_activity',
  '{"id": "date_est", "header": "Date", "type" : "string"},
      {"id": "user_count", "header": "Subscribers", "type" : "number",  "formatter": ABUtils.formatNumber, "aggregate": "SUM"},
      {"id": "impressions", "header": "Impressions", "type" : "number", "formatter": ABUtils.formatNumber, "aggregate": "SUM"},
      {"id": "clicks", "header": "Clicks", "type":"number", "formatter" : ABUtils.formatNumber, "aggregate": "SUM"},
      {"id": "ctr", "header": "CTR", "type":"number", 
        "formatter" : ABUtils.formatNumber,
        "decimals" : 2,
        "aggregate":"formula", 
        "formula":"{clicks}/{impressions}"}',
  "{
      rowsPerPage : 10,
      dataSource : dataSource,
      columns: cols,
      className: 'nativealerts_stats',
      containerElement: document.getElementById('nativealerts_stats_activity_container')
    }",
  $nativealertsPush->getAdUserActivity(),
  $nativealertsPush->getToken(),
  $nativealertsPush->getApiUrl($nativealertsPush::ENDPOINT_AD_USER_ACTIVITY));

echo $nativealerts_AdUserActivity->render();

