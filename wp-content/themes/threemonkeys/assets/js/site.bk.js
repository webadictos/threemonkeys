import './file-loader';
import { fileLoader } from './file-loader';
import './menu-desplegable';

const Site = (() => {
  const baseURI = window.location.hostname;
  let scrollExecuted = false;
  let DMP;

  const init = () => {
    checkExternalLinks();
    import('./page-refresh');
    //Listener For Infinite Scroll
    document
      .querySelector('body')
      .addEventListener('is.post-load', checkExternalLinks);

    window.addEventListener('scroll', checkScrollToLoadScripts);
  };

  const checkExternalLinks = () => {
    //Open all external links in new tab
    [...document.links].forEach(link => {
      if (link.hostname != baseURI) {
        link.target = '_blank';
        let rel = link.rel;

        if (!rel.includes('noreferrer')) link.rel += ' noreferrer';
        if (!rel.includes('noopener')) link.rel += ' noopener';
      }
    });
  };
  const checkScrollToLoadScripts = () => {
    const scrollTop =
      window.pageYOffset ||
      document.documentElement.scrollTop ||
      document.body.scrollTop ||
      0;

    if ((scrollTop > 3) & !scrollExecuted) {
      window.removeEventListener('scroll', checkScrollToLoadScripts);
      executeOnScroll();
      scrollExecuted = true;
    }
  };

  const executeOnScroll = () => {
    fileLoader
      .js('https://securepubads.g.doubleclick.net/tag/js/gpt.js', 'gpt-js')
      .then(gpt => {
        window.adSlots = window.adSlots || {
          all: [],
          refreshables: [],
        };
        window.googletag = window.googletag || {
          cmd: [],
        };
        googletag.cmd.push(function () {
          googletag.pubads().setTargeting('canal', ThemeSetup.page.canal);
          googletag.pubads().setTargeting('postID', ThemeSetup.page.postID);
          googletag.pubads().setTargeting('tags', ThemeSetup.page.tags);
          googletag.pubads().setTargeting('single', ThemeSetup.page.is_single);
          googletag.pubads().setTargeting('url', window.location.pathname);
          googletag
            .pubads()
            .setTargeting('referrer', document.referrer.split('/')[2]);
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

          googletag
            .pubads()
            .addEventListener('slotRenderEnded', function (event) {
              if (event.isEmpty) {
                var id = event.slot.getSlotElementId();
                var x = document.getElementById(id);
                x.style.display = 'none';
                //console.log("No tiene anuncio");

                var r1 = x.closest('.ad-container');

                if (r1) {
                  r1.style.display = 'none';
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

                x.classList.add('ad-slot');

                var r1 = x.closest('.ad-container');

                if (r1) {
                  r1.style.display = r1.style.display === 'none' ? '' : '';
                }
              }
            });
        });
        import('./ads');
      });
    import('./dmp').then(e => {
      DMP = e.DMP;
      DMP.init();
    });

    /*
wp_enqueue_script( 'standalone-amp-story-player', 'https://cdn.ampproject.org/amp-story-player-v0.js', [], 'v0', true );
wp_enqueue_style('standalone-amp-story-player', 'https://cdn.ampproject.org/amp-story-player-v0.css', [], 'v0' );

*/
    fileLoader
      .js(
        'https://cdn.ampproject.org/amp-story-player-v0.js',
        'standalone-amp-story-player-js'
      )
      .then(script => {
        import('./web-stories');
      });
    fileLoader.css(
      'https://cdn.ampproject.org/amp-story-player-v0.css',
      'standalone-amp-story-player-css'
    );
  };

  return {
    init: init,
  };
})();

export { Site };
