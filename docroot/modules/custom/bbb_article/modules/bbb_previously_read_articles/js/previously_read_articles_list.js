/**
 * @file
 * previously_read_articles_list.js
 */

(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.previouslyReadList = {
    attach: function (context, settings) {
      // Fetch articles from local storage.
      let articles_list = JSON.parse(window.localStorage.getItem("article_list"));
      var template = document.querySelector('#prev-read-row');
      var current_node = drupalSettings.current_node_uuid;
      var bundle = drupalSettings.bundle;
      if (articles_list) {
        let site = Object. keys(articles_list)[0];
        let articles_list_site = articles_list[site];
        let reverse_articles_list = obj_reverse(articles_list_site);

        let i = 0;
        for (key in reverse_articles_list) {
          // added a check to remove currently viewed article from the list of previously viewed articles.
          if ((Object.keys(reverse_articles_list).length === 1) && (key == current_node)) {
            document.querySelector('.prev-read-wrapper').remove();
          }
          if (key == current_node) {
            continue;
          }
          var clone = template.content.cloneNode(true);
          var prev_list = clone.querySelector('.prev-read-li-wrapper .prev-read-article .prev-read-link');
          var prev_text = clone.querySelector('.prev-read-li-wrapper .prev-read-article .prev-read-text');
          prev_list.innerHTML = "<span class='visually-hidden'>" + Drupal.t('Read article') + "</span>" + "<span property='headline'>" +reverse_articles_list[key]['article_title'] + "</span>";
          prev_list.setAttribute('href', reverse_articles_list[key]['article_url']);
          prev_text.textContent = bundle;

          i++;
          let itemMeta = clone.querySelector('li meta');
          itemMeta.setAttribute("content", i);
          template.parentNode.appendChild(clone);
        }
        let meta = document.querySelector('.prev-read-ul meta');
        meta.setAttribute("content", i);
      }
      // Remove the dummy template code added.
      template.remove();
    }
  };

  function obj_reverse(obj) {
    new_obj= {}
    rev_obj = Object.keys(obj).reverse();
    rev_obj.forEach(function(i) {
      new_obj[i] = obj[i];
    })
    return new_obj;
  }
})(jQuery, Drupal, drupalSettings);
