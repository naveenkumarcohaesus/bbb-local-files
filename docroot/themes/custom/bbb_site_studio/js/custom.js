/**
 * @file
 * custom.js
 */

(function (Drupal, drupalSettings) {
  Drupal.behaviors.custom = {
    attach: function (context, settings) {

      // Replace class for article listing block for width
      document.getElementById("views-exposed-form-article-article-listing-block").children[0].children[1].classList.replace('grid-2','grid-3');      

      // Filter close after lost focus 
      document.querySelector("details").addEventListener('focus', function() { 
        document.querySelector("div.dx8-details-content").style.display = 'block';
      }, true );
      document.querySelector("div.bef-checkboxes div:last-child").addEventListener('blur', function() { 
        document.querySelector("div.dx8-details-content").style.display = 'none';
      }, true );
      document.querySelector("div.filtered-tags").addEventListener('focus', function() { 
        document.querySelector("div.dx8-details-content").style.display = 'none';
      }, true );

    }
  };

})(Drupal, drupalSettings);