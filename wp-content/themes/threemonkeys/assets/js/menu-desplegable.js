const menuDesplegable = (() => {
  const config = {
    api: ThemeSetup.ajaxurl,
  };
  const cache = {};

  const menuItems = document.querySelectorAll(
    '.menu-desplegable li[data-has-submenu]'
  );
  const init = () => {
    if (checkIsValidSize()) {
      import(
        /* webpackChunkName: "sv-hover-intent" */
        /* webpackMode: "lazy" */
        './sv-hover-intent'
      ).then(module => {
        const monsterMenu = new module.SV(menuItems, {
          // required parameters
          onEnter: function (targetItem) {
            let submenu = targetItem.querySelector('.submenu-container');
            targetItem.classList.add('open');
            submenu.classList.add('open');
            if (targetItem.dataset.loaded !== 'true') {
              submenu.innerHTML = `<div class="submenu-loader"><img src="${ThemeSetup.themeUri}/assets/images/loader.svg" width="50" height="50"></div>`;

              fetchCategory(targetItem.dataset.categoryId).then(items =>
                displaySubmenu(items.data, targetItem)
              );
            }
          },
          onExit: function (targetItem) {
            let submenu = targetItem.querySelector('.submenu-container');
            targetItem.classList.remove('open');
            submenu.classList.remove('open');
          },
        });
      });
    }
  };

  const checkIsValidSize = () =>
    window.matchMedia('(min-width: 1024px)').matches;

  const getApiUrl = (url, id) => {
    const data = { action: 'loadcatmenu', catid: id };
    let apiUrl = new URL(url);
    apiUrl.search = new URLSearchParams(data).toString();
    return apiUrl;
  };
  const fetchCategory = async id => {
    try {
      const request = await fetch(getApiUrl(config.api, id));
      const article = await request.json();
      return article;
    } catch (e) {
      console.log(e);
    }
  };

  const displaySubmenu = (data, targetItem) => {
    targetItem.dataset.loaded = true;

    let submenuContainer = targetItem.querySelector('.submenu-container');

    const displaySubcategories = items => {
      let itemList = '';

      if (items) {
        items.forEach(element => {
          itemList += `<li><a href="${element.link}">${element.name}</a></li>`;
        });
      }
      return itemList;
    };

    const displayArticles = articles => {
      let articlesList = '';
      articles.forEach(article => {
        articlesList += `
          <article>
              <picture><a href="${article.link}" title="${article.title}"><img src="${article.image}" alt="${article.title}"></a></picture>
              <h3 class="submenu-container__articles--links" ><a href="${article.link}">${article.title}</a></h3>
          </article>`;
      });
      return articlesList;
    };

    let subcats = displaySubcategories(data.categories);
    let articles = displayArticles(data.articles);

    let template = `
      <div class="container-fluid submenu-container__items">
        <div class="row w-100">
          <div class="col-5 col-md-2">
          <h3 class="submenu-container__category"><a href="${data.main.link}">${data.main.name}</a></h3>
          <ul class="submenu-container__subcategories">${subcats}</ul>
          </div>
          <div class="col-7 col-md-10 submenu-container__articles scroll">
            ${articles}
          </div>
        </div>

      </div>
     `;

    submenuContainer.innerHTML = '';

    const fragment = document.createRange().createContextualFragment(template);

    submenuContainer.appendChild(fragment);
  };

  return {
    init: init,
  };
})();

menuDesplegable.init();
