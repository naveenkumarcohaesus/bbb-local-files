/**
 * @file
 * case_study_filters.js
 */

(function (Drupal, drupalSettings) {
  Drupal.behaviors.listingPageFilters = {
    attach: function (context, settings) {

      // Close filter dropdown when clicked anywhere outside in DOM.
      let details = [...document.querySelectorAll('details')];
      document.addEventListener('click', function (e) {
        if (!details.some(f => f.contains(e.target))) {
          details.forEach(f => f.removeAttribute('open'));
        } else {
          details.forEach(f => !f.contains(e.target) ? f.removeAttribute('open') : '');
        }
      });

      // Mobile filter append for tab/mobile
      function mobileResultAdd() {
        if(!document.querySelector('.sm-results-wrapper')){
          if(document.querySelector("#mobile-results")){
            const sm_org_html = document.getElementById("mobile-results").outerHTML;
            const sm_new_html = "<div id='sm-results-wrapper' class='sm-results-wrapper'>" + sm_org_html + "</div>";
            document.getElementById("mobile-results").outerHTML = sm_new_html;
          } else if (document.getElementsByClassName("sm-results").length){
            const sm_org_htmls = document.querySelector(".sm-results").outerHTML;
            const sm_new_htmls = "<div id='sm-results-wrapper' class='sm-results-wrapper'>" + sm_org_htmls + "</div>";
            document.querySelector(".sm-results").outerHTML = sm_new_htmls;
          }
        } 
        // Add reset button
        setTimeout(() => {
          if(document.querySelector('.form-type-checkbox.highlight')) {
            let sm_clear_result = document.createElement("input");
            sm_clear_result.setAttribute('type', 'button');
            sm_clear_result.setAttribute('name', 'reset');
            sm_clear_result.setAttribute('value', 'Reset all filters');
            sm_clear_result.setAttribute('class', 'sm-reset-button');
            if(!document.querySelector('.sm-reset-button')){
              document.getElementById("sm-results-wrapper").append(sm_clear_result);
            }

            // foreach function for apply filter number
            const smFilterItem = document.querySelectorAll('.dx8-details-content');
            smFilterItem.forEach(box => {
              let itemLength = box.getElementsByClassName('highlight').length;
              if(itemLength > 0 && !box.previousElementSibling.querySelector('.append-number')) {
                createFilterWrapper = document.createElement("span");
                createFilterWrapper.innerHTML = itemLength + ' applied';
                createFilterWrapper.classList.add('append-number');
                box.previousElementSibling.append(createFilterWrapper);
              } 
            });
          }

          // filter button target to clear
          let targetbtnfilter = document.querySelector('.sm-reset-button');
          let targetbtnfilterLength = document.querySelectorAll('.sm-reset-button').length;
          if(targetbtnfilterLength){
            targetbtnfilter.addEventListener('click', function (e) {
              e.preventDefault();
              let targetFilterInput = document.querySelector('.filtered-tags-wrapper input:last-child');
              targetFilterInput.click();
            });
          }
        }, 100);
      }

      // Filter heading click event to open mobile full screen.
      const filterHeading = document.querySelector('.sm-filter-heading');
      if (typeof (filterHeading) != 'undefined' && filterHeading !== null) {
        filterHeading.addEventListener('click', function () {
          document.querySelector('.sm-filter-heading-label').style.display = 'flex';
          document.querySelector('.sm-results').style.display = 'block';
          document.querySelector('.close-filters').style.display = 'flex';
          document.querySelector('.filters-wrapper').style.display = 'block';
          document.querySelector('.filter-details-wrapper').style.display = 'block';
          document.querySelector('.mobile-full-screen-view').classList.add('fullscreen');

          // Set session storage flag to auto open post AJAX.
          sessionStorage.setItem('mobileFilterScreen', 'open');

          // Add filter option/CTA to add mobile panel
          mobileResultAdd();

        });
      }

      // Filter close click event to close mobile full screen.
      const close = document.querySelector('.close-filters');
      if (typeof (close) != 'undefined' && close !== null) {
        close.addEventListener('click', function () {
          document.querySelector('.sm-filter-heading-label').style.display = 'none';
          document.querySelector('.sm-results').style.display = 'none';
          document.querySelector('.filter-details-wrapper').style.display = 'none';
          document.querySelector('.mobile-full-screen-view').classList.remove('fullscreen');

          // Remove the session storage flag.
          sessionStorage.removeItem('mobileFilterScreen');
          close.style.display = 'none';
        });
      }

      // Sort filters, trigger click on sort by.
      let sortBy = document.querySelector('.sort-filters select[name=sort_by]');
      let sortFilter = document.querySelector('.views-list-content-container form select[name="sort_by"]');
      if (sortFilter === null) {
        sortFilter = document.querySelector('.views-list-content-container form select[name="sort_bef_combine"]');
      }
      if (sortFilter !== null) {
        sortBy.value = sortFilter.value;
        sortBy.addEventListener('change', function (e) {
          sortFilter.value = this.value;
          // Trigger change event for sort filters.
          document.querySelector('.views-list-content-container .form-actions input:first-child').click();
        });
      }

      // Filter pills.
      const filteredTags = document.querySelector('.filtered-tags');
      const filteredTagsWrapper = document.querySelector('.filtered-tags-wrapper');
      // Intially hide the markup as it is empty.
      filteredTagsWrapper.classList.add('visually-hidden');
      filteredTagsWrapper.querySelector('input[type="submit"]').classList.add("js-hide");

      let ul = document.createElement('ul');
      let li;
      let filtersList = document.querySelectorAll(".filters-wrapper details");
      let removeFilter = sessionStorage.getItem('removeFilter');
      filtersList.forEach((item) => {
        item.removeAttribute("open");
        const inputAll = item.querySelectorAll('input[type=checkbox]');

        inputAll.forEach((checkbox) => {
          checkbox.setAttribute('role', 'checkbox');
          if (checkbox.checked === true) {
            checkbox.setAttribute('aria-checked', 'true');
            let text = checkbox.nextElementSibling.textContent;
            let id = checkbox.getAttribute('id');
            // Create a new list item with the active filter.
            li = document.createElement('li');
            let spanLabel = document.createElement('span');
            spanLabel.textContent = text;
            spanLabel.classList.add('tag');
            spanLabel.classList.add('coh-style-label-small');
            li.appendChild(spanLabel);
            // Create a close button.
            let span = document.createElement('span');
            span.classList.add("close");
            span.textContent = "close";
            span.setAttribute('aria-label', 'remove filter ' + text);
            span.setAttribute('tabindex', '0');
            span.setAttribute('role', 'button');
            // Add click event for the close button to remove filter.
            span.addEventListener('click', function () {
              // Set session storage to know user has clicked on the pills.
              sessionStorage.setItem('removeFilter', 'removeFilter');
              item.querySelector('#' + id).click();
            });
            // Add keyboard support for the close button to remove filter.
            span.addEventListener('keydown', function(e) {
              if (e.keyCode == 13) {
                item.querySelector('#' + id).click();
              }
            });

            li.appendChild(span);
            ul.appendChild(li);
          }
          else {
            checkbox.setAttribute('aria-checked', 'false');
          }

          checkbox.addEventListener('click', function (e) {
            sessionStorage.setItem('clickedInput', this.getAttribute('name'));
            sessionStorage.setItem('filterId', this.getAttribute('data-drupal-selector'));
          });
        });

        var clicked = sessionStorage.getItem('clickedInput');
        if (typeof(clicked) != 'undefined' && clicked !== null) {
          const clickedInput = item.querySelector('input[name="'+ clicked +'"]');
          if (typeof(clickedInput) != 'undefined' && clickedInput !== null) {
            if (clickedInput) {
              // Open dropdown only when checkbox is clicked.
              if (typeof (removeFilter) != 'string' && removeFilter !== 'removeFilter') {
                item.setAttribute("open", "open");
              }
              // Remove the session storage flag.
              sessionStorage.removeItem('clickedInput');
              sessionStorage.removeItem('removeFilter');
            }
          }
        }


        // Add focus again as now filter element is generated again post ajax.
        var filterId = sessionStorage.getItem('filterId');
        if (typeof(filterId) != 'undefined' && filterId !== null) {
          const checkboxElm = item.querySelector('input[data-drupal-selector="'+ filterId +'"]');
          if (typeof(checkboxElm) != 'undefined' && checkboxElm !== null) {
            if (checkboxElm) {
              // Add focus.
              //
              // We need this to be staggered very slightly to avoid earlier
              // JS which opens the dropdown post ajax to get completed
              // and also the dropdown to be rendered
              // Without this, the JS has a race condition and executes before
              // the element is visible.
              setTimeout(() => {
                checkboxElm.focus();
              }, 60);
              // Remove the session storage.
              sessionStorage.removeItem('filterId');
            }
          }
        }
      });

      // Add new filter list if it doesnt exist.
      if (ul.childElementCount > 0 && document.querySelectorAll('.filtered-tags > ul').length === 0) {
        filteredTags.appendChild(ul);
        filteredTagsWrapper.classList.remove('visually-hidden');
      }
      // Remove existing filter list and then add new.
      else {
        if (ul.childElementCount > 0 && document.querySelectorAll('.filtered-tags > ul').length > 0) {
          // Cleaning up.
          document.querySelector('.filtered-tags ul').remove()
          filteredTags.appendChild(ul);
          filteredTagsWrapper.classList.remove('visually-hidden');
        }
      }

      // Wrap all details elements in a div.
      if (document.querySelectorAll('.filter-details-wrapper').length === 0){
        var newElem = document.createElement('div');
        newElem.classList.add('filter-details-wrapper');
        let count = 0;
        Array.prototype.forEach.call(document.querySelectorAll('.filters-wrapper details'), function (c) {
          newElem.appendChild(c);
          if (c.getAttribute('data-drupal-selector') !== 'edit-field-partner-collapsible') {
            count++;
          }
        });

        newElem.classList.add('grid-' + count);
        document.querySelector('.filters-wrapper form').prepend(newElem);
      }

      // Prepare markup for mobile full screen view.
      if (document.querySelectorAll('.mobile-full-screen-view').length === 0){
        var fullScreen = document.createElement('div')
        let mobileHeader = document.querySelector('.view-mobile-filter-header');
        if (typeof (mobileHeader) != 'undefined' && mobileHeader !== null) {
          fullScreen.classList.add('mobile-full-screen-view');
          document.querySelector('.filters-wrapper form').prepend(fullScreen);
          fullScreen.appendChild(document.querySelector('.filter-details-wrapper'));
          // Add the mobile header to this full screen.
          fullScreen.prepend(document.querySelector('.view-mobile-filter-header'));
        }
      }

      // Set session storage flag to auto open post AJAX.
      var flag = sessionStorage.getItem('mobileFilterScreen');
      if (flag === 'open') {
        const filterLabel = document.querySelector('.sm-filter-heading');
        filterLabel.click();
      }

      // Check for no results.
      let noResults = document.querySelector('.no-results');
      if (typeof(noResults) != 'undefined' && noResults !== null && noResults.innerHTML.trim().length !== 0) {
        let resultCount = document.querySelector('.view-header #results');
        if (resultCount !== null) {
          resultCount.classList.add('no-results-count');
          resultCount.innerHTML = Drupal.t('Showing 0 results');
        }
      }
    }
  };

})(Drupal, drupalSettings);
