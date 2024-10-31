<?php
/*
 * Plugin Name: Native Alerts Push Notifications
 * Plugin URI: https://www.nativealerts.com/
 * Description: Increase monetization and improve user engagement with NativeAlerts
 * Version: 1.2
 * Author: Adiant Tech
 * License: GPLv2 or later
 */

/*
 * Plugin constants
 */
defined( 'ABSPATH' ) or die( 'Access Denied!' );

if (!defined('NATIVEALERTS_PUSH_URL'))
  define('NATIVEALERTS_PUSH_URL', plugin_dir_url( __FILE__ ));
if (!defined('NATIVEALERTS_PUSH_PATH'))
  define('NATIVEALERTS_PUSH_PATH', plugin_dir_path( __FILE__ ));

require_once NATIVEALERTS_PUSH_PATH . '/includes/lib/Report.php';

/*
 * Main class
 * Singleton
 */
class NativeAlertsPush
{

  const OPTIONS_KEY = 'NativeAlerts_Push';

  const ENDPOINT_STATS_MESSAGES     = '/report/stats/messages/';
  const ENDPOINT_UNSENT_MESSAGES    = '/messages/unsent/';
  const ENDPOINT_AD_USER_ACTIVITY   = '/report/stats/ad-user-activity/';
  const ENDPOINT_AD_STATS_DEVICE    = '/report/stats/ads-device/';
  const ENDPOINT_SUBSCRIBER_COUNTS  = '/report/stats/subscriber-counts/';
  const ENDPOINT_PLATFORMS          = '/data/platforms/';
  const ENDPOINT_USER_SEGMENTS      = '/data/user-segments/';
  const ENDPOINT_PROFILE            = '/pub/profile/';
  const ENDPOINT_PERMISSION_STATS   = '/report/stats/permission-stats/';
  const ENDPOINT_AD_REVENUE_STATS   = '/report/stats/ad-revenue-stats/';

  const MESSAGE_INVALID_SETTINGS    = 'Invalid settings detected. Please check that your Domain and Site ID is correct.';

  private static $_instance = null;

  private $nativealertsOptions = [];
  private $apiDomain;
  private $pubDomain;
  private $siteId;

  /*
   * class constructor
   * @return N/A
   */
  private function __construct() {
      $this->apiDomain = get_option('nativealerts_push_api_domain');
      $this->siteId = get_option('nativealerts_push_site_id');
      $this->pubDomain = get_option('nativealerts_push_pub_domain');

      $this->setNativeAlertsOptions();

      add_action('admin_menu', array($this, 'nativealerts_addAdminMenu'));
      add_action('admin_enqueue_scripts', array($this, 'nativealerts_enqueueAdminScripts'));
      add_action('wp_enqueue_scripts', array($this, 'nativealerts_enqueueClientScripts') );

      register_activation_hook( __FILE__, array($this, 'nativealerts_pushInstall'));
      register_deactivation_hook( __FILE__, array($this, 'nativealerts_pushUninstall'));

  }

  /*
  * refresh the properties with values that may have changed
  * @return N/A
  */
  public function refresh() {
      $self = self::$_instance;

      $self->apiDomain = get_option('nativealerts_push_api_domain');
      $self->siteId = get_option('nativealerts_push_site_id');
      $self->pubDomain = get_option('nativealerts_push_pub_domain');
  }

  /**
   * return this instance 
   * @return Object
   */
  public function getInstance() {
    if (!self::$_instance) {
      self::$_instance = new NativeAlertsPush();
    }
    return self::$_instance;
  }

  /*
   * return the options related to this plugin
   * @return Array
   */
  public function getNativeAlertsOptions() {
    return $this->nativealertsOptions;
  }

  /**
   * return the API Endpoint's full url path
   * if $hasSiteId is provided, it appends the site's id to the endpoint
   * 
   * @param string $path
   * @param string $hasSiteId
   * @return string
   */
  public function getApiUrl($path, $hasSiteId = true) {
    return $this->apiDomain . $path . ($hasSiteId ? $this->siteId : '');
  }

