/**
 * @file
 * case_study_filters.js
 */

(function (Drupal, drupalSettings) {
  Drupal.behaviors.activeBBBlinks = {
    attach: function (context, settings) {
      let activeElements = context.querySelectorAll("nav a.is-active");
      if (typeof (activeElements) != 'undefined' && activeElements !== null) {
        activeElements.forEach((item) => {
          item.setAttribute('aria-current', 'page');
        });
      }
    }
  };

})(Drupal, drupalSettings);
