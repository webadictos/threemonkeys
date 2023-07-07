const isMobile = () => {
  const isMobile = window.matchMedia('(max-width: 1199px)');

  return isMobile.matches;
};

const dropdownItems = document.querySelectorAll(
  '.navbar .nav-item [data-bs-toggle="dropdown"]'
);

if (!isMobile()) {
  dropdownItems.forEach(function (everyitem) {
    let timeout = null;
    let submenu = everyitem.nextElementSibling;

    everyitem.addEventListener('mouseenter', function (e) {
      clearTimeout(timeout);
      everyitem.classList.add('show');
      submenu.classList.add('show');
    });

    everyitem.addEventListener('mouseleave', function (e) {
      timeout = setTimeout(function () {
        everyitem.classList.remove('show');
        submenu.classList.remove('show');
      }, 200);
    });

    submenu.addEventListener('mouseenter', function (e) {
      clearTimeout(timeout);
    });

    submenu.addEventListener('mouseleave', function (e) {
      timeout = setTimeout(function () {
        everyitem.classList.remove('show');
        submenu.classList.remove('show');
      }, 200);
    });

    everyitem.addEventListener('click', function (e) {
      let el_link = this;

      if (el_link != null && el_link.href) {
        e.preventDefault();
        window.location.href = el_link.href;
      }
    });

    submenu.addEventListener('transitionend', function () {
      if (!submenu.classList.contains('show')) {
        submenu.style.display = 'none';
      }
    });
  });
} else {
  dropdownItems.forEach(function (everyitem) {
    let submenu = everyitem.nextElementSibling;
    const url = everyitem.getAttribute('href');
    let viewAllTemplate = `
        <li itemscope="itemscope" itemtype="https://www.schema.org/SiteNavigationElement" class="menu-item"><a title="View All" href="${url}" class="dropdown-item">View All</a></li>
        `;

    if (url != null) {
      const fragment = document
        .createRange()
        .createContextualFragment(viewAllTemplate);
      submenu.appendChild(fragment);
    }
  });
}