  /**
   * sets the plugin's options
   * 
   * @return N/A
   */
  private function setNativeAlertsOptions() {
    $this->nativealertsOptions = [
      'nativealerts_push_site_id' => [
        'name' =>'nativealerts_push_site_id',
        'desc' => 'Site Id',
        'isBlankOk' => false,
        'value' => isset($_POST['nativealerts_push_site_id']) ? sanitize_text_field($_POST['nativealerts_push_site_id']) : $this->siteId
      ],
      'nativealerts_push_api_login' => [
        'name' =>'nativealerts_push_api_login',
        'desc' => 'API Login',
        'isBlankOk' => true,
        'value' => isset($_POST['nativealerts_push_api_login']) ? sanitize_text_field($_POST['nativealerts_push_api_login']) : get_option('nativealerts_push_api_login')
      ],
      'nativealerts_push_api_password' => [
        'name' =>'nativealerts_push_api_password',
        'desc' => 'API Password',
        'isBlankOk' => true,
        'value' => isset($_POST['nativealerts_push_api_password']) ? sanitize_text_field($_POST['nativealerts_push_api_password']) : get_option('nativealerts_push_api_password')
      ],
      'nativealerts_push_ad_rate' => [
        'name' =>'nativealerts_push_ad_rate',
        'desc' => 'Ad Rate %',
        'isBlankOk' => true,
        'value' => isset($_POST['nativealerts_push_ad_rate']) ? sanitize_text_field($_POST['nativealerts_push_ad_rate']) : get_option('nativealerts_push_ad_rate')
      ],
      'nativealerts_push_api_domain' => [
        'name' =>'nativealerts_push_api_domain',
        'desc' => 'API Domain',
        'isBlankOk' => false,
        'value' => isset($_POST['nativealerts_push_api_domain']) ? sanitize_text_field($_POST['nativealerts_push_api_domain']) : $this->apiDomain
      ],
      'nativealerts_push_pub_domain' => [
        'name' =>'nativealerts_push_pub_domain',
        'desc' => 'Publisher Domain',
        'isBlankOk' => false,
        'value' => isset($_POST['nativealerts_push_pub_domain']) ? sanitize_text_field($_POST['nativealerts_push_pub_domain']) : $this->pubDomain        
      ]

    ];
  }

  /**
   * wrapper function for getting data from endpoints
   * 
   * @param string $url - the endpoint
   * @param string $method - POST | GET
   * @param boolean $decode - set to true to return the object JSON
   * @return Object | String
   */
  private function _getData($url, $method, $decode = true) {
    $token = $this->getToken();

    $headers = [
      'Content-Type'  => 'application/json',
      'token'         => $token
    ];

    $options = [
      'method'    => $method,
      'headers'   => $headers,
      'blocking'  => true
    ];

    $response = wp_remote_post($url, $options);
    $result = $response['body'];

    if ($decode) {
      $result = json_decode($result);
    } 
    return $result;
  }

  /**
   * retrieve the messages stats from its API endpoint
   * 
   * @return Object
   */
  public function getMessages() {
    $url = $this->getApiUrl(self::ENDPOINT_STATS_MESSAGES);
    return $this->_getData($url, 'GET', false);
  }

  /**
   * retrieve the unsent messages from its API endpoint
   * 
   * @return Object
   */
  public function getUnsentMessages() {
    $url = $this->getApiUrl(self::ENDPOINT_UNSENT_MESSAGES);
    return $this->_getData($url, 'GET');
  }

  /**
   * return the statistics related to Ads Push from its API endpoint
   * @return Object
   */
  public function getAdUserActivity() {
    $url = $this->getApiUrl(self::ENDPOINT_AD_USER_ACTIVITY);
    return $this->_getData($url, 'GET', false);
  }

