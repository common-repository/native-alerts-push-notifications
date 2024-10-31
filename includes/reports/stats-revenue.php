<?php
$nativealerts_AdRevStats = new NativeAlertsPush_Report(
  'Ad Push Revenue Statistics - Daily eCPM',
  'rev_stats',
  '{"id": "creation_date", "header": "Date", "type" : "string"},
      {"id": "impressions", "header": "Impressions", "type" : "number", 
        "formatter": ABUtils.formatNumber, 
        "decimals" : 0,
        "aggregate": "SUM"},
      {"id": "clicks", "header": "Clicks", "type":"number", "formatter" : ABUtils.formatNumber, "aggregate": "SUM"},
      {"id": "ctr", "header": "CTR", "type":"number", 
        "formatter" : ABUtils.formatNumber, 
        "decimals" : 3,
        "aggregate":"formula", 
        "formula":"{clicks}/{impressions}"},
      {"id": "pub_earned", "header": "Earnings", "type":"number", 
        "formatter" : ABUtils.formatNumber, 
        "decimals" : 2, 
        "aggregate": "SUM"},
      {"id": "pub_ecpm", "header": "eCPM", "type":"number", 
        "formatter" : ABUtils.formatNumber, 
        "decimals" : 3,
        "aggregate":"formula", 
        "formula":"({pub_earned}/{impressions}) * 1000"}',
  "{
      rowsPerPage : 10,
      dataSource : dataSource,
      columns: cols,
      className: 'nativealerts_stats',
      containerElement: document.getElementById('nativealerts_rev_stats_container')
    }",
  $nativealertsPush->getAdRevenueStats(),
  $nativealertsPush->getToken(),
  $nativealertsPush->getApiUrl($nativealertsPush::ENDPOINT_AD_REVENUE_STATS));

echo $nativealerts_AdRevStats->render();

