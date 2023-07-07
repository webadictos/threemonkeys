import {
  is_single,
  getPostConfig,
  uniqid,
  nextAll,
  filterTags,
} from './wordpress-functions';
/**
 * Generate Ads on The Fly
 */

const waGoogleAdManagerModule = (function () {
  let adUnits = [];
  let adSlots = [];
  let previousPostId = 0;
  let adsLoaded = false;
  let isInfiniteScroll = false;
  let currentPostId = 0;
  const setup = {
    network: WA_ThemeSetup.ads.network ?? '76814929000',
    slotPrefix: WA_ThemeSetup.ads.prefix ?? '',
    refresh: WA_ThemeSetup.ads.refreshAds ?? false,
    refreshTime: parseInt(WA_ThemeSetup.ads.refresh_time) || 30,
    refreshAllAdUnits: WA_ThemeSetup.ads.refreshAllAdUnits ?? false,
    timeToRefreshAllAdUnits:
      parseInt(WA_ThemeSetup.ads.timeToRefreshAllAdUnits) || 60,
    refreshAllAdUnitsLimit:
      parseInt(WA_ThemeSetup.ads.refreshAllAdUnitsLimit) || 5,
    loadOnScroll: WA_ThemeSetup.ads.loadOnScroll ?? false,
    enableInRead: WA_ThemeSetup.ads.enableInRead ?? false,
    inReadParagraph: WA_ThemeSetup.ads.inReadParagraph ?? 3,
    inReadLimit: WA_ThemeSetup.ads.inReadLimit ?? 3,
    inReadSlot: WA_ThemeSetup.ads.inread_slot ?? {
      code: 'inread',
      size_mapping: 'inread',
      refesh: true,
    },
    multipleInreadSlot: WA_ThemeSetup.ads.multiple_inread_slot ?? {
      code: 'inread-multiple',
      size_mapping: 'inread_multiple',
      refesh: true,
    },

    mappings: {
      superbanner: {
        sizes: [
          [300, 250],
          [728, 90],
          [768, 90],
          [320, 50],
          [970, 90],
          [320, 100],
        ],
        desktop: [
          [970, 90],
          [728, 90],
        ],
        mobile: [
          [320, 50],
          [320, 100],
          [300, 250],
        ],
        all: [[728, 90]],
      },
      billboard: {
        sizes: [
          [300, 250],
          [728, 90],
          [768, 90],
          [320, 50],
          [970, 90],
          [970, 250],
          [320, 100],
        ],
        desktop: [
          [970, 250],
          [970, 90],
          [728, 90],
        ],
        mobile: [
          [320, 50],
          [320, 100],
        ],
        all: [[728, 90]],
      },
      billboardboxmobile: {
        sizes: [
          [300, 250],
          [728, 90],
          [768, 90],
          [320, 50],
          [970, 90],
          [970, 250],
          [320, 100],
        ],
        desktop: [
          [970, 250],
          [970, 90],
          [728, 90],
        ],
        mobile: [
          [320, 50],
          [320, 100],
          [300, 250],
        ],
        all: [[728, 90]],
      },
      boxbanner: {
        sizes: [
          [300, 250],
          [300, 600],
        ],
        desktop: [
          [300, 250],
          [300, 600],
        ],
        mobile: [
          [300, 250],
          [300, 600],
        ],
        all: [[300, 250]],
      },
      inread: {
        sizes: [[1, 1], [300, 250], [300, 600], 'fluid'],
        desktop: [[1, 1]],
        mobile: [[1, 1], [300, 250], [300, 600], 'fluid'],
        all: [[1, 1], [300, 250], 'fluid'],
      },
      inread_multiple: {
        sizes: [
          [300, 250],
          [300, 600],
        ],
        desktop: [
          [300, 250],
          [300, 600],
        ],
        mobile: [
          [300, 250],
          [300, 600],
        ],
        all: [
          [1, 1],
          [300, 250],
        ],
      },
      interstitial: {
        sizes: [[1, 1]],
        desktop: [[1, 1]],
        mobile: [[1, 1]],
        all: [[1, 1]],
      },
    },
  };

  const debug = () => {
    console.log(setup);
    adUnits.forEach(adUnit => {
      console.log(adUnit.dataset);
    });
  };

  const initialize = () => {
    var bodyEl = document.querySelector('body');
    if (bodyEl) {
      bodyEl.addEventListener('is.post-load', e => {
        // console.log(e);
        if (e.detail.postID) {
          if (e.detail.infinitescroll) {
            isInfiniteScroll = true;
            currentPostId = e.detail.postID;
          }
          if (setup.enableInRead) {
            createInReadBlocks(e.detail.postID, e.detail.infinitescroll);
          }
          renderBlocks();
        }
      });
    }

    adSlots['all'] = [];

    if (is_single()) {
      if (setup.enableInRead) createInReadBlocks(WA_ThemeSetup.current.postID);
    }

    renderBlocks();

    refreshOnResize();
    if (setup.refresh) {
      //Si se habilita el refresh se configura un timer para cada bloque visible
      googletag.cmd.push(function () {
        googletag
          .pubads()
          .addEventListener('impressionViewable', function (event) {
            var slot = event.slot;
            // console.log('Es visible');
            if (slot.getTargeting('canRefresh').indexOf('true') > -1) {
              //console.log('Si se puede refrescar', slot.id);
              setTimeout(function () {
                googletag.pubads().refresh([slot]);
                //   console.log('Refrescando');
              }, setup.refreshTime * 1000);
            } else {
              // console.log('No puede refrescar', slot.id);
            }
          });
      });
    }

    if (setup.refreshAllAdUnits) {
      let refreshCounter = 0;
      let refreshAllSlotsInterval = window.setInterval(() => {
        refreshAllSlots();
        refreshCounter++;

        // console.log(refreshCounter);

        if (setup.refreshAllAdUnitsLimit > 0) {
          if (refreshCounter === setup.refreshAllAdUnitsLimit) {
            // console.log('Clear interval');
            clearInterval(refreshAllSlotsInterval);
          }
        }
      }, setup.timeToRefreshAllAdUnits * 1000);
    }
    //console.log('Rendering');
    if (setup.loadOnScroll) {
      loadOnScroll();
    } else {
      refreshSlots();
    }
  };

  const isSlotDisabled = (slot, exclude_adunits = []) =>
    exclude_adunits.includes(slot);

  const createInReadBlocks = (postID, infinitescroll = false) => {
    //console.log('Creando Bloques inRead');
    let postConfig = getPostConfig(postID);

    //console.log(postConfig.exclude_adunits);

    //console.log(isSlotDisabled('ros-inread', postConfig.exclude_adunits));

    if (!isSlotDisabled(setup.inReadSlot, postConfig.exclude_adunits)) {
      //console.log('Esta habilitado');

      let inReadContainer = document.createElement('div');
      let id = uniqid();
      let pNumber = postConfig.inReadParagraph ?? setup.inReadParagraph;
      let inReadLimit = postConfig.inReadLimit ?? setup.inReadLimit;

      inReadContainer.setAttribute('id', 'ros-inread-' + id); // assign an id
      inReadContainer.classList.add('ad-container');
      inReadContainer.classList.add('dfp-ad-unit');
      inReadContainer.classList.add('ad-inread');
      inReadContainer.classList.add('ad-main-inread');

      inReadContainer.setAttribute(
        'data-ad-type',
        setup.inReadSlot.size_mapping
      ); // Size Mapping
      inReadContainer.setAttribute('data-slot', setup.inReadSlot.code); // assign an id
      inReadContainer.setAttribute('data-ad-loaded', '0'); // assign an id
      inReadContainer.setAttribute(
        'data-ad-setup',
        `{"postID":${postID},"canRefresh":${setup.inReadSlot.refresh},"infinitescroll":${infinitescroll}}`
      ); // assign an id

      let mainContainer = document.querySelector(
        '#post-' + postID + ' .entry-main-text'
      );

      let childs = filterTags(mainContainer.childNodes);

      if (childs.length > 2) {
        childs[pNumber - 1].parentNode.insertBefore(
          inReadContainer,
          childs[pNumber - 1].nextSibling
        );
        //Dispatch inread event to add observer
        const inReadAdedd = new CustomEvent('is.inread-loaded', {
          detail: { postID: postID },
        });
        document.querySelector('body').dispatchEvent(inReadAdedd);

        if (
          !isSlotDisabled(
            setup.multipleInreadSlot,
            postConfig.exclude_adunits
          ) &&
          WA_ThemeSetup.ads.enableMultipleInRead
        ) {
          let sib = nextAll(document.querySelector('#ros-inread-' + id));
          let counter = 1;
          let mod = 0;
          let adCount = 0;

          // console.log(sib);

          sib.forEach(element => {
            mod = counter % 5;
            if (adCount > inReadLimit - 1) return;
            if (mod === 0) {
              let divInreadMultiple = document.createElement('div');
              let id = uniqid();
              divInreadMultiple.setAttribute('id', 'ros-inread-multiple-' + id); // assign an id
              divInreadMultiple.classList.add('ad-container');
              divInreadMultiple.classList.add('dfp-ad-unit');
              divInreadMultiple.classList.add('ad-inread');

              divInreadMultiple.setAttribute(
                'data-ad-type',
                setup.multipleInreadSlot.size_mapping
              ); // assign an id
              divInreadMultiple.setAttribute(
                'data-slot',
                setup.multipleInreadSlot.code
              ); // assign an id
              divInreadMultiple.setAttribute('data-ad-loaded', '0'); // assign an id
              divInreadMultiple.setAttribute(
                'data-ad-setup',
                `{"postID":${postID},"canRefresh":${setup.multipleInreadSlot.refresh},"infinitescroll":${infinitescroll}}`
              ); // assign an id

              element.parentNode.insertBefore(
                divInreadMultiple,
                element.nextSibling
              );
              adCount++;
            }
            counter++;
          });
        }
      }
    }
  };

  const refreshOnResize = () => {
    const isMobile = window.matchMedia('(max-width: 576px)');

    isMobile.addEventListener('change', event => {
      if (event.matches) {
        // console.log('The window is now 576px or under');
        googletag.pubads().refresh();
      } else {
        // console.log('The window is now over 576px');
        googletag.pubads().refresh();
      }
    });
  };
  const loadOnScroll = () => {
    setTimeout(() => {
      if (!adsLoaded) refreshSlots();
    }, 5000);
    window.addEventListener('scroll', checkScrollToLoad);
  };

  const checkScrollToLoad = () => {
    const scrollTop =
      window.pageYOffset ||
      document.documentElement.scrollTop ||
      document.body.scrollTop ||
      0;

    if ((scrollTop > 10) & !adsLoaded) {
      refreshSlots();
      adsLoaded = true;
      //console.log('Loading Ads...');
      window.removeEventListener('scroll', checkScrollToLoad);
    }
  };

  const refreshSlots = () => {
    var idVar = setInterval(() => {
      if (checkGoogleTag()) {
        //console.log('Googletag Enabled');
        clearInterval(idVar);
        googletag.pubads().refresh();
        adsLoaded = true;
        window.removeEventListener('scroll', checkScrollToLoad);
      } else {
        //console.log('Waiting Googletag');
      }
    }, 100);
  };

  const refreshAllSlots = () => {
    if (window.googletag && googletag.pubadsReady) {
      adSlots['all'].forEach(slot => {
        if (slot.getTargeting('canRefresh').indexOf('true') > -1) {
          // let slotId = slot.getSlotElementId();
          // let slotContainer = document.getElementById(slotId);
          // let refreshingTimes = parseInt(slotContainer.dataset.refreshing) || 0;

          // slotContainer.dataset.refreshing = refreshingTimes + 1;

          // console.log(slot);

          googletag.pubads().refresh([slot]);
        }
      });
    }
  };

  const checkGoogleTag = () => {
    if (window.googletag && googletag.pubadsReady) {
      return true;
    } else return false;
  };

  const renderBlocks = () => {
    let adSetup = {};
    adUnits = document.querySelectorAll('.dfp-ad-unit');
    //debug();
    //console.log('Rendering Ad blocks');

    adUnits.forEach(adUnit => {
      let slotNameElements = [];
      let slotName = '';
      let slot, slotSizes;
      let postConfig = {};
      let targeting = {};
      let slotMapping = {
        desktop: [],
        mobile: [],
        all: [],
      };

      try {
        //console.log(adUnit.id);
        adSetup = adUnit.dataset.adSetup;

        if (adUnit.dataset.adLoaded != 1) {
          adUnit.dataset.adLoaded = 1;

          if (adUnit.dataset.adType) {
            if (adSetup) {
              adSetup = JSON.parse(adSetup);
            }
            if (is_single() && adSetup.postID) {
              //console.log('PRUEBA', adSlots['post-' + adSetup.postID]);
              if (!adSlots['post-' + adSetup.postID]) {
                // console.log(adSlots['post-' + adSetup.postID]);
                adSlots['post-' + adSetup.postID] = [];
                // console.log('YA', adSlots['post-' + adSetup.postID]);
              }

              postConfig = getPostConfig(adSetup.postID);
              //console.log(postConfig);
              targeting.canal = postConfig.canal;
              targeting.postID = adSetup.postID;
              targeting.tags = postConfig.tags;
              targeting.is_single = 'true';
            } else {
              targeting.canal = WA_ThemeSetup.current.canal;
              targeting.postID = WA_ThemeSetup.current.postID;
              targeting.tags = WA_ThemeSetup.current.tags;
              targeting.is_single = WA_ThemeSetup.current.is_single;
            }

            slotNameElements.push(setup.network);

            if (setup.slotPrefix) {
              slotNameElements.push(setup.slotPrefix);
            }

            slotNameElements.push(adUnit.dataset.slot);

            slotName = '/' + slotNameElements.join('/');

            if (
              setup.mappings[adUnit.dataset.adType] &&
              typeof setup.mappings[adUnit.dataset.adType].sizes != 'undefined'
            ) {
              //console.log('Default Sizes');
              slotSizes = setup.mappings[adUnit.dataset.adType].sizes;
              slotMapping['desktop'] =
                setup.mappings[adUnit.dataset.adType].desktop;
              slotMapping['mobile'] =
                setup.mappings[adUnit.dataset.adType].mobile;
              slotMapping['all'] = setup.mappings[adUnit.dataset.adType].all;
            } else if (typeof adSetup.mappings.sizes != 'undefined') {
              //console.log('Custom Sizes');
              // console.log(adSetup.mapping.sizes);
              slotSizes = adSetup.mappings.sizes;
              slotMapping['desktop'] = adSetup.mappings.desktop;
              slotMapping['mobile'] = adSetup.mappings.mobile;
              slotMapping['all'] = adSetup.mappings.all;
            } else {
              console.log(
                `No se encontraron sizes definidos para el slot: ${adUnit.id} `
              );
              return;
            }

            googletag.cmd.push(function () {
              slot = googletag
                .defineSlot(slotName, slotSizes, adUnit.id)
                .defineSizeMapping(
                  googletag
                    .sizeMapping()
                    .addSize([1024, 200], slotMapping['desktop']) // Desktop
                    .addSize([320, 0], slotMapping['mobile']) //Mobile
                    .addSize([0, 0], slotMapping['all']) // all
                    .build()
                )
                .addService(googletag.pubads())
                .setTargeting('canal', targeting.canal)
                .setTargeting('postID', targeting.postID)
                .setTargeting('tags', targeting.tags)
                .setTargeting('single', targeting.is_single)
                .setTargeting('url', window.location.pathname)
                .setTargeting('hostname', window.location.hostname)
                .setTargeting('canRefresh', adSetup.canRefresh)
                .setTargeting(
                  'referrer',
                  document.referrer.split('/')[2] ?? ''
                );

              // console.log(adUnit.id);

              // console.log(slot);

              adSlots['all'].push(slot);
              if (is_single() && adSetup.postID) {
                adSlots['post-' + adSetup.postID].push(slot);
              }
            });
          }
        } else {
          //console.log(`Ad unit ${adUnit.id} is already loaded`);
        }
      } catch (e) {
        console.log(e);
      }
    });
    if (isInfiniteScroll) {
      //console.log('Si es infinito');
      if (adSlots['post-' + currentPostId]) {
        googletag.pubads().refresh(adSlots['post-' + currentPostId]);
      }
    }
  };

  return {
    init: () => {
      initialize();
    },
  };
})();

export { waGoogleAdManagerModule };
