/**
 * @file
 * mobile_pager.js
 */

(function (Drupal) {
  Drupal.behaviors.pagerMobile = {
    attach: function (context, settings) {

      // Trigger previous button.
      let prev = document.querySelector('.pager-mobile > .pager__item--previous a');
      if (prev != null) {
        prev.addEventListener("click", function (e) {
          e.preventDefault();
          document.querySelector("nav.pager .pager__item--previous a").click();
        });
      }
      // Trigger next button.
      let next = document.querySelector('.pager-mobile > .pager__item--next a');
      if (next != null) {
        next.addEventListener("click", function (e) {
          e.preventDefault();
          document.querySelector("nav.pager .pager__item--next a").click();
        });
      }
      // Navigate to selected page from dropdown
      let dropdown = document.getElementById("pager__mobile_dropdown");
      if (dropdown != null) {
        dropdown.onchange = function (evt) {
          document.querySelector("nav.pager .pager__items .pager__item a[href='" + evt.target.value + "']").click();
        };
      }
    }
  };

})(Drupal);
