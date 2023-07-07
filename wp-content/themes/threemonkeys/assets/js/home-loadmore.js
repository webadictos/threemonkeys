const WPHomeLoadMore = (() => {
  // Basic Configuration
  const config = {
    api: ThemeSetup.ajaxurl,
    maxPage: 5, // Number of posts to load per page
    articlesContainer: '.articles-container',
    buttonLoadMore: '.instyle_loadmore',
  };

  // Private Properties
  let postsLoaded = false;
  const buttonLoadMore = document.querySelector(config.buttonLoadMore);
  const loader = document.querySelector('.ins-loader');

  let articlesContainer;
  let canBeLoaded = true;

  const initialize = () => {
    buttonLoadMore.addEventListener('click', loadContent, false);
  };

  const loadContent = e => {
    e.preventDefault();

    const btn = e.currentTarget;

    articlesContainer = document.querySelector(btn.dataset.container);

    console.log(btn.dataset);

    console.log(articlesContainer);
    console.log('Loading');

    btn.setAttribute('disabled', true);

    loader.classList.remove('d-none');

    if (parseInt(btn.dataset.paged) === parseInt(btn.dataset.maxPages)) {
      btn.style.display = 'none';
    }

    //     const fragment = document
    //       .createRange()
    //       .createContextualFragment(article.data);

    // if (isInRangeToLoad()) {
    //   let pID = 0;
    //   let articlePromoted = getPromoted(); //config.promoted.shift();
    //   let nextArticle = config.next.shift();
    //   let isPromoted = false;

    //   if (articlePromoted) {
    //     pID = articlePromoted;
    //     isPromoted = true;
    //   } else {
    //     if (nextArticle) {
    //       pID = nextArticle;
    //     }
    //   }

    //   canBeLoaded = false;

    const articles = fetchArticles(btn).then(article => {
      btn.dataset.paged = parseInt(btn.dataset.paged) + 1;

      loader.classList.add('d-none');

      const fragment = document
        .createRange()
        .createContextualFragment(article.data);

      articlesContainer.appendChild(fragment);

      btn.setAttribute('disabled', false);

      // dispatchArticleEvent(pID, isPromoted);

      // canBeLoaded = true;
    });
  };
  const getApiUrl = (url, btn) => {
    const p = parseInt(btn.dataset.paged);
    const ppp = btn.dataset.perPage;
    const max = btn.dataset.maxPages;

    const data = {
      action: 'morehome',
      paged: p,
      pos_per_page: ppp,
      max_pages: max,
    };

    // const data = { action: 'loadmore', postid: id };
    let apiUrl = new URL(url);
    apiUrl.search = new URLSearchParams(data).toString();
    return apiUrl;
  };
  const fetchArticles = async btn => {
    try {
      const request = await fetch(getApiUrl(config.api, btn));
      const articles = await request.json();
      return articles;
    } catch (e) {
      console.log(e);
    }
  };

  // Public Properties and Methods
  return {
    init: initialize,
  };
})();

// Initialize Infinite Scroll
WPHomeLoadMore.init();

// jQuery(function ($) {
//   // use jQuery code inside this to avoid "$ is not defined" error
//   $('.instyle_loadmore').click(function (e) {
//     e.preventDefault();
//     var button = $(this),
//       container = $('.' + button.data('container')),
//       data = {
//         action: 'loadmorehome',
//         page: instyle_loadmore_params.current_page,
//       };
//     console.log(button.data('container'));
//     $.ajax({
//       // you can also use $.post here
//       url: instyle_loadmore_params.ajaxurl, // AJAX handler
//       data: data,
//       type: 'POST',
//       beforeSend: function (xhr) {
//         container.append(
//           "<div class='ins-loader'><img src='https://instyle.mx/wp-content/themes/instylemx/images/loading.svg' alt='Loading...'></div>"
//         );
//       },
//       success: function (data) {
//         if (data) {
//           $('.ins-loader').remove();
//           container.append(data);
//           var pnum = instyle_loadmore_params.current_page;
//           setTimeout(function () {
//             $('.marque-title-page-' + pnum).marquee({
//               duplicated: true,
//               startVisible: true,
//               gap: 10,
//               speed: 20,
//             });
//           }, 500);

//           if (
//             $('#home-b-d-' + instyle_loadmore_params.current_page).length > 0 &&
//             $('#home-b-d-' + instyle_loadmore_params.current_page).data(
//               'ad-loaded'
//             ) != 1
//           ) {
//             googletag.cmd.push(function () {
//               var ros_box_c_more = googletag
//                 .sizeMapping()
//                 .addSize([0, 0], [300, 250])
//                 .addSize(
//                   [320, 0],
//                   [
//                     [300, 250],
//                     [300, 600],
//                   ]
//                 ) //Mobile
//                 .addSize([1024, 200], [300, 250]) // Desktop
//                 .build();

//               adSlots['home-b-d-' + instyle_loadmore_params.current_page] =
//                 googletag
//                   .defineSlot(
//                     '/270959339/instyle-ros-b-d',
//                     [
//                       [
//                         [300, 600],
//                         [300, 250],
//                       ],
//                     ],
//                     'home-b-d-' + instyle_loadmore_params.current_page
//                   )
//                   .defineSizeMapping(ros_box_c_more)
//                   .addService(googletag.pubads());
//               googletag.display(
//                 'home-b-d-' + instyle_loadmore_params.current_page
//               );
//             });
//             //		console.log('#ros-b-b-' + instyle_loadmore_params.current_page +" loaded");
//             $('#home-b-d-' + instyle_loadmore_params.current_page).data(
//               'ad-loaded',
//               1
//             );
//             if (checkWidth() > 767) {
//               $(
//                 '#home-b-d-' + instyle_loadmore_params.current_page
//               ).scrollToFixed({
//                 marginTop: function () {
//                   var margin = 0;

//                   margin = $('.sections-navigation').outerHeight(true) + 10;

//                   return margin;
//                 },
//                 limit:
//                   $('#section-more-' + pnum).offset().top +
//                   $('#section-more-' + pnum).outerHeight(true) -
//                   300,
//                 removeOffsets: true,
//               });
//             }
//           }

//           instyle_loadmore_params.current_page++;
//           //$('.masonry-bootstrap-page'+instyle_loadmore_params.current_page).masonry();

//           if (
//             instyle_loadmore_params.current_page ==
//             instyle_loadmore_params.max_page
//           )
//             button.remove(); // if last page, remove the button

//           // you can also fire the "post-load" event here if you use a plugin that requires it
//           //$( document.body ).trigger( 'post-load' );
//         } else {
//           button.remove(); // if no data, remove the button as well
//         }
//       },
//     });
//   });
// });
