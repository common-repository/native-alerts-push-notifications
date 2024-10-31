<?php
$nativealerts_subscriberCounts = new NativeAlertsPush_Report(
  'Permissions Statistics',
  'stats_permissions',
  '{"id": "date_est", "header": "Date", "type":"date"},
   {"id": "device", "header": "Platform", "type":"string"},
   {"id": "denied", "header": "Denied", "type" : "number",  "formatter": ABUtils.formatNumber, "aggregate": "SUM"},
   {"id": "default", "header": "Default", "type" : "number",  "formatter": ABUtils.formatNumber, "aggregate": "SUM"},
   {"id": "allowed", "header": "Allowed", "type" : "number",  "formatter": ABUtils.formatNumber, "aggregate": "SUM"},
   {"id": "total", "header": "Total", "type" : "number",  "formatter": ABUtils.formatNumber, "aggregate": "SUM"}',
  '{
      rowsPerPage : 10,
      hasPageIndex: true,
      dataSource : dataSource,
      columns: cols,
      className: "nativealerts_stats",
      containerElement: document.getElementById("nativealerts_stats_permissions_container")
    }',
  $nativealertsPush->getPermissionStats(),
  $nativealertsPush->getToken(),
  $nativealertsPush->getApiUrl($nativealertsPush::ENDPOINT_PERMISSION_STATS));

echo $nativealerts_subscriberCounts->render();
