"use strict";(self.webpackChunk=self.webpackChunk||[]).push([["instagram-grid"],{"./js/instagram-grid.js":function(e,t,a){a.r(t),a.d(t,{instagramGrid:function(){return r}});const r=(()=>{const n=void 0===ThemeSetup.social.igtoken?"":ThemeSetup.social.igtoken;let s="";const o=(e,t)=>{e.data.forEach(function(e){t.innerHTML=t.innerHTML+(e=>{let t="";return t=`
      <div class="scrolling-wrapper__item wa-instagram__item">
      <div class="scrolling-wrapper__thumbnail wa-instagram__thumbnail media-${e.media_type.toLowerCase()}">

            <a
              href="${e.permalink}"
              title="${e.caption}"
              rel="nofollow noopener"
              target="_blank"
            >
              ${(()=>{if(e.media_type=="VIDEO")return`
                    <video
                      src="${e.media_url}"
                      alt="${e.caption}"
                      title="${e.caption}"
                      nocontrols
                    />
                  `;else return`<img
                    src="${e.media_url}"
                    alt="${e.caption}"
                    title="${e.caption}"
                    loading="lazy"
                  />`})()}
            </a>
          </div>
      </div>
    `})(e)})},c=(t,a)=>{var e="https://graph.instagram.com/me/media?fields=caption,media_url,media_type,permalink,timestamp,username&access_token="+n;fetch(e).then(e=>e.json()).then(e=>{sessionStorage.setItem(t+"_ig_datav2",JSON.stringify(e)),o(e,a)})};return{init:()=>{const t=new IntersectionObserver((e,i)=>e.forEach(e=>{if(e.isIntersecting){var t=e.target;if(""!==n){var a,r=t.dataset.user,t=(t.dataset.items,t.dataset.selector?document.querySelector(t.dataset.selector):t);(s=sessionStorage.getItem(r+"_ig_datav2"))?(a=JSON.parse(s),o(a,t)):c(r,t)}i.unobserve(e.target)}}),{rootMargin:"0px 0px 200px 0px"});document.querySelectorAll(".wa-instagram-grid").forEach(e=>t.observe(e))}}})()}}]);