"use strict";(self.webpackChunkcoolspot_theme=self.webpackChunkcoolspot_theme||[]).push([["infinite-scroll"],{"./js/infinite-scroll.js":function(e,t,o){o.r(t);var n=o("./js/localstorage.js");(()=>{const r={api:`${window.location.protocol}//${window.location.host}/wp-json/wa-theme/v1/post/`,startPage:0,postsPerPage:WA_ThemeSetup.infinite_scroll.max_page||5,articlesContainer:".articles-container",next:WA_ThemeSetup.infinite_scroll.next||[],promoted:WA_ThemeSetup.promoted||[]};let s=document.querySelector(r.articlesContainer),l=!0;const i=()=>{const t=n.ls.get("promotedviews")||[];return r.promoted=r.promoted.filter(e=>!t.includes(e)),r.promoted.shift()},a=(e,t)=>{e=new CustomEvent("is.post-load",{detail:{postID:e,infinitescroll:!0,isPromoted:t}});document.querySelector("body").dispatchEvent(e)},c=async e=>{try{return await(await fetch((t=r.api,t+=e,new URL(t)))).json()}catch(e){console.log(e)}var t},t=()=>{return window.pageYOffset>s.offsetHeight-s.offsetHeight/2&&1==l&&0<r.next.length};return{init:()=>{window.addEventListener("scroll",()=>{if(t()){let t=0;var e=i(),n=r.next.shift();let o=!1;e?(t=e,o=!0):n&&(t=n),l=!1,c(t).then(e=>{e=document.createRange().createContextualFragment(e.content_rendered);s.appendChild(e),a(t,o),l=!0})}},!1)}}})().init()},"./js/localstorage.js":function(e,t,o){o.d(t,{ls:function(){return n}});const n={set:(e,t,o=86400)=>{t={value:t,ttl:Date.now()+1e3*o};localStorage.setItem(e,JSON.stringify(t))},get:e=>{var t=localStorage.getItem(e);return t?(t=JSON.parse(t),Date.now()>t.ttl?(localStorage.removeItem(e),null):t.value):null}}}}]);