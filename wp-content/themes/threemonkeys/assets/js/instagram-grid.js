const instagramGrid = (() => {
  const access_token =
    typeof ThemeSetup.social.igtoken === 'undefined'
      ? ''
      : ThemeSetup.social.igtoken;
  let igData = '';

  const init = () => {
    const callbackRouter = (entries, observer) =>
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          let target = entry.target;
          //  console.log('Loading: ' + target.dataset.element);
          if (access_token !== '') loadInstagramGrid(target);
          observer.unobserve(entry.target);
        }
      });
    const callbackObserver = new IntersectionObserver(callbackRouter, {
      rootMargin: '0px 0px 200px 0px',
    });

    document
      .querySelectorAll('.wa-instagram-grid')
      .forEach(lazyElement => callbackObserver.observe(lazyElement));
  };

  const loadInstagramGrid = target => {
    let user = target.dataset.user;
    let items = target.dataset.items ? target.dataset.items : 12;
    let el = target.dataset.selector
      ? document.querySelector(target.dataset.selector)
      : target;

    //console.log('Display Instagram Grid');
    igData = sessionStorage.getItem(user + '_ig_datav2');
    if (igData) {
      //console.log('Instagram Cache');
      const data = JSON.parse(igData);
      displayInstagramGrid(data, el);
    } else {
      fetchInstagramGrid(user, el);
    }
  };

  const displayInstagramGrid = (data, el) => {
    data.data.forEach(function (item) {
      el.innerHTML = el.innerHTML + getInstagramItemTemplate(item);
    });
  };

  const fetchInstagramGrid = (user, el) => {
    const instagramJSON =
      'https://graph.instagram.com/me/media?fields=caption,media_url,media_type,permalink,timestamp,username&access_token=' +
      access_token;
    fetch(instagramJSON)
      .then(response => response.json())
      .then(data => {
        sessionStorage.setItem(user + '_ig_datav2', JSON.stringify(data));
        displayInstagramGrid(data, el);
      });
  };

  const getInstagramItemTemplate = item => {
    let template = '';

    template = `
      <div class="scrolling-wrapper__item wa-instagram__item">
      <div class="scrolling-wrapper__thumbnail wa-instagram__thumbnail media-${item.media_type.toLowerCase()}">

            <a
              href="${item.permalink}"
              title="${item.caption}"
              rel="nofollow noopener"
              target="_blank"
            >
              ${(() => {
                if (item.media_type == 'VIDEO') {
                  return `
                    <video
                      src="${item.media_url}"
                      alt="${item.caption}"
                      title="${item.caption}"
                      nocontrols
                    />
                  `;
                } else {
                  return `<img
                    src="${item.media_url}"
                    alt="${item.caption}"
                    title="${item.caption}"
                    loading="lazy"
                  />`;
                }
              })()}
            </a>
          </div>
      </div>
    `;

    return template;
  };

  return {
    init: init,
  };
})();

export { instagramGrid };
