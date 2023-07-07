import { fileLoader } from './file-loader';
import { is_home } from './wordpress-functions';

const Site = (() => {
  const baseURI = window.location.hostname;
  let scrollExecuted = false;
  let DMP;

  const init = () => {
    checkExternalLinks();

    import(
      /* webpackChunkName: "dropdown-hover" */
      /* webpackMode: "lazy" */
      './dropdown-hover'
    );

    import(
      /* webpackChunkName: "page-refresh" */
      /* webpackMode: "lazy" */
      './page-refresh'
    );
    if (typeof ga === 'function')
      import(
        /* webpackChunkName: "track-outbound" */
        /* webpackMode: "lazy" */
        './track-outbound'
      );

    // import(
    //   /* webpackChunkName: "menu-hamburguesa" */
    //   /* webpackMode: "lazy" */
    //   './menu-hamburguesa'
    // );

    if (document.querySelector('.collapse-search')) {
      import(
        /* webpackChunkName: "search-autocomplete" */
        /* webpackMode: "lazy" */
        './search-autocomplete'
      );
    }

    //Listener For Infinite Scroll
    document
      .querySelector('body')
      .addEventListener('is.post-load', checkExternalLinks);

    checkScrollToLoadScripts();

    // window.addEventListener('scroll', checkScrollToLoadScripts);
    // window.addEventListener('mousemove', checkScrollToLoadScripts);
    // window.addEventListener('touchmove', checkScrollToLoadScripts);
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
    if (!scrollExecuted) {
      window.removeEventListener('scroll', checkScrollToLoadScripts);
      window.removeEventListener('mousemove', checkScrollToLoadScripts);
      window.removeEventListener('touchmove', checkScrollToLoadScripts);

      executeOnScroll();
      scrollExecuted = true;
    }
  };

  const executeOnScroll = () => {
    loadLCP();
    import(
      /* webpackChunkName: "dmp" */
      /* webpackMode: "lazy" */
      './dmp'
    ).then(e => {
      DMP = e.DMP;
      DMP.init();
    });

    import(
      /* webpackChunkName: "native-share" */
      /* webpackMode: "lazy" */
      './native-share'
    ).then(e => e.NativeShare.init());

    // import(
    //   /* webpackChunkName: "scroll-indicator" */
    //   /* webpackMode: "lazy" */
    //   './scroll-indicator'
    // );

    import(
      /* webpackChunkName: "scroll-controls" */
      /* webpackMode: "lazy" */
      './scroll-controls'
    );

    // import(
    //   /* webpackChunkName: "jquery-functions" */
    //   /* webpackMode: "lazy" */
    //   './jquery-functions'
    // );

    if (ThemeSetup.ads.enabled) {
      fileLoader
        .js('https://securepubads.g.doubleclick.net/tag/js/gpt.js', 'gpt-js')
        .then(script => {
          import(
            /* webpackChunkName: "admanager" */
            /* webpackMode: "lazy" */
            './admanager'
          ).then(module => {
            module.AdManager.init();
            import(
              /* webpackChunkName: "google-ads" */
              /* webpackMode: "lazy" */
              './ads'
            ).then(ads => ads.waGoogleAdManagerModule.init());
          });
        });
    }

    if (document.querySelector('.wa-instagram-grid')) {
      import(
        /* webpackChunkName: "instagram-grid" */
        /* webpackMode: "lazy" */
        './instagram-grid'
      ).then(module => {
        // console.log('Comenzando Instagram Grid');
        module.instagramGrid.init();
      });
    }

    if (document.querySelector('.glightbox')) {
      import(
        /* webpackChunkName: "lightbox" */
        /* webpackMode: "lazy" */
        './lightbox'
      );
    }

    if (
      ThemeSetup.infinite_scroll &&
      document.querySelector('.archive-articles-container')
    ) {
      import(
        /* webpackChunkName: "archive-infinite-scroll" */
        /* webpackMode: "lazy" */
        './archive-infinite-scroll'
      ).then(
        import(
          /* webpackChunkName: "archive-observer" */
          /* webpackMode: "lazy" */
          './archive-observer'
        )
      );
    }
  };

  const loadLCP = () => {
    const lcp = document.querySelectorAll('[data-lcp-src]');
    if (lcp) {
      [...lcp].forEach(item => {
        item.src = item.dataset.lcpSrc;
        item.dataset.lcpLoaded = true;
      });
    }

    const lazyBgItems = document.querySelectorAll('[data-lazybg]');

    if (lazyBgItems) {
      [...lazyBgItems].forEach(item => {
        item.style.backgroundImage = `url('${item.dataset.lazybg}')`;
        item.dataset.lazyBgLoaded = true;
      });
    }
  };

  return {
    init: init,
  };
})();

export { Site };
