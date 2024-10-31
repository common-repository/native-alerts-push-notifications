<?php
$nativealerts_AdsDevices = new NativeAlertsPush_Report(
  'Ad Push Activity By Device',
  'stats_devices',
  '{"id": "date_est", "header": "Date", "type" : "string"},
      {"id": "device", "header": "Device", "type" : "string"},
      {"id": "impressions", "header": "Impressions", "type" : "number", 
        "decimals" : 0,
        "formatter": ABUtils.formatNumber, 
        "aggregate": "SUM"},
      {"id": "clicks", "header": "Clicks", "type":"number", "formatter" : ABUtils.formatNumber, "aggregate": "SUM"},
      {"id": "ctr", "header": "CTR", "type":"number", 
        "formatter" : ABUtils.formatNumber,
        "decimals" : 3,
        "aggregate":"formula", 
        "formula":"{clicks}/{impressions}"}',
  "{
      rowsPerPage : 10,
      dataSource : dataSource,
      columns: cols,
      className: 'nativealerts_stats',
      containerElement: document.getElementById('nativealerts_stats_devices_container')
    }",
  $nativealertsPush->getAdStatsByDevice(),
  $nativealertsPush->getToken(),
  $nativealertsPush->getApiUrl($nativealertsPush::ENDPOINT_AD_STATS_DEVICE));

echo $nativealerts_AdsDevices->render();

