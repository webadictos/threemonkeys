const EditorsPick = (() => {
  const apiKey = 'ck_e6bfd066d51c70f4f3dfc3188382ea9d3f97c3ae';
  const apiSecret = 'cs_9cb284d60988cfcb3ef1f54ee0e65a2e91e378eb';
  const wooSiteAPI = 'https://hotbookbazar.com/wp-json/wc/v3';
  const editorsPick = document.getElementById('editors-pick');
  const productsContainer = editorsPick.querySelector('.editors-pick__items');
  const cache = {};
  const productQueryAtts = {
    per_page: 8,
    status: 'publish',
    featured: true,
  };

  const init = () => {
    const intersectionHandler = entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          load();
          editorsPickObserver.unobserve(entry.target);
        }
      });
    };

    const editorsPickObserver = new IntersectionObserver(intersectionHandler, {
      threshold: 0.02,
    });

    editorsPickObserver.observe(editorsPick);
  };

  const load = () => {
    initLinks();
  };

  const basicAuth = (key, secret) => 'Basic ' + btoa(key + ':' + secret);

  const getCategories = async () => {
    const wooUrl = `${wooSiteAPI}/products/categories`;
    const data = { per_page: 100, hide_empty: true, parent: 0 };
    let apiUrl = new URL(wooUrl);
    apiUrl.search = new URLSearchParams(data).toString();

    try {
      const response = await fetch(apiUrl, {
        headers: { Authorization: basicAuth(apiKey, apiSecret) },
      });
      return await response.json();
    } catch (error) {
      // catches errors both in fetch and response.json
      console.log(error);
    }
  };

  const getProducts = item => {
    const catId = item.dataset.id;
    //console.log(catId);
    if (catId) {
      if (cache[catId]) {
        displayProducts(cache[catId]);
      } else {
        fetchProducts({ ...productQueryAtts, category: catId }).then(data => {
          cache[catId] = data;
          displayProducts(data);
        });
      }
    } else {
      if (cache[0]) {
        displayProducts(cache[0]);
      } else {
        if (item.dataset.atts) {
          let atts = JSON.parse(item.dataset.atts);

          fetchProducts({ ...productQueryAtts, ...atts }).then(data => {
            cache[0] = data;
            displayProducts(data);
          });
        }
      }
    }
  };

  const fetchProducts = async (atts = {}) => {
    const wooUrl = `${wooSiteAPI}/products`;
    //const data = { per_page: 8, category: catId };
    const data = { ...atts };

    let apiUrl = new URL(wooUrl);
    apiUrl.search = new URLSearchParams(data).toString();

    productsContainer.innerHTML = `<div class="submenu-loader"><img src="${window.location.protocol}//${window.location.hostname}/wp-content/themes/hotbook-theme/assets/images/loader.svg" width="50" height="50"></div>`;

    try {
      const response = await fetch(apiUrl, {
        headers: { Authorization: basicAuth(apiKey, apiSecret) },
      });
      return await response.json();
    } catch (error) {
      // catches errors both in fetch and response.json
      console.log(error);
    }
  };

  const displayProducts = products => {
    let productsTemplate = '';

    if (products) {
      products.forEach(product => {
        productsTemplate += getProductTemplate(product);
      });

      const fragment = document
        .createRange()
        .createContextualFragment(productsTemplate);

      productsContainer.innerHTML = '';
      productsContainer.appendChild(fragment);
    }
  };

  const getProductTemplate = product => {
    let vendor = '';
    if (product.vendor) {
      vendor = `<div class="hbazar-item__vendor"><a href="${product.vendor_url}" target="_blank" rel="noopener noreferrer" title="${product.vendor}">${product.vendor}</a></div>`;
    }
    let formatter = new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',

      // These options are needed to round to whole numbers if that's what you want.
      //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
      //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
    });

    let price = formatter.format(product.price); /* $2,500.00 */
    let regular_price = 0;
    let regular_price_html = '';
    let image =
      'https://i1.wp.com/hotbook.mx/wp-content/uploads/2021/06/hotbook-icon-black.png';
    if (product.on_sale) {
      regular_price = formatter.format(product.regular_price);
      regular_price_html = `<span class="regular_price">${regular_price}</span>`;
    }
    if (product.images.length > 0) {
      image = product.images[0].src;
    }

    let template = `
    <article id='product-${product.id}' class='hbazar-item'>
      <header>
        <figure class="hbazar-item__thumbnail">
          <img src="${image}">
        </figure>
        <div class="hbazar-item__meta">
        <h4>${product.name}</h4>
        ${vendor}
        <div class="hbazar-item__price">${regular_price_html}<span class="price">${price}</span></div>
        </div>
      </header>
      <footer>
      <a href="${product.permalink}" target="_blank" class="hbazar-item__buy" rel="noopener noreferrer" title="${product.name}">Comprar Ahora</a>
      </footer>
    </article>
    `;

    return template;
  };

  const initLinks = () => {
    let links = editorsPick.querySelectorAll('a[data-type=products]');
    let firstItem = editorsPick.querySelector('a[data-active="1"]');

    if (links) {
      links.forEach(item => {
        item.addEventListener(
          'click',
          e => {
            e.preventDefault();
            const item = e.target;
            const parent = item.parentElement;
            const submenu = parent.querySelector(
              '.editors-pick__menu--submenu'
            );
            const activeItems = editorsPick.querySelectorAll('.active');

            activeItems.forEach(activeItem =>
              activeItem.classList.remove('active')
            );

            const othersMenus = Array.from(
              editorsPick.querySelectorAll('.editors-pick__menu--submenu')
            ).filter(item => item !== submenu);

            if (parent.classList.contains('mainitem')) {
              othersMenus.forEach(item => {
                item.classList.remove('open');
                item.classList.remove('active');
              });
              parent.classList.add('active');
              if (submenu) submenu.classList.toggle('open');
            }

            item.classList.add('active');

            getProducts(item);
          },
          false
        );
      });
    }

    if (firstItem) {
      const parent = firstItem.parentElement;
      const submenu = parent.querySelector('.editors-pick__menu--submenu');
      if (submenu) submenu.classList.toggle('open');
      getProducts(firstItem);
    }
  };

  return {
    init: init,
  };
})();

export { EditorsPick };
