/**
 * @file
 * previously_read_articles.js
 */

 (function ($, Drupal, drupalSettings) {
  Drupal.behaviors.FAQQuestionList = {
    attach: function (context, settings) {
      var faqbodytxt = document.getElementsByClassName("coh-faq-tabs-content");
      for (var i = 0; i < faqbodytxt.length; i++) {
        faqbodytxt[i].style.display = 'none';
      }

      const faqtitlelink = document.querySelectorAll('.coh-faq-link');

      faqtitlelink.forEach(box => {
        box.addEventListener('click', function handleClick(event) {
          event.preventDefault();
          if(box.parentNode.classList.contains('is-active')){
            box.parentNode.classList.remove("is-active");
            box.parentNode.nextElementSibling.style.display = 'none';
            box.setAttribute("aria-expanded", "false");
          } else {
            box.parentNode.classList.add("is-active");
            box.parentNode.nextElementSibling.removeAttribute('style');
            box.setAttribute("aria-expanded", "true");
          }
        });
      });

    }
  };
})(jQuery, Drupal, drupalSettings);
