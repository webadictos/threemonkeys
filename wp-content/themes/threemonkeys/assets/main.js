// Webpack Imports
import * as bootstrap from 'bootstrap';
//import './js/sticky-header';
import { is_home, is_single, post_type } from './js/wordpress-functions';
import { Site } from './js/site';

console.log(
  `%c 
  
  Theme: Food and Pleasure   
  Developed by: @dmedina
  
  `,
  'color: red;padding-bottom:.5rem;font-size:1.2rem;'
);

window.addEventListener('DOMContentLoaded', function (event) {
  Site.init();

  //   import(
  //     /* webpackChunkName: "sticky-header" */
  //     /* webpackMode: "lazy" */
  //     './js/sticky-header'
  //   );

  if (is_single()) {
    import(
      /* webpackChunkName: "single" */
      /* webpackMode: "lazy" */
      './js/single'
    ).then(e => e.Single.init());
  }
});

(function () {
  'use strict';

  // Focus input if Searchform is empty
  [].forEach.call(document.querySelectorAll('.search-form'), el => {
    el.addEventListener('submit', function (e) {
      var search = el.querySelector('input');
      if (search.value.length < 1) {
        e.preventDefault();
        search.focus();
      }
    });
  });

  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
  );
  const tooltipList = [...tooltipTriggerList].map(
    tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)
  );

  // Initialize Popovers: https://getbootstrap.com/docs/5.0/components/popovers
  var popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]')
  );
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl, {
      trigger: 'focus',
    });
  });
})();
