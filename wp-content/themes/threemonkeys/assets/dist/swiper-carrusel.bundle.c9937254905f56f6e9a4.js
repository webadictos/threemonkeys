"use strict";(self.webpackChunkcoolspot_theme=self.webpackChunkcoolspot_theme||[]).push([["swiper-carrusel"],{"./js/swiper-carrusel.js":function(e,s,i){i.r(s),i.d(s,{swiperCarrusel:function(){return t}});var a=i("../node_modules/swiper/swiper.esm.js");const t={init:()=>{document.querySelectorAll(".swiper").forEach(e=>{{var s=JSON.parse(e.dataset.carruselConfig),i=e.dataset.carouselId,t={slideClass:"swiper-slide",direction:"horizontal",centeredSlides:!0,loop:!1,slidesPerView:3,spaceBetween:10,mousewheel:!0,autoHeight:!0,modules:[],breakpoints:{}};t.direction=s.direction,t.loop=s.loop,t.slidesPerView=s.items_visible,t.autoHeight=s.autoheight,t.spaceBetween=s.items_gap,s.pagination&&(t.modules.push(a.tl),t.pagination={el:".swiper-pagination",clickable:!0}),s.mousewheel&&t.modules.push(a.Gk),s.navigation&&(t.modules.push(a.W_),t.navigation={nextEl:`#swiper-carousel-${i} .swiper-button-next`,prevEl:`#swiper-carousel-${i} .swiper-button-prev`}),s.autoplay&&(t.modules.push(a.pt),t.autoplay={delay:2500,disableOnInteraction:!1,pauseOnMouseEnter:!0}),t.breakpoints={320:{slidesPerView:s.items_visible_movil,spaceBetween:0,slidesOffsetBefore:30,slidesOffsetAfter:30},768:{slidesPerView:s.items_visible_tablet,spaceBetween:s.items_gap},1024:{slidesPerView:s.items_visible,spaceBetween:s.items_gap}};const l=new a.ZP(e,t),o=e.querySelectorAll(".collapse");o&&[...o].map(e=>{e.addEventListener("shown.bs.collapse",()=>{l.updateAutoHeight(150)}),e.addEventListener("hidden.bs.collapse",()=>{l.updateAutoHeight(150)})})}})}}}}]);