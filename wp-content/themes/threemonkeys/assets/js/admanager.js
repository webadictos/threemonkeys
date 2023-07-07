const AdManager = (() => {
  const init = () => {
    initAdManager();
  };

  const initAdManager = () => {
    window.adSlots = window.adSlots || {
      all: [],
      refreshables: [],
    };
    window.googletag = window.googletag || {
      cmd: [],
    };
    googletag.cmd.push(function () {
      googletag
        .pubads()
        .setTargeting('canal', WA_ThemeSetup.current.canal ?? []);
      googletag
        .pubads()
        .setTargeting('postID', WA_ThemeSetup.current.postID ?? 0);
      googletag.pubads().setTargeting('tags', WA_ThemeSetup.current.tags ?? []);
      googletag
        .pubads()
        .setTargeting('single', WA_ThemeSetup.current.is_single);
      googletag.pubads().setTargeting('url', window.location.pathname);
      googletag.pubads().setTargeting('hostname', window.location.hostname);

      if (document.referrer) {
        googletag
          .pubads()
          .setTargeting('referrer', document.referrer.split('/')[2]);
      }

      googletag.pubads().setCentering(true);

      googletag.pubads().setCentering(true);

      googletag.pubads().enableLazyLoad({
        fetchMarginPercent: 50,
        renderMarginPercent: 25,
        mobileScaling: 2.0,
      });

      googletag.pubads().enableSingleRequest();
      googletag.pubads().disableInitialLoad();
      googletag.enableServices();

      googletag.pubads().addEventListener('slotRenderEnded', function (event) {
        if (event.isEmpty) {
          var id = event.slot.getSlotElementId();
          var x = document.getElementById(id);
          x.style.display = 'none';
          //console.log("No tiene anuncio");

          var r1 = x.closest('.ad-container');

          if (r1) {
            r1.style.display = 'none';
          }

          if (x.classList.contains('ad-footer')) {
            let footerContainer = x.closest('.ad-footer-container');

            if (footerContainer) {
              footerContainer.classList.remove('show');
            }
          }
        } else {
          var id = event.slot.getSlotElementId();
          var x = document.getElementById(id);
          //console.log("Cargando anuncio");

          //console.log(event.size[1]);
          if (event.size[1] > 100) {
            //console.log("Es mayor");
            var r1 = x.closest('.ad-fixed-top');
            if (r1) {
              r1.classList.remove('sticky-top');
              r1.classList.add('not-sticky');
            }
          }

          if (x.classList.contains('ad-footer')) {
            let footerContainer = x.closest('.ad-footer-container');

            if (footerContainer) {
              footerContainer.classList.add('show');
              // footerContainer.style.display = r1.style.display === 'none' ? '' : '';
            }
          }

          x.classList.add('ad-slot');

          var r1 = x.closest('.ad-container');

          if (r1) {
            r1.style.display = r1.style.display === 'none' ? '' : '';
          }
        }
      });
    });
  };
  return {
    init: init,
  };
})();

export { AdManager };
