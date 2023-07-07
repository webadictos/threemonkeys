const scrollToNextPage = evt => {
  evt.preventDefault();
  const scrollContainer = document.getElementById(
    evt.currentTarget.dataset.scrollContainer
  );

  scrollContainer.scrollBy({
    top: 0,
    left: 250,
    behaviour: 'smooth',
  });
};
const scrollToPrevPage = evt => {
  evt.preventDefault();

  const scrollContainer = document.getElementById(
    evt.currentTarget.dataset.scrollContainer
  );

  scrollContainer.scrollBy({
    top: 0,
    left: -250,
    behaviour: 'smooth',
  });
};

const scrollToPage = evt => {
  evt.preventDefault();

  // console.log(evt.currentTarget);
  // console.log(evt.currentTarget.parentNode);

  const activeIndicator = evt.currentTarget;
  let activeIndicatorSlideTo = activeIndicator.dataset.slideTo;
  let currentActive = 0;
  const list = document.getElementById(
    evt.currentTarget.parentNode.dataset.scrollContainer
  );
  let itemsTmp = [];

  if (list.querySelector('.scrolling-wrapper__item-article')) {
    itemsTmp = Array.from(
      list.querySelectorAll('.scrolling-wrapper__item-article')
    );
  } else if (list.querySelector('.article-slide-item')) {
    itemsTmp = Array.from(list.querySelectorAll('.article-slide-item'));
  }

  const items = itemsTmp;

  //console.log(items);

  const indicators = Array.from(
    document.querySelectorAll('.scrollling-wrapper__indicators li')
  );

  indicators.forEach((indicator, i) => {
    if (indicator.classList.contains('active')) {
      currentActive = indicator.dataset.slideTo;
    }
  });

  indicators.forEach(indicator => indicator.classList.remove('active'));

  activeIndicator.classList.add('active');

  if (currentActive === activeIndicator.dataset.slideTo) return;
  if (currentActive >= activeIndicator.dataset.slideTo) {
    if (activeIndicator.dataset.slideTo === 1) activeIndicatorSlideTo = 0;
    else activeIndicatorSlideTo = activeIndicator.dataset.slideTo;
  }

  console.log(items, activeIndicatorSlideTo);

  items[activeIndicatorSlideTo].scrollIntoView();
};

const checkArrows = target => {
  //console.log(scrollevent.currentTarget.scrollLeft);

  const container = target;
  const itemsLenght = container.querySelectorAll('.nav-item').length;
  const itemSize = container.querySelector('.nav-item').offsetWidth + 30;

  const menuSize = itemsLenght * itemSize;

  const menuPosition = container.scrollLeft;

  const menuWrapperSize = container.offsetWidth;

  const menuInvisibleSize = menuSize - menuWrapperSize;

  const menuEndOffset = menuInvisibleSize - 15;

  const containerScroll = container.scrollWidth;

  var seccionContainer = container.closest('nav');

  // console.log('meenu:', menuPosition);
  // console.log('menuEndOffset:', menuEndOffset);

  // console.log('Container Width:', container.offsetWidth);
  // console.log('ScrolWidth:', container.scrollWidth);
  //console.log(seccionContainer);

  if (menuPosition <= 15 && containerScroll > menuWrapperSize) {
    seccionContainer
      .querySelector('.scroll-control.scroll-control-prev')
      .classList.add('d-none');
    seccionContainer
      .querySelector('.scroll-control.scroll-control-next')
      .classList.remove('d-none');
  } else if (menuPosition < menuEndOffset) {
    seccionContainer
      .querySelector('.scroll-control.scroll-control-prev')
      .classList.remove('d-none');
    seccionContainer
      .querySelector('.scroll-control.scroll-control-next')
      .classList.remove('d-none');
  } else if (
    menuPosition >= menuEndOffset &&
    containerScroll != menuWrapperSize
  ) {
    seccionContainer
      .querySelector('.scroll-control.scroll-control-next')
      .classList.add('d-none');
    seccionContainer
      .querySelector('.scroll-control.scroll-control-prev')
      .classList.remove('d-none');
  }
};

document
  .querySelectorAll('.scroll-control.scroll-control-next')
  .forEach(item => {
    item.addEventListener('click', scrollToNextPage);
  });

document
  .querySelectorAll('.scroll-control.scroll-control-prev')
  .forEach(item => {
    item.addEventListener('click', scrollToPrevPage);
  });

document
  .querySelectorAll('.scrollling-wrapper__indicators li')
  .forEach(item => {
    item.addEventListener('click', scrollToPage);
  });

document
  .querySelectorAll('.scrolling-wrapper.scrolling-articles')
  .forEach(item => {
    item.addEventListener('scroll', scrollevent => {
      //console.log(scrollevent.currentTarget.scrollLeft);

      const container = scrollevent.currentTarget;
      const itemsLenght = scrollevent.currentTarget.querySelectorAll(
        '.scrolling-wrapper__item-article'
      ).length;
      const itemSize = scrollevent.currentTarget.querySelector(
        '.scrolling-wrapper__item-article'
      ).offsetWidth;

      const menuSize = itemsLenght * itemSize;

      const menuPosition = scrollevent.currentTarget.scrollLeft;

      const menuWrapperSize = scrollevent.currentTarget.offsetWidth;

      const menuInvisibleSize = menuSize - menuWrapperSize;

      const menuEndOffset = menuInvisibleSize - 15;

      var seccionContainer = container.closest('section');

      if (menuPosition <= 15) {
        seccionContainer
          .querySelector('.scroll-control.scroll-control-prev')
          .classList.remove('show');
        seccionContainer
          .querySelector('.scroll-control.scroll-control-next')
          .classList.add('show');
      } else if (menuPosition < menuEndOffset) {
        seccionContainer
          .querySelector('.scroll-control.scroll-control-prev')
          .classList.add('show');
        seccionContainer
          .querySelector('.scroll-control.scroll-control-next')
          .classList.add('show');
      } else if (menuPosition >= menuEndOffset) {
        seccionContainer
          .querySelector('.scroll-control.scroll-control-next')
          .classList.remove('show');
        seccionContainer
          .querySelector('.scroll-control.scroll-control-prev')
          .classList.add('show');
      }
    });
  });

document
  .querySelectorAll('.scrolling-wrapper.scroll-with-controls')
  .forEach(item => {
    item.addEventListener('scroll', scrollevent => {
      checkArrows(scrollevent.currentTarget);
    });
  });

document.addEventListener('DOMContentLoaded', () => {
  document
    .querySelectorAll('.scrolling-wrapper.scroll-with-controls')
    .forEach(item => {
      checkArrows(item);
    });
});
