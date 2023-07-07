/**
 * Generate Ads on The Fly
 */

const waCommonModule = (function () {

  const debug = () => {
    console.log(setup);
    adUnits.forEach(adUnit => {
      console.log(adUnit.dataset);
    });
  };

  const initialize = () => {


    refreshOnResize();
    console.log('Rendering Test');

  };

  const refreshOnResize = () => {
    const isMobile = window.matchMedia('(max-width: 176px)');

    isMobile.addEventListener('change', event => {
      if (event.matches) {
        console.log('The window is now 576px or under');

      } else {
        console.log('The window is now over 576px');
      }
    });
  };


  return {
    init: () => {
      initialize();
    },
  };
})();

export { waCommonModule };
