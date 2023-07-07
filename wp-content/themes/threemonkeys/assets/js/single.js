import { is_single, getPostConfig } from './wordpress-functions';
import { fileLoader } from './file-loader';

const Single = (() => {
  let scriptsLoaded = false;
  let SocialShare;
  let mapLoaded = false;

  const init = () => {
    //Send GA Events to track channels of current post
    trackPost();

    window.addEventListener('scroll', checkScrollToLoadScripts);

    document
      .querySelector('body')
      .addEventListener('is.post-load', loadDependenciesForNewArticles);

    if (document.querySelector('[data-place-id]')) {
      import(
        /* webpackChunkName: "map-places" */
        /* webpackMode: "lazy" */
        './leaflet-places'
      ).then(e => {
        // console.log('Tiene mapa');
        mapLoaded = true;
        e.PlacesMap.init();
      });
    }
  };

  const loadDependenciesForNewArticles = e => {
    if (e.detail.postID) {
      //console.log(`Loading dependencies for #post-${e.detail.postID}`);
      WA_ThemeSetup.currentID = e.detail.postID;
      loadSocialScripts(`#post-${e.detail.postID}`);
      if (typeof window.lazyLoadInstance != 'undefined') {
        window.lazyLoadInstance.update();
      }

      if (SocialShare) SocialShare.init();

      if (!mapLoaded) {
        import(
          /* webpackChunkName: "map-places" */
          /* webpackMode: "lazy" */
          './leaflet-places'
        ).then(e => {
          mapLoaded = true;
          e.PlacesMap.init();
          // console.log('Tiene mapa también');
        });
      }
    }
  };

  const checkScrollToLoadScripts = () => {
    const scrollTop =
      window.pageYOffset ||
      document.documentElement.scrollTop ||
      document.body.scrollTop ||
      0;

    if ((scrollTop > 10) & !scriptsLoaded) {
      window.removeEventListener('scroll', checkScrollToLoadScripts);
      scriptsLoadedByScroll();
      scriptsLoaded = true;
    }
  };

  const scriptsLoadedByScroll = () => {
    if (
      ThemeSetup.infinite_scroll &&
      document.querySelector('.articles-container')
    ) {
      import(
        /* webpackChunkName: "infinite-scroll" */
        /* webpackMode: "lazy" */
        './infinite-scroll'
      ).then(
        import(
          /* webpackChunkName: "articles-observer" */
          /* webpackMode: "lazy" */
          './articles-observer'
        )
      );
    }
    loadSocialScripts();

    import(
      /* webpackChunkName: "social-share" */
      /* webpackMode: "lazy" */
      './social-share'
    ).then(e => {
      SocialShare = e.SocialShare;
      SocialShare.init();
    });

    // ga(function (tracker) {
    //   var clientId = tracker.get('clientId');
    //   console.log('El id del cliente:', clientId);
    // });

    //Vainilla Lazy

    window.lazyLoadOptions = {
      elements_selector: '.lazy-wa',
      threshold: '20',
    };
    window.addEventListener(
      'LazyLoad::Initialized',
      function (event) {
        window.lazyLoadInstance = event.detail.instance;
      },
      false
    );

    fileLoader.js(
      'https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.4.0/dist/lazyload.min.js',
      'vanilla-lazyload-js'
    );
  };

  const trackPost = () => {
    const postConfig = getPostConfig(ThemeSetup.current.postID);
    const article = document.querySelector(
      `#post-${ThemeSetup.current.postID}`
    );

    if (typeof gtag === 'function' && gtag.hasOwnProperty('config')) {
      if (Array.isArray(postConfig.canal)) {
        postConfig.canal.forEach(function (item, index) {
          gtag('event', 'page_view', {
            event_category: 'Pageviews por canal',
            event_label: item,
            page_path: article.dataset.slug,
          });
        });
      }
    }

    if (Array.isArray(postConfig.canal)) {
      postConfig.canal.forEach(function (item, index) {
        try {
          ga(
            'send',
            'event',
            'Pageviews por canal',
            item,
            article.dataset.slug
          );
        } catch (err) {
          console.log('Analytics is not defined'); // Error: "printMessage is not defined"
        }
      });
    }
  };

  const loadSocialScripts = container => {
    let contenedor;
    if (typeof container === 'undefined') {
      contenedor = document;
    } else {
      contenedor = document.querySelector(container);
    }

    if (is_single()) {
      //INSTAGRAM
      if (contenedor.querySelector('.instagram-media')) {
        //Instagram
        if (undefined === window.instgrm) {
          console.log('Load Instagram JS');
          fileLoader
            .js('//platform.instagram.com/en_US/embeds.js', 'instagram-js')
            .then(script => window.instgrm.Embeds.process());
        } else {
          window.instgrm.Embeds.process();
        }
      }

      //PINTEREST
      if (contenedor.querySelector('a[data-pin-do]')) {
        //Instagram
        (function (w, d) {
          if (!w.hazPinIt) {
            console.log('Load Pinterest JS');

            w.hazPinIt = true;
            var s = d.createElement('SCRIPT');
            s.src = '//assets.pinterest.com/js/pinit.js';
            s.type = 'text/javascript';
            s.setAttribute('data-pin-build', 'parsePins');
            d.body.appendChild(s);
            window.parsePins();
          }
        })(window, document);
      }

      //Twitter
      if (contenedor.querySelector('.twitter-tweet')) {
        if (typeof twttr != 'undefined') {
          twttr.widgets.load();
        } else {
          console.log('Load Twitter JS');
          fileLoader
            .js('https://platform.twitter.com/widgets.js', 'twitter-js')
            .then(script => twttr.widgets.load());
        }
      }

      //Facebook
      if (contenedor.querySelector('.fb-post')) {
        console.log('Load FB JS');

        fileLoader
          .js('https://connect.facebook.net/en_US/all.js#xfbml=1', 'fb-js')
          .then(script => {
            FB.init({ status: true, cookie: true, xfbml: true });
            FB.XFBML.parse();
          });
      }
    }
  };

  return {
    init: init,
  };
})();

export { Single };