  /**
   * return the statistics related to Ads Push, by device, from 
   * its API endpoint
   * 
   * @return Object
   */
  public function getAdStatsByDevice() {
    $url = $this->getApiUrl(self::ENDPOINT_AD_STATS_DEVICE);
    return $this->_getData($url, 'GET', false);
  }

  /**
   * returns the subcriber counts from its API endpoint
   * @return Object
   */
  public function getSubscriberCounts() {
    $url = $this->getApiUrl(self::ENDPOINT_SUBSCRIBER_COUNTS);
    return $this->_getData($url, 'GET', false);
  }

  /**
   * returns the permissions statistics. 
   * @return Object
   */
  public function getPermissionStats() {
    $url = $this->getApiUrl(self::ENDPOINT_PERMISSION_STATS);
    return $this->_getData($url, 'GET', false);
  }

  /**
   * returns the Ad Revenue Stats
   * @return Object
   */
  public function getAdRevenueStats() {
    $url = $this->getApiUrl(self::ENDPOINT_AD_REVENUE_STATS);
    return $this->_getData($url, 'GET', false);
  }

  /**
   * returns the target platforms for the Push Message
   * 
   * @return Object
   */
  public function getPlatforms() {
    $url = $this->getApiUrl(self::ENDPOINT_PLATFORMS, false);
    return $this->_getData($url, 'GET');
  }

  /**
   * returns the target segments for the Push Message
   * @return Object
   */
  public function getUserSegments() {
    $url = $this->getApiUrl(self::ENDPOINT_USER_SEGMENTS, false);
    return $this->_getData($url, 'GET');
  }

  /**
   * returns the publisher's profile
   * 
   * @return Object
   */
  public function getProfile() {
    $url = $this->getApiUrl(self::ENDPOINT_PROFILE);
    $result = $this->_getData($url, 'GET'); 
    return $result->data;
  }

  /**
   * checks if the profile settings are correct
   * 
   * @return boolean
   */
  public function isValidProfile() {
    $profile = $this->getProfile();
    if ($profile && $profile->domain_name) {
      return ($profile->domain_name == $this->pubDomain);
    } else {
      return false;
    }
  }

  /**
   * checks the current profile settings and
   * returns the appropriate message based on profile's validity
   * 
   * @return string
   */
  public function getCurrentSettingsStatus() {
    if (get_option('nativealerts_push_site_id') == '') {
      include NATIVEALERTS_PUSH_PATH . './includes/status/setup_required_message.php';
    }

    if ($this->isValidProfile()) {
      return '';
    } 

    include NATIVEALERTS_PUSH_PATH . './includes/status/invalid_settings.php';
  }

  /**
   * sends the Push Message record to the API where it is saved 
   * 
   * @param string $message
   * @return string
   */
  public function sendMessage($message) {
    $token = $this->getToken();
    if ($token === null) {
      return 'Could not send message. No valid token.';
    }

    $messageUrl = $this->apiDomain . '/messages';

    $payload = json_encode([
      'title'           => $message['title'],
      'message'         => $message['message'],
      'click_url'       => $message['click_url'],
      'icon_url'        => $message['icon_url'],
      'image_url'       => $message['image_url'],
      'send_date_time'  => $message['schedule'],
      'platforms'       => $message['platforms'],
      'user_segments'   => $message['segments'],
      'site_id'         => $this->siteId
    ]);

    $headers = [
      'Content-Type'    => 'application/json',
      'Content-Length'  => strlen($payload),
      'token'           => $token
    ];

    $options = [
      'method'    => 'POST',
      'headers'   => $headers,
      'body'      => $payload,
      'blocking'  => true
    ];

    $response = wp_remote_post($messageUrl, $options);
    $obj = json_decode($response['body']);
    if ($obj->success) {
      return 'Message was saved and scheduled for delivery.';
    } else {
      return 'Message was not saved. Check that all fields are filled out.';
    }
  }

