const menuDesplegable = (() => {
  const cache = {};

  const menuItems = document.querySelectorAll('[data-show-submenu]');
  const container = document.getElementById('menu-display');
  const menu = document.getElementById('menuoffcanvas');
  let currentTarget = '';

  const init = () => {
    // console.log(menuItems);

    if (checkIsValidSize()) {
      //      const meta = JSON.parse(article.dataset.meta);

      if (menuItems) {
        menuItems.forEach(menuItem => {
          const args = JSON.parse(menuItem.dataset.submenuArgs);

          // menuItem.addEventListener('mouseover', e => {
          //   displaySubmenu(args, menuItem);
          //   currentTarget = menuItem;
          //   removeActiveClass();
          //   menuItem.classList.add('active');
          // });

          menuItem.addEventListener('click', e => {
            e.preventDefault();
            displaySubmenu(args, e.target);
          });
        });

        menu.addEventListener('shown.bs.offcanvas', event => {
          displaySubmenu({ layout: 'productos' }, menuItems[0]);
        });

        menu.addEventListener('hidden.bs.offcanvas	', event => {
          resetContainer();
          removeActiveClass();
        });
      }
    }
  };

  const removeActiveClass = () => {
    menuItems.forEach(menuItem => {
      menuItem.classList.remove('active');
    });
  };
  const resetContainer = () => (container.innerHTML = '');

  const checkIsValidSize = () =>
    window.matchMedia('(min-width: 992px)').matches;

  const displaySubmenu = (data, target) => {
    if (!data.layout) return;

    removeActiveClass();

    resetContainer();

    const template = document.getElementById(`menu-template-${data.layout}`);

    if (template) {
      const menuData = template.content.cloneNode(true);
      container.appendChild(menuData);
    }

    target.classList.add('active');

    // console.log(target);
  };

  return {
    init: init,
  };
})();

menuDesplegable.init();
