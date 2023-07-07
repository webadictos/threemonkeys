console.log(
  `%c 
  
  HOME
  
  `,
  'color: red;padding-bottom:.5rem;font-size:1.2rem;'
);

if (document.querySelector('.swiper')) {
  import(
    /* webpackChunkName: "swiper-carrusel" */
    /* webpackMode: "lazy" */
    './js/swiper-carrusel'
  ).then(module => {
    module.swiperCarrusel.init();
  });
}
