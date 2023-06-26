/**
 * @file
 * alert_banner.js
 */

(function (Drupal, drupalSettings) {
  Drupal.behaviors.alertBanner = {
    attach: function (context, settings) {

      const alertBanner = document.querySelector('.alert-banner');
      // Add & Remove class when banner exist on the page.
      if (typeof (alertBanner) != 'undefined' && alertBanner !== null) {
        alertBanner.classList.remove('visually-hidden');
        document.body.classList.add('alert-banner-on');
      }
      else {
        document.body.classList.remove('alert-banner-on');
      }

      // Add & Remove class when Menu opens So that change the position of Header.
      if (document.querySelectorAll('.menu-open').length) {
        document.body.classList.add('menu-active');
      }
      else {
        document.body.classList.remove('menu-active');
      }

      function removeAlert() {
        if (typeof (alertBanner) != 'undefined' && alertBanner !== null) {
          alertBanner.remove();
          document.body.classList.remove('alert-banner-on');
        }
      }

      let uuid = document.querySelector('.block-content').parentElement.getAttribute('data-uuid');

      if (window.localStorage.getItem(uuid)) {
        removeAlert();
      }

      // Hide the Banner on clicking of close icon
      document.querySelectorAll('.alert-banner').forEach(function (element) {
        let close = document.querySelector('.alert-close-icon');

        ['click', 'keypress'].forEach((e) => {
          close.addEventListener(e, closeAlert)
        });

        function closeAlert(e) {
          // Save on LocalStorage
          window.localStorage.setItem(uuid, true);
          removeAlert();
          if (e.which == '13' || e.which == '32') {
            removeAlert();
          }
        }
      });
    }
  };

})(Drupal, drupalSettings);
