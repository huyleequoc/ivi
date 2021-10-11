<?php
/*
 * Google Analytics Integration
 * 
 * @1.1.3
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
   exit;

add_filter( 'wp_footer', 'quads_ad_blocker_script', 100 );
add_filter( 'wp_footer', 'quads_analytics', 101 );
add_filter( 'wp_footer', 'quads_show_adblocker_notice',110 );

/**
 * Create ad blocker main code
 * 
 * @return mixed string|bool false
 */
function quads_ad_blocker_script() {
   global $quads_options;

   if( quads_is_amp_endpoint() ) {
      return false;
   }

   // Load either way
   if( isset( $quads_options['analytics'] ) || isset( $quads_options['ad_blocker_message'] ) ) {
      //echo '<script src="' . QUADS_PRO_PLUGIN_URL . 'assets/js/ads.js" type="text/javascript"></script>';
      ?>
      <script type="text/javascript">
         if (typeof wpquads_adblocker_check === 'undefined') {
             wpquads_adblocker_check = false;
         } else {
             wpquads_adblocker_check = true;
         }
      </script>
      <?php
   }
}

/**
 * Add ads.js adblocker detection
 */
function quads_add_script()
{
    wp_enqueue_script( 'quads-ad-ga', QUADS_PRO_PLUGIN_URL . 'assets/js/ads.js', array('jquery'), QUADS_VERSION, false ); 
}
add_action( 'wp_enqueue_scripts', 'quads_add_script', 10 );

/**
 * Create GA event code
 * 
 * @global array $quads_options

 * @return mixed string|bool false
 */
function quads_analytics() {
   global $quads_options;

   if( quads_is_amp_endpoint() || !isset( $quads_options['analytics'] ) ) {
      return false;
   }
   ?>
   <script type="text/javascript">
      if (typeof ga !== 'undefined' && wpquads_adblocker_check === false) {
          ga('send', 'event', 'Blocking Ads', 'true', {'nonInteraction': true});
      } else if (typeof _gaq !== 'undefined' && wpquads_adblocker_check === false) {
          _gaq.push(['_trackEvent', 'Blocking Ads', 'true', undefined, undefined, true]);
      }
   </script>
   <?php
}

/**
 * Create ad blocker notice script
 * 
 * @global array $quads_options
 * @return mixed string|boolean false
 */
function quads_show_adblocker_notice() {
   global $quads_options;

   if( quads_is_amp_endpoint() || !isset( $quads_options['ad_blocker_message'] ) ) {
      return false;
   }
   ?>
   <!--noptimize--><style>.quads-highlight-adblocked { outline:4px solid #ef4000;background-color:#ef4000;color:#ffffff;text-align: center;display:block;}.quads-highlight-adblocked:after {content:'Please allow this ad by disabling your ad blocker';font-size: 0.8em; display:inline-block;}</style>
   <script type="text/javascript">
      (function (d, w) {

          var addEvent1 = function (obj, type, fn) {
              if (obj.addEventListener)
                  obj.addEventListener(type, fn, false);
              else if (obj.attachEvent)
                  obj.attachEvent('on' + type, function () {
                      return fn.call(obj, window.event);
                  });
          };

          function highlight_adblocked_ads() {
              try {
                  var ad_wrappers = document.querySelectorAll('div[id^="quads-ad"]')
              } catch (e) {
                  return;
              }
              for (i = 0; i < ad_wrappers.length; i++) {
                  ad_wrappers[i].className += ' quads-highlight-adblocked';
                  ad_wrappers[i].setAttribute('style', 'display:block !important');
              }
          }

          addEvent1(w, 'load', function () {
              if (wpquads_adblocker_check === undefined || wpquads_adblocker_check === false) {
                  highlight_adblocked_ads();
              }
          });

      })(document, window);
   </script>
   <?php
}
?>