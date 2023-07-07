const articlesObserver = (() => {
  const config = {
    articlesContainerSelector: '.archive-articles-container',
    articleObserverSelector: '.row',
    firstArticleSelector: '.row',
  };

  let previousPage;
  let scrollIndex = 0;

  const dispatchTrackedEvent = (meta, infiniteScroll = true) => {
    const newPostLoaded = new CustomEvent('is.page-tracked', {
      detail: {
        page: meta,
        byInfiniteScroll: infiniteScroll,
      },
    });
    document.querySelector('body').dispatchEvent(newPostLoaded);
  };

  const intersectionHandler = entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        intersectionArticleHandler(entry);
      }
    });
  };

  // const newArticlesLoadedObserver;
  // const articleIntersectionObserver;
  const articleIntersectionObserver = new IntersectionObserver(
    intersectionHandler,
    {
      threshold: 0.02,
    }
  );

  const decodeEntity = inputStr => {
    var textarea = document.createElement('textarea');
    textarea.innerHTML = inputStr;
    return textarea.value;
  };

  const changeTitle = page => {
    let currentTitle = document.title.replace(/ - Página [0-9]+/, '');
    let currentUri = window.location.pathname;
    let uri = currentUri.replace(/\/page\/[0-9]+/, '');

    if (page > 1) {
      uri = uri + 'page/' + page + '/';
      currentTitle = currentTitle + ' - Página ' + page;
    }

    document.title = decodeEntity(currentTitle);

    history.replaceState({}, decodeEntity(currentTitle), uri);
  };

  const trackingArticle = row => {
    if (row.dataset.isTracking) {
    } else {
      const page =
        typeof row.dataset.page !== 'undefined'
          ? JSON.parse(row.dataset.page)
          : 1;
      let currentUri = window.location.pathname;
      let uri = currentUri.replace(/\/page\/[0-9]+/, '');
      row.setAttribute('data-is-tracking', 'true');

      dispatchTrackedEvent(page);

      if (page > 1) {
        uri = uri + 'page/' + page + '/';
      }
      if (uri != currentUri) {
        history.pushState({ page: page }, '/page/' + page + '/', uri);

        currentUri = uri;

        if (typeof gtag === 'function' && gtag.hasOwnProperty('config')) {
          // Google Analytics 4 is being used.
          gtag('event', 'page_view', { page_path: uri });
        }

        if (typeof ga === 'function') {
          //Send Google Analytics Pageview
          ga('set', 'page', uri);
          ga('send', 'pageview');
          //Evento para analizar cuantos pageviews da cada scroll
          ga('send', 'event', 'Scroll Archive Pageview', scrollIndex, uri);
        }
      }
    }
  };

  const intersectionArticleHandler = entry => {
    if (entry.target.nodeType === 1) {
      //const newTarget = entry.target;
      const parentArticle = entry.target;
      const currentPage =
        typeof parentArticle.dataset.page !== 'undefined'
          ? JSON.parse(parentArticle.dataset.page)
          : 1;

      const articlesContainer = document.querySelector(
        config.articlesContainerSelector
      ); //console.log(parentArticle);

      // Prhimera ejecucción
      if (!previousPage) {
        previousPage = parentArticle;
        parentArticle.setAttribute('data-is-visible', 'true');
        parentArticle.setAttribute('data-is-tracking', 'true');
        parentArticle.setAttribute('data-scroll-index', scrollIndex++);
        dispatchTrackedEvent(currentPage, false);
        // Hay un nuevo articulo visible
      } else if (previousPage !== parentArticle) {
        previousPage.removeAttribute('data-is-visible');
        parentArticle.setAttribute('data-is-visible', 'true');
        changeTitle(currentPage);
        previousPage = parentArticle;
        trackingArticle(parentArticle);

        // if (parentArticle.dataset.scrollIndex !== '0') {

        // } else {

        // }
      }
    }
  };

  const newArticleHandler = mutations => {
    mutations.forEach(function (mutation) {
      if (mutation.type === 'childList') {
        mutation.addedNodes.forEach(node => {
          if (node.nodeType === 1) {
            if (node.classList.contains('row')) {
              const parentArticle = node;

              articleIntersectionObserver.observe(node);
              //console.log('Nuevo Artículo', 'Observer agregado');

              parentArticle.setAttribute('data-scroll-index', scrollIndex++);
            }
          }
        });
      }
    });
  };

  const init = () => {
    //console.log('Empieza a observar');

    const articlesContainer = document.querySelector(
      config.articlesContainerSelector
    );

    const newArticlesLoadedObserver = new MutationObserver(newArticleHandler);

    // pasa al observer el nodo y la configuracion
    newArticlesLoadedObserver.observe(articlesContainer, {
      attributes: true,
      childList: true,
      characterData: true,
    });

    const firstArticle = articlesContainer.querySelector(
      config.firstArticleSelector
    );
    if (firstArticle && firstArticle.nodeType === 1) {
      articleIntersectionObserver.observe(firstArticle);
      // console.log('Observando primera página');
    }

    // const bodyEl = document.querySelector('body');
    // if (bodyEl) {
    //   bodyEl.addEventListener('is.page-load', e => {
    //     if (e.detail.postID && e.detail.isPromoted) {
    //       // console.log('Nueva página cargada');
    //     }
    //   });
    // }
  };

  return {
    init: init,
  };
})();

articlesObserver.init();
