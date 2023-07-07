import { ls } from './localstorage';

const WPInfiniteScroll = (() => {
  // Basic Configuration
  const config = {
    api: WA_ThemeSetup.ajaxurl,
    startPage: 0, // 0 for the first page, 1 for the second and so on...
    postsPerPage: WA_ThemeSetup.infinite_scroll.max_page || 5, // Number of posts to load per page
    articlesContainer: '.articles-container',
    next: WA_ThemeSetup.infinite_scroll.next || [],
    promoted: WA_ThemeSetup.promoted || [],
  };

  // Private Properties
  let postsLoaded = false;
  let articlesContainer = document.querySelector(config.articlesContainer);
  let canBeLoaded = true;

  const initialize = () => {
    window.addEventListener(
      'scroll',
      () => {
        loadContent();
      },
      false
    );

    // document.querySelector('body').addEventListener('is.post-tracked', e => {
    //   canBeLoaded = true;
    // });
  };
  // Private Methods

  const getPromoted = () => {
    const promotedviews = ls.get('promotedviews') || [];
    //console.log('Current:', promotedviews);
    //console.log('Current Promoted:', config.promoted);
    config.promoted = config.promoted.filter(
      val => !promotedviews.includes(val)
    );
    //console.log('Filter Promoted:', config.promoted);

    return config.promoted.shift();
  };

  const dispatchArticleEvent = (pID, isPromoted) => {
    const newPostLoaded = new CustomEvent('is.post-load', {
      detail: { postID: pID, infinitescroll: true, isPromoted: isPromoted },
    });
    document.querySelector('body').dispatchEvent(newPostLoaded);
  };
  const loadContent = function () {
    //console.log(getArticleScrollOffset());

    if (isInRangeToLoad()) {
      let pID = 0;
      let articlePromoted = getPromoted(); //config.promoted.shift();
      let nextArticle = config.next.shift();
      let isPromoted = false;

      if (articlePromoted) {
        pID = articlePromoted;
        isPromoted = true;
      } else {
        if (nextArticle) {
          pID = nextArticle;
        }
      }

      canBeLoaded = false;

      const article = fetchArticle(pID).then(article => {
        const fragment = document
          .createRange()
          .createContextualFragment(article.data);

        articlesContainer.appendChild(fragment);

        // const promotedviews = ls.get('promotedviews') || [];

        // console.log('Promoted Before:', ls.get('promotedviews'));

        // promotedviews.push(pID);

        // ls.set('promotedviews', promotedviews, 60);

        // console.log('Promoted After:', ls.get('promotedviews'));

        // const newPostLoaded = new CustomEvent('is.post-load', {
        //   detail: { postID: pID, infinitescroll: true, isPromoted: isPromoted },
        // });
        // document.querySelector('body').dispatchEvent(newPostLoaded);

        dispatchArticleEvent(pID, isPromoted);

        canBeLoaded = true;
      });
    }
  };
  const getApiUrl = (url, id) => {
    const data = { action: 'loadmore', postid: id };
    let apiUrl = new URL(url);
    apiUrl.search = new URLSearchParams(data).toString();
    return apiUrl;
  };
  const fetchArticle = async id => {
    try {
      const request = await fetch(getApiUrl(config.api, id));
      const article = await request.json();
      return article;
    } catch (e) {
      console.log(e);
    }
  };

  const getArticleScrollOffset = () => {
    return articlesContainer.offsetHeight - articlesContainer.offsetHeight / 2;
  };

  const isInRangeToLoad = () => {
    const currentScroll = window.pageYOffset;

    return (
      currentScroll > getArticleScrollOffset() &&
      canBeLoaded == true &&
      config.next.length > 0
    );
  };

  // Public Properties and Methods
  return {
    init: initialize,
  };
})();

// Initialize Infinite Scroll
WPInfiniteScroll.init();
