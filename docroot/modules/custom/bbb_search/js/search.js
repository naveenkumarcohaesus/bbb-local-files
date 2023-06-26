/**
 * @file
 * search.js
 */

(function (Drupal, drupalSettings) {
   Drupal.behaviors.search = {
     attach: function (context, settings) {
      jQuery(document).on("keypress", function(event) {
         console.log('heloo');
         console.log(event.keyCode);
         if (event.keyCode == 32) {   //tab pressed
            console.log('heloo123');
             return false; // stops its action
         }
      });
     }
   };
 
 })(Drupal, drupalSettings);