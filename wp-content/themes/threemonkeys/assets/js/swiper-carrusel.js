import Swiper, { Navigation, Pagination, Mousewheel, Autoplay } from 'swiper';
const swiperCarrusel = (() => {
  const init = () => {
    document
      .querySelectorAll('.swiper')
      .forEach(swiperElement => createSwiper(swiperElement));
  };

  const createSwiper = item => {
    const itemConfig = JSON.parse(item.dataset.carruselConfig);

    const carouselID = item.dataset.carouselId;
    /**
+   {"direction":"horizontal","loop":false,"items_gap":10,
"autoheight":true,"items_visible":3,"items_visible_movil":1,"items_visible_tablet":2,
"mousewheel":false,"pagination":true,"navigation":true}

    */

    let swiperConfig = {
      slideClass: 'swiper-slide',
      direction: 'horizontal',
      centeredSlides: true,
      loop: false,
      slidesPerView: 3,
      spaceBetween: 10,
      mousewheel: true,
      autoHeight: true,
      modules: [],
      breakpoints: {},
    };

    swiperConfig.direction = itemConfig.direction;
    swiperConfig.loop = itemConfig.loop;
    swiperConfig.slidesPerView = itemConfig.items_visible;
    swiperConfig.autoHeight = itemConfig.autoheight;
    swiperConfig.spaceBetween = itemConfig.items_gap;

    // if (swiperConfig.autoHeight) {
    //   swiperConfig.calculateHeight = true;
    // }

    if (itemConfig.pagination) {
      swiperConfig.modules.push(Pagination);
      swiperConfig.pagination = {
        el: '.swiper-pagination',
        clickable: true,
      };
    }

    if (itemConfig.mousewheel) {
      swiperConfig.modules.push(Mousewheel);
    }

    if (itemConfig.navigation) {
      swiperConfig.modules.push(Navigation);
      swiperConfig.navigation = {
        nextEl: `#swiper-carousel-${carouselID} .swiper-button-next`,
        prevEl: `#swiper-carousel-${carouselID} .swiper-button-prev`,
      };
    }
    if (itemConfig.autoplay) {
      swiperConfig.modules.push(Autoplay);
      swiperConfig.autoplay = {
        delay: 2500,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      };
    }

    swiperConfig.breakpoints = {
      // when window width is >= 320px
      320: {
        slidesPerView: itemConfig.items_visible_movil,
        spaceBetween: 0,
        slidesOffsetBefore: 30,
        slidesOffsetAfter: 30,
      },
      // when window width is >= 480px
      768: {
        slidesPerView: itemConfig.items_visible_tablet,
        spaceBetween: itemConfig.items_gap,
      },
      // when window width is >= 1024
      1024: {
        slidesPerView: itemConfig.items_visible,
        spaceBetween: itemConfig.items_gap,
      },
    };
    // console.log(swiperConfig);

    const swiper = new Swiper(item, swiperConfig);

    const collapsedItems = item.querySelectorAll('.collapse');

    if (collapsedItems) {
      [...collapsedItems].map(item => {
        item.addEventListener('shown.bs.collapse', () => {
          swiper.updateAutoHeight(150);
        });
        item.addEventListener('hidden.bs.collapse', () => {
          swiper.updateAutoHeight(150);
        });
      });
    }

    // const swiper = new Swiper(item, {
    //   // Optional parameters
    //   direction: 'horizontal',
    //   loop: false,
    //   slidesPerView: 2,
    //   spaceBetween: 10,
    //   modules: [Navigation, Pagination, Mousewheel],
    //   mousewheel: true,
    //   autoHeight: true,
    //   // If we need pagination
    //   pagination: {
    //     el: '.swiper-pagination',
    //     clickable: true,
    //   },

    //   // Navigation arrows
    //   navigation: {
    //     nextEl: '.swiper-button-next',
    //     prevEl: '.swiper-button-prev',
    //   },
    //   // Responsive breakpoints
    //   breakpoints: {
    //     // when window width is >= 320px
    //     320: {
    //       slidesPerView: 1,
    //       spaceBetween: 0,
    //     },
    //     // when window width is >= 480px
    //     768: {
    //       slidesPerView: 2,
    //       spaceBetween: 10,
    //     },
    //     // when window width is >= 1024
    //     1024: {
    //       slidesPerView: 3,
    //       spaceBetween: 10,
    //     },
    //   },
    // });
  };

  return {
    init: init,
  };
})();

export { swiperCarrusel };
