(self.webpackChunk=self.webpackChunk||[]).push([["track-outbound"],{"./js/track-outbound.js":function(){(()=>{const e=window.location.host;const t=t=>{"function"==typeof ga&&(t=t.target.closest("a"))&&e!==t.host&&(t=t.href,ga("send",{hitType:"event",eventCategory:"outbound-link",eventAction:"link",eventLabel:t,transport:"beacon"}))};return{init:()=>{document.querySelector("body").addEventListener("click",t)}}})().init()}}]);