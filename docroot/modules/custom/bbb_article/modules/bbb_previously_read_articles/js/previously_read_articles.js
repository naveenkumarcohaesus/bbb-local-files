/**
 * @file
 * previously_read_articles.js
 */

 (function ($, Drupal, drupalSettings) {
  Drupal.behaviors.previouslyRead = {
    attach: function (context, settings) {
      let site = drupalSettings.host;
      let articles = drupalSettings.article_details;
      // Fetch articles from local storage.
      let existing_articles = JSON.parse(window.localStorage.getItem("article_list"));
      // If article present in localstorage then update it else add it to localstorage.
      if (existing_articles) {
        let article_key = Object.keys(articles[site])[0];
        let existing_articles_site = existing_articles[site];
        var key_check = article_key in existing_articles_site; 
        if (key_check === true) {
          delete existing_articles_site[article_key];
          existing_articles[site] = existing_articles_site;
          Object.assign(existing_articles[site], articles[site]);
          window.localStorage.setItem("article_list", JSON.stringify(existing_articles));
        } else {
          let existing_articles_count = Object.keys(existing_articles[site]).length;
          // If total no. articles in local storage are less than 4 then insert new article at the end of list, else remove one article from top of list and then insert latest article.
          if (existing_articles_count < 4) {
            Object.assign(existing_articles[site], articles[site]);
            window.localStorage.setItem("article_list", JSON.stringify(existing_articles));
          } else {
            let keys = Object.keys(existing_articles_site);
            delete existing_articles_site[keys[0]];
            existing_articles[site] = existing_articles_site;
            Object.assign(existing_articles[site], articles[site]);
            window.localStorage.setItem("article_list", JSON.stringify(existing_articles));
          }
        }
      } else {
        window.localStorage.setItem("article_list", JSON.stringify(articles));
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
