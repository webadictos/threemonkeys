"use strict";(self.webpackChunkcoolspot_theme=self.webpackChunkcoolspot_theme||[]).push([["dmp"],{"./js/dmp.js":function(e,s,t){t.r(s),t.d(s,{DMP:function(){return n}});var a=t("./js/localstorage.js");const n=(()=>{let s=a.ls.get("userProfile")||{};let e,t;const n=()=>{s.clientId||"function"==typeof ga&&ga(function(e){s.clientId=e.get("clientId")}),t=Date.now(),e=s.lastTimestamp||t,s.lastTimestamp=t,s.previousTimestamp=e},o=()=>{a.ls.set("userProfile",s,44928e3)};const l=()=>{void 0===s.sessions&&(s.sessions=0),s.sessions++},c=e=>{void 0===s.articlesIds&&(s.articlesIds=[]),!1===s.articlesIds.includes(e)&&s.articlesIds.push(e)},r=()=>{void 0===s.sections&&(s.sections=[]),Array.isArray(ThemeSetup.current.canal)&&ThemeSetup.current.canal.forEach(e=>{!1===s.sections.includes(e)&&s.sections.push(e)})};return{init:()=>{(async()=>{n(),l(),c(ThemeSetup.current.postID),r(),o()})()}}})()},"./js/localstorage.js":function(e,s,t){t.d(s,{ls:function(){return n}});const n={set:(e,s,t=86400)=>{s={value:s,ttl:Date.now()+1e3*t};localStorage.setItem(e,JSON.stringify(s))},get:e=>{var s=localStorage.getItem(e);return s?(s=JSON.parse(s),Date.now()>s.ttl?(localStorage.removeItem(e),null):s.value):null}}}}]);