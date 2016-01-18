/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function ($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function () {
        // JavaScript to be fired on all pages
        //

        // Morphing Search
        //
        (function () {
          var morphSearch = document.getElementById('morphsearch'),
            input = morphSearch.querySelector('input.morphsearch-input'),
            ctrlClose = morphSearch.querySelector('span.morphsearch-close'),
            isOpen = isAnimating = false,
          // show/hide search area
            toggleSearch = function (evt) {
              // return if open and the input gets focused
              if (evt.type.toLowerCase() === 'focus' && isOpen) return false;

              var offsets = morphsearch.getBoundingClientRect();
              if (isOpen) {
                classie.remove(morphSearch, 'open');

                // trick to hide input text once the search overlay closes
                // todo: hardcoded times, should be done after transition ends
                if (input.value !== '') {
                  setTimeout(function () {
                    classie.add(morphSearch, 'hideInput');
                    setTimeout(function () {
                      classie.remove(morphSearch, 'hideInput');
                      input.value = '';
                    }, 300);
                  }, 500);
                }

                input.blur();
              }
              else {
                classie.add(morphSearch, 'open');
              }
              isOpen = !isOpen;
            };

          // events
          input.addEventListener('focus', toggleSearch);
          ctrlClose.addEventListener('click', toggleSearch);
          // esc key closes search overlay
          // keyboard navigation events
          document.addEventListener('keydown', function (ev) {
            var keyCode = ev.keyCode || ev.which;
            if (keyCode === 27 && isOpen) {
              toggleSearch(ev);
            }
          });


          /***** for demo purposes only: don't allow to submit the form *****/
          morphSearch.querySelector('button[type="submit"]').addEventListener('click', function (ev) {
            ev.preventDefault();
          });
        })();

      },
      finalize: function () {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function () {
        // JavaScript to be fired on the home page
      },
      finalize: function () {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    // About us page, note the change from about-us to about_us.
    'about_us': {
      init: function () {
        // JavaScript to be fired on the about us page
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function (func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function () {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function (i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

  $(document).ready(function () {

    var filters = [];

    $('.filters span[data-filter]').on('click', function (e) {

      var filter = $(this).data('filter');
      if (!$(this).hasClass('active')) {
        filters.push(filter);
      } else {
        var index = filters.indexOf(filter);
        if (index > -1) {
          filters.splice(index, 1);
        }
      }

      if (filters.length == 0) {
        $('.restaurant-grid .grid-item').each(function (index) {
          $(this).show();
        });
      } else {
        $('.restaurant-grid .grid-item').each(function (index) {
          filter = $(this).data('filter');
          if (filter !== undefined) {
            filter = filter.split(',');
            for (var i = 0; i < filter.length; i++) {
              var index = filters.indexOf(filter[i]);
              if (index > -1) {
                $(this).show();
                break;
              } else {
                $(this).hide();
              }
            }
          }
        });
      }

      $(this).toggleClass('active');
    });

    $(".open-filter").click(function () {
      $(".filter-full").slideToggle('open');
    });
  });

})(jQuery); // Fully reference jQuery after this point.
