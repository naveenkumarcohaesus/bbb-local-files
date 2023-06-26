/**
 * @file
 * logo-list.js
 */

(function (Drupal, drupalSettings) {
  Drupal.behaviors.logoList = {
    attach: function (context, settings) {

      // Close any active panel.
      const closeActivePanel = () => {
        Array.from(document.querySelectorAll('.grid-layout .views-row.taxonomy-term.is-selected')).forEach(function (element) {
          element.classList.remove('is-selected');
        });
      };

      // Close Panel handler.
      const panelCloseHandler = (e) => {
        e.preventDefault();
        const parentNode = e.target.parentNode;
        const termPanelId = parentNode.getAttribute('data-overlay-for');
        if (termPanelId !== 'null') {
          const termPanel = document.getElementById(termPanelId);
          termPanel.classList.remove('is-selected');
        }
      };

      // Open Panel handler.
      const panelOpenHandler = (e) => {
        e.preventDefault();
        // Check if this panel is already open.
        if (e.target.parentNode.classList.contains('is-selected')) {
          // Close panel if already open.
          e.target.parentNode.classList.remove('is-selected');
        }
        else {
          // Close any active panel.
          closeActivePanel();
          e.target.parentNode.classList.add('is-selected');
        }
      };

      // Add click event support for opening panel.
      Array.from(document.querySelectorAll('.grid-layout .views-row.taxonomy-term:not(.no-accordion) .logo')).forEach(function (element) {
        element.addEventListener('click', (e) => {
          panelOpenHandler(e);
        });

        element.addEventListener('keydown', (e) => {
          if (event.keyCode === 13) {
            panelOpenHandler(e);
          }
        });
      });

      // Add click event for closing panel.
      Array.from(document.querySelectorAll('.views-row-overlay .close-overlay')).forEach(function (element) {
        element.addEventListener('click', (e) => {
          panelCloseHandler(e);
        });

        element.addEventListener('keydown', (e) => {
          if (event.keyCode === 13) {
            panelCloseHandler(e);
          }
        });
      });
    }
  };

})(Drupal, drupalSettings);