  /**
   * deletes a Push Message record via the API
   * 
   * @param int $messageId
   * @return string
   */
  public function deleteMessage($messageId) {
    $token = $this->getToken();
    if ($token === null) {
      return 'Could not send message. Could not acquire token.';
    }

    $messageUrl = $this->apiDomain . '/messages';
    $payload = json_encode([
      'site_id' => $this->siteId,
      'id'      => $messageId
    ]);

    $headers = [
      'Content-Type'    => 'application/json',
      'Content-Length'  => strlen($payload),
      'token'           => $token
    ];

    $options = [
      'method'    => 'DELETE',
      'headers'   => $headers,
      'body'      => $payload,
      'blocking'  => true
    ];

    $response = wp_remote_post($messageUrl, $options);
    $obj = json_decode($response['body']);
    if ($obj->success) {
      return '';
    } else {
      return 'Delete failed.';
    }
  }

  /**
   * retrieves the token from the API using the
   * credentials saved in the profile
   * 
   * @return Object|NULL
   */
  public function getToken() {

    $loginUrl = $this->apiDomain . '/login';

    $credentials = json_encode([
      'login' => get_option('nativealerts_push_api_login'),
      'password' => get_option('nativealerts_push_api_password')
    ]);

    $headers = [
      'Content-Type'    => 'application/json',
      'Content-Length'  => strlen($credentials)
    ];

    $options = [
      'method'    => 'POST',
      'headers'   => $headers,
      'body'      => $credentials,
      'blocking'  => true
    ];

    $response = wp_remote_post($loginUrl, $options);

    $obj = json_decode($response['body']);

    if ($obj->success) {
      return $obj->data->token;
    } else {
      return null;
    }
  }

  /**
   * get the last 10 published posts
   * @optional @param num int // defaults to 10, max at 25
   * 
   * 
   * @return Array | boolean
   */
  public function getRecentPosts(int $num = 10) {
    if ($num > 25) {
      return 25;
    }
    $args = [
      'numberposts' => $num,
      'offset' => 0,
      'category' => 0,
      'orderby' => 'post_date',
      'order' => 'DESC',
      'include' => '',
      'exclude' => '',
      'meta_key' => '',
      'meta_value' =>'',
      'post_type' => 'post',
      'post_status' => 'publish',
      'suppress_filters' => true
    ];
    
    $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
    return $recent_posts;
  }

  /**
   * add the client script
   * @return N/A
   */
  function nativealerts_enqueueClientScripts() {
    wp_enqueue_script( 'nativealerts_push_client', $this->apiDomain . '/js/client/wp/' . $this->siteId . '.js');
  }

  /**
   * Admin links
   * 
   * @return N/A
   */
  function nativealerts_addAdminMenu() {
        add_menu_page( 'NativeAlerts Push', 'NativeAlerts Push', 'manage_options', 'nativealerts-push-compose', array(
                          __CLASS__,
                         'nativealerts_push_page_file_path'
                        ));

        add_submenu_page( 'nativealerts-push-compose', 'NativeAlerts Push' . ' Compose', 'Compose', 'manage_options', 'nativealerts-push-compose', array(
                              __CLASS__,
                             'nativealerts_push_compose_file_path'
                            ));

        add_submenu_page( 'nativealerts-push-compose', 'NativeAlerts Push' . ' Push Stats', 'Push Stats', 'manage_options', 'nativealerts-push-messages', array(
                              __CLASS__,
                             'nativealerts_push_messages_file_path'
                            ));

        add_submenu_page( 'nativealerts-push-compose', 'NativeAlerts Push' . ' Ads Stats', ' Ads Stats', 'manage_options', 'nativealerts-push-dashboard', array(
                              __CLASS__,
                             'nativealerts_push_dashboard_file_path'
                            ));

        add_submenu_page( 'nativealerts-push-compose', 'NativeAlerts Push' . ' Settings', 'Settings', 'manage_options', 'nativealerts-push-settings', array(
                              __CLASS__,
                             'nativealerts_push_settings_file_path'
                            ));
  }

