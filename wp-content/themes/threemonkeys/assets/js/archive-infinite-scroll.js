const WPArchiveInfiniteScroll = (() => {
  // Basic Configuration
  const config = {
    // api: WA_ThemeSetup.ajaxurl,
    api: `${window.location.protocol}//${window.location.host}/wp-json/wa-theme/v1/archive`,
    max_page: WA_ThemeSetup.infinite_scroll.max_page || 1, // Number of posts to load per page
    query: WA_ThemeSetup.infinite_scroll.query,
    page: WA_ThemeSetup.infinite_scroll.current_page,
    articlesContainer: '.archive-articles-container',
  };

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
  };

  const dispatchArticleEvent = page => {
    const newPostLoaded = new CustomEvent('is.page-load', {
      detail: { currentPage: page, infinitescroll: true },
    });
    document.querySelector('body').dispatchEvent(newPostLoaded);
  };
  const loadContent = function () {
    if (isInRangeToLoad()) {
      canBeLoaded = false;

      const article = fetchArticle().then(article => {
        const fragment = document
          .createRange()
          .createContextualFragment(article.content_rendered);

        articlesContainer.appendChild(fragment);

        config.page++;

        dispatchArticleEvent(config.page);

        canBeLoaded = true;
      });
    }
  };
  const getApiUrl = url => {
    //data-loadmore-layout="grid" data-loadmore-item-layout="article-item-nota"
    const layout = articlesContainer.dataset.loadmoreLayout || 'flex';
    const itemLayout = articlesContainer.dataset.loadmoreItemLayout || '';

    const data = {
      //  action: 'loadmore_archive',
      query: config.query,
      page: config.page,
      item_layout: itemLayout,
      layout: layout,
    };
    let apiUrl = new URL(url);
    apiUrl.search = new URLSearchParams(data).toString();
    return apiUrl;
  };
  const fetchArticle = async () => {
    try {
      const request = await fetch(getApiUrl(config.api));
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
      config.page < config.max_page
    );
  };

  // Public Properties and Methods
  return {
    init: initialize,
  };
})();

// Initialize Infinite Scroll
WPArchiveInfiniteScroll.init();
