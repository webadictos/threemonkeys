import { Loader } from '@googlemaps/js-api-loader';
import L from 'leaflet';

const PlacesMap = (() => {
  let currentID = 0;
  // let currentContainer = ''; //document.getElementById(`post-${currentID}`);
  // let currentWidgetArea = ''; //currentContainer.querySelector('.single-widget-area');

  //let map = {};
  const maps = {};
  const markers = {};
  let mapLoaded = false;
  let firstLoad = false;
  let ignoreObserver = false;

  /**
   * THEME VARS
   */

  const themeUri = WA_ThemeSetup.themeUri;
  const iconMarker =
    WA_ThemeSetup.maps.marker || `${themeUri}/assets/images/marker.png`;
  const iconMarkerActive =
    WA_ThemeSetup.maps.markerActive || `${themeUri}/assets/images/marker.png`;
  const mapApiKey = WA_ThemeSetup.maps.api || '';
  let mapCenter = WA_ThemeSetup.maps.center || {
    lat: 19.4326018,
    lng: -99.1332049,
  };
  const initialZoom = parseInt(WA_ThemeSetup.maps.zoom) || 13;

  const mapCategory = WA_ThemeSetup.maps.map_category ?? 'maps';

  const iconMarkerL = L.icon({
    iconUrl: iconMarker,
    iconSize: [35, 48],
    // iconAnchor: [22, 94],
    // popupAnchor: [-27, -97],
    // shadowUrl: 'my-icon-shadow.png',
    // shadowSize: [68, 95],
    // shadowAnchor: [22, 94]
  });
  const iconMarkerActiveL = L.icon({
    iconUrl: iconMarkerActive,
    iconSize: [35, 48],
    // iconAnchor: [22, 94],
    // popupAnchor: [-27, -97],
    // shadowUrl: 'my-icon-shadow.png',
    // shadowSize: [68, 95],
    // shadowAnchor: [22, 94]
  });

  /**
   * Methods
   */
  const init = () => {
    currentID = WA_ThemeSetup.currentID ? WA_ThemeSetup.currentID : 0;
    initMapPlaces(currentID);

    document.querySelector('body').addEventListener('is.post-load', e => {
      if (e.detail.postID) {
        if (e.detail.infinitescroll) {
          // isInfiniteScroll = true;
          //currentPostId = e.detail.postID;
        }
        initMapPlaces(e.detail.postID);
      }
    });
  };

  const initMapPlaces = currentID => {
    const currentContainer = document.getElementById(`post-${currentID}`);
    const currentWidgetArea = getCurrentWidgetArea(currentID);
    //  currentContainer
    //   ? currentContainer.querySelector('.single-widget-area')
    //   : null;

    if (
      currentContainer &&
      currentContainer.classList.contains(`category-${mapCategory}`)
    ) {
      const title = currentContainer.querySelector('h1').innerText;
      const places = getPlaces(currentContainer);
      if ([...places].length > 0) {
        renderWidget(title, currentID, currentWidgetArea);
        loadMap(currentID, [...places]);

        if (!isMobile()) {
          addIntersectionObserver(places, currentID);
        }
      }
    }
  };

  const getPlaces = currentContainer => {
    return currentContainer.querySelectorAll('[data-place-id]');
  };

  const renderWidget = (title, id, widgetArea) => {
    const template = /* html */ `
    <div id="wa-maps-widget-${id}" class="widget wa_maps_widget ${
      isMobile() ? 'is-mobile' : ''
    }">
    <div class="widget-header">
        <h3 class="widget-title">${title}</h3>
    </div>
        <div id="map-widget-${id}" class="map-widget" data-map-id="${id}">
            <div id="map-container-${id}" class="map-container"></div>
            <div id="map-places-container-${id}" class="map-places-container"></div>
        </div>
    </div>
`;
    const fragment = document.createRange().createContextualFragment(template);

    // widgetArea.prepend(fragment);

    if (isMobile()) {
      widgetArea.after(fragment);
      // addIntersectionObserverFromMobile(id);
    } else {
      widgetArea.prepend(fragment);
    }
  };

  const isMobile = () => {
    const isMobile = window.matchMedia('(max-width: 991px)');

    return isMobile.matches;
  };

  const getCurrentWidgetArea = currentID => {
    const currentContainer = document.getElementById(`post-${currentID}`);

    if (isMobile()) {
      return currentContainer.querySelector('.entry-excerpt');
      //      return currentContainer.querySelector('footer');
    } else {
      return currentContainer.querySelector('.single-widget-area');
    }
  };

  const resetWidgetContainer = currentID => {};

  const addIntersectionObserver = (places, currentID) => {
    const callbackRouter = (entries, observer) =>
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          let target = entry.target;
          const place = {
            id: target.dataset.placeId,
          };
          if (!ignoreObserver) {
            activatePlace(currentID, place, false, true);
          } else {
            if (target.dataset.isScrolling) {
              ignoreObserver = false;
              target.removeAttribute('data-is-scrolling');
            }
          }
        }
      });
    const callbackObserver = new IntersectionObserver(callbackRouter, {
      rootMargin: '0px 0px 0px 0px',
    });

    places.forEach(placeItem => callbackObserver.observe(placeItem));
  };

  const loadMap = (currentID, places) => {
    // const loader = new Loader({
    //   apiKey: mapApiKey,
    //   version: 'weekly',
    // });

    // const mapOptions = {
    //   center: mapCenter,
    //   zoom: initialZoom,
    //   styles: [
    //     {
    //       featureType: 'poi',
    //       elementType: 'labels',
    //       stylers: [
    //         {
    //           visibility: 'off',
    //         },
    //       ],
    //     },
    //     { featureType: 'transit', stylers: [{ visibility: 'off' }] },
    //   ],
    //   mapTypeControl: false,
    //   streetViewControl: false,
    // };

    // loader
    //   .load()
    //   .then(google => {
    //     const map = new google.maps.Map(
    //       document.getElementById(`map-container-${currentID}`),
    //       mapOptions
    //     );

    //     maps[currentID] = map;

    //     google.maps.event.addListener(map, 'idle', () => {
    //       mapLoaded = true;

    //       if (!firstLoad) {
    //         firstLoad = true;
    //         renderPlacesIntoMap(currentID, places);
    //       }
    //     });

    //     // console.log('Map loaded');
    //   })
    //   .catch(e => {
    //     // do something
    //   });

    const map = L.map(`map-container-${currentID}`);

    // const map = L.map(`map-container-${currentID}`).setView(
    //   [51.505, -0.09],
    //   13
    // );
    maps[currentID] = map;

    // const marker = L.marker([51.5, -0.09])
    //   .addTo(map)
    //   .bindPopup('<b>Hello world!</b><br />I am a popup.')
    //   .openPopup();

    map.on('load', function () {
      // Aquí puedes escribir el código que deseas ejecutar cuando el mapa se cargue completamente
      // console.log('El mapa se ha cargado completamente');
      // Puedes agregar más líneas de código aquí
      // if (!firstLoad) {
      //   firstLoad = true;
      renderPlacesIntoMap(currentID, places);
      // }
    });

    map.setView([mapCenter.lat, mapCenter.lng], initialZoom);

    const tiles = L.tileLayer(
      'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
      {
        maxZoom: 19,
        attribution:
          '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      }
    ).addTo(map);
  };

  const renderPlacesIntoMap = (currentID, places) => {
    let currentMapWidget = document.getElementById(`map-widget-${currentID}`);
    let placesString = '';
    let placesItemsContainer = currentMapWidget.querySelector(
      '.map-places-container'
    );
    markers[currentID] = {};
    const map = maps[currentID];
    let isFirstIteration = true;

    places.forEach(place => {
      const placeItem = {
        id: place.dataset.placeId,
        title: place.dataset.placeTitle,
        latitud: parseFloat(place.dataset.placeLatitude),
        longitud: parseFloat(place.dataset.placeLongitude),
      };
      placesString = placesString + renderTemplatePlace(placeItem);

      addPlaceIntoMap(placeItem, currentID);

      if (isFirstIteration) {
        // Llama a map.setCenter() solo en la primera iteración
        map.setView({ lat: placeItem.latitud, lng: placeItem.longitud });
        isFirstIteration = false; // Cambia la variable de control para las siguientes iteraciones
      }
    });

    let placesTemplate = `
    <ul class="wa-item-map-places__list" id="map-places-list-${currentID}">
    ${placesString}
    </ul>
    `;

    const fragment = document
      .createRange()
      .createContextualFragment(placesTemplate);
    placesItemsContainer.appendChild(fragment);

    const listItems = document.querySelectorAll(
      `#map-places-list-${currentID} > li`
    );

    listItems.forEach(item => {
      // console.log(item);

      item.addEventListener('click', e => {
        const placeItem = {
          id: item.dataset.placeId,
        };
        activatePlace(currentID, placeItem, true);
      });
    });
  };

  const renderTemplatePlace = place => {
    const placeTitle = place.title;
    const placeId = place.id;
    const template = `
      <li class="wa-item-map-places__item" data-place-id="${placeId}">${placeTitle}</li>
    `;

    return template;
  };

  const getItemTemplate = item => {
    const template = `

        <h4 class="location-item-title">${item.title}</h4>

    `;

    return template;
  };

  const addPlaceIntoMap = (place, currentID) => {
    //  console.log('no existe');
    const latitud = place.latitud;
    const longitud = place.longitud;
    const map = maps[currentID];

    const resultsContainer = document.getElementById(
      `map-places-container-${currentID}`
    );

    const currentContainer = document.getElementById(`post-${currentID}`);

    const socioInfo = getItemTemplate(place, true);

    // const infowindow = new google.maps.InfoWindow({
    //   content: socioInfo,
    // });

    const marker = L.marker([latitud, longitud], { icon: iconMarkerL })
      .addTo(map)
      .bindPopup(socioInfo);
    // const marker = new google.maps.Marker({
    //   position: {
    //     lat: latitud, //location.coords.latitude,
    //     lng: longitud, //location.coords.longitude,
    //   },
    //   visible: true,
    //   map: maps[currentID],
    //   title: place.title || '',
    //   icon: {
    //     url: iconMarker,
    //     scaledSize: new google.maps.Size(47, 65),
    //   },
    //   //   icon: iconMarker,
    //   infowindow: infowindow,
    // });

    // infowindow.addListener('closeclick', () => {
    //   changeMarkerIcon(marker, false);
    // });

    marker.on('click', () => {
      activatePlace(currentID, place, true);
    });
    markers[currentID][place.id] = marker;
  };

  const activatePlace = (
    currentID,
    place,
    block = false,
    fromScroll = false
  ) => {
    const resultsContainer = document.getElementById(
      `map-places-container-${currentID}`
    );

    const currentContainer = document.getElementById(`post-${currentID}`);

    const map = maps[currentID];

    const marker = markers[currentID][place.id];

    if (isPlaceActive(currentID, place)) return;

    hideAllInfoWindows(currentID);

    changeMarkerIcon(marker, true);

    // marker.infowindow.open({
    //   anchor: marker,
    //   map: map,
    //   shouldFocus: false,
    // });
    marker.openPopup();
    if (map.getZoom() < 17) map.setZoom(17);

    map.setView(marker.getLatLng());

    if (!isMobile()) {
      resultsContainer
        .querySelector(`[data-place-id="${place.id}"]`)
        .scrollIntoView({
          behavior: 'smooth',
          block: 'nearest',
          inline: 'start',
        });
    }

    resetActiveItems(currentContainer);

    resultsContainer
      .querySelector(`[data-place-id="${place.id}"]`)
      .classList.add('active');

    if (!fromScroll && !isMobile()) {
      currentContainer
        .querySelector(`[data-place-id="${place.id}"]`)
        .scrollIntoView({
          behavior: 'smooth',
          block: 'center',
          inline: 'start',
        });
    }

    if (block) {
      ignoreObserver = true;
      currentContainer
        .querySelector(`[data-place-id="${place.id}"]`)
        .setAttribute('data-is-scrolling', true);
    }
  };

  const resetActiveItems = container => {
    const places = getPlaces(container);

    [...places].forEach(place => {
      place.classList.remove('active');
    });
  };

  const isPlaceActive = (currentID, place) => {
    const resultsContainer = document.getElementById(
      `map-places-container-${currentID}`
    );

    return resultsContainer
      .querySelector(`[data-place-id="${place.id}"]`)
      .classList.contains('active');
  };

  const changeMarkerIcon = (marker, activo) => {
    activo ? marker.setIcon(iconMarkerActiveL) : marker.setIcon(iconMarkerL);
  };

  const hideAllInfoWindows = currentID => {
    const map = maps[currentID];
    Object.entries(markers[currentID]).forEach(([id, marker]) => {
      // marker.infowindow.close(map, marker);
      changeMarkerIcon(marker, false);
    });
  };

  return {
    init: init,
  };
})();

export { PlacesMap };