  /**
   * enqueue admin-specific scripts
   * 
   * @return N/A
   */
  function nativealerts_enqueueAdminScripts() {
    // js scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('nativealerts_abpaginator', plugins_url('js/abpaginator.min.js', __FILE__));
    wp_enqueue_script('nativealerts_abutils', plugins_url('js/abutils.min.js', __FILE__));

    // styles
    wp_enqueue_style('nativealerts_default_style', plugins_url( 'css/styles.css' , __FILE__ ));
    wp_enqueue_style('nativealerts_jquery_ui_css', plugins_url( 'css/jquery-ui.min.css' , __FILE__ ));

  }

  function adminLayout() {
    echo 'Admin Options for NativeAlerts Push Notifications';
  }

  /**
   * main menu
   */
  function nativealerts_push_page_file_path() {
  }

  /**
   * Dashboard submenu
   */
  function nativealerts_push_dashboard_file_path() {
    include 'includes/nativealerts-push-dashboard.php';
  }

  /**
   * Settings submenu
   */
  function nativealerts_push_settings_file_path() {
    include 'includes/settings.php';
  }

  /**
   * Ad Messages submenu
   */
  function nativealerts_push_messages_file_path() {
    include 'includes/messages-tab.php';
  }

  /**
   * Compose Message submenu
   */
  function nativealerts_push_compose_file_path() {
    include 'includes/compose-new-message.php';
  }


  /**
   * default installation options
   */
  function nativealerts_pushInstall() {
    update_option('nativealerts_push_api_domain', 'https://api.nativealerts.com');
  }

  /**
   * uninstall plugin options
   */
  function nativealerts_pushUninstall() {
    // REMOVE ALL NATIVEALERTS PUSH OPTIONS
    $nativealertsOptions = $this->getNativeAlertsOptions();
    foreach ($nativealertsOptions as $key => $value) {
      delete_option($key);
      delete_site_option($key);
    }
  }

  /**
   * format date to YYYY-MM-DD HH:MM
   * 
   * @param int $datenum
   * @return string
   */
  static function formatYMD($datenum) {
    return date('Y-m-d h:i A', substr($datenum, 0, 10));
  }

  static function getHourSelect($value) {
    $html = '<select name="nativealerts_push_schedule_hour" id="nativealerts_push_schedule_hour">';
    for ($i = 1; $i < 13; $i++) {
      $hour = ($i < 10 ? '0' : '') . (string) $i;
      $html .= sprintf('<option value="%s" %s>%s</option>', 
        $hour, 
        ($value == $hour ? 'selected' : ''), 
        $hour);
    }
    $html .= '</select>';
    return $html;
  }

  static function getMinuteSelect($value) {
    $html = '<select name="nativealerts_push_schedule_minute" id="nativealerts_push_schedule_minute">';
    for ($i = 0; $i < 59; $i+=5) {
      $minute = ($i < 10 ? '0' : '') . (string) $i;
      $html .= sprintf('<option value="%s" %s>%s</option>', 
        $minute, 
        ($value == $minute ? 'selected' : ''), 
        $minute);
    }
    $html .= '</select>';
    return $html;
  }

  static function getAMPM($value) {
    $html = '<select name="nativealerts_push_schedule_part" id="nativealerts_push_schedule_part">';
    $parts = ['AM', 'PM'];
    foreach ($parts as $part) {
      $html .= sprintf('<option value="%s" %s>%s</option>', $part, ($value == $part ? 'selected' : ''), $part);
    }
    $html .= '</select>';
    return $html;
  }

  static function formatSchedule($date, $hour, $minute, $part) {
    if ($part == 'PM' && (int) $hour < 12) {
      $hour = (int) $hour;
      $hour += 12;
      $hour = (string) $hour;
    } else if ($part == 'AM' && (int) $hour == 12) {
			$hour = '00';
		}

    $result = sprintf('%s %s:%s:00',
      $date,
      $hour,
      $minute);
    return $result;
  }
}

// initiate the instance
NativeAlertsPush::getInstance();
