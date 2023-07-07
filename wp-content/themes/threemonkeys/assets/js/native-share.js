import * as bootstrap from 'bootstrap';

// import {
//   Toaster,
//   ToasterPosition,
//   ToasterTimer,
//   ToasterType,
// } from 'bs-toaster';

/**
 * Native Share
 */
const NativeShare = (() => {
  //   const advancedToaster = new Toaster({
  //     position: ToasterPosition.TOP_END,
  //     type: ToasterType.DEFAULT,
  //     delay: 5000,
  //     timer: ToasterTimer.COUNTDOWN,
  //     animation: true,
  //     defaultIconMarkup: `<i class="fas fa-bolt me-2"></i>`,
  //   });

  const init = () => {
    initializeShare();
  };

  const initializeShare = () => {
    const shortcutsBar = document.createElement('div');
    shortcutsBar.id = 'shortcuts-bar';
    shortcutsBar.classList.add('shortcuts-bar');

    if (ThemeSetup.socio !== undefined) {
      if (
        ThemeSetup.socio.gg_socio_geolocalizacion !== undefined &&
        ThemeSetup.socio.gg_socio_geolocalizacion.latitude !== undefined
      ) {
        const locationBtn = document.createElement('button');
        locationBtn.id = 'shortcut-location';
        locationBtn.classList.add('btn');
        locationBtn.classList.add('btn-primary');
        locationBtn.classList.add('shortcut-bar__item');

        let msgs = {};
        msgs['es'] = 'Ir a ubicación';
        msgs['en'] = 'Go to location';

        locationBtn.innerHTML = `<a href="https://www.google.com/maps/dir/Current+Location/${
          ThemeSetup.socio.gg_socio_geolocalizacion.latitude
        },${
          ThemeSetup.socio.gg_socio_geolocalizacion.longitude
        }" target="_blank" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="${
          msgs[ThemeSetup.lang]
        }"><i class="fas fa-location-arrow"></i></a>`;
        shortcutsBar.appendChild(locationBtn);
      }

      if (
        ThemeSetup.socio.gg_socio_url_reservaciones !== undefined &&
        ThemeSetup.socio.gg_socio_url_reservaciones !== ''
      ) {
        const reservacionBtn = document.createElement('button');
        reservacionBtn.id = 'shortcut-reservacion';
        reservacionBtn.classList.add('btn');
        reservacionBtn.classList.add('btn-primary');
        reservacionBtn.classList.add('shortcut-bar__item');

        let msgs = {};
        msgs['es'] = 'Reservaciones';
        msgs['en'] = 'Reservation';

        reservacionBtn.innerHTML = `<a href="${
          ThemeSetup.socio.gg_socio_url_reservaciones
        }" target="_blank" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="${
          msgs[ThemeSetup.lang]
        }"><i class="fas fa-calendar-check"></i></a>`;
        shortcutsBar.appendChild(reservacionBtn);
      }

      if (
        ThemeSetup.socio.gg_socio_menu !== 'undefined' &&
        ThemeSetup.socio.gg_socio_menu !== ''
      ) {
        const menuBtn = document.createElement('button');
        menuBtn.id = 'shortcut-menu';
        menuBtn.classList.add('btn');
        menuBtn.classList.add('btn-primary');
        menuBtn.classList.add('shortcut-bar__item');

        let msgs = {};
        msgs['es'] = 'Ver menú';
        msgs['en'] = 'View menu';

        menuBtn.innerHTML = `<a href="${
          ThemeSetup.socio.gg_socio_menu
        }" target="_blank" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="${
          msgs[ThemeSetup.lang]
        }"><i class="fas fa-file-alt"></i></a>`;
        shortcutsBar.appendChild(menuBtn);
      }

      if (
        ThemeSetup.socio.gg_socio_telefono !== undefined &&
        ThemeSetup.socio.gg_socio_telefono !== ''
      ) {
        const telBtn = document.createElement('button');
        telBtn.id = 'shortcut-tel';
        telBtn.classList.add('btn');
        telBtn.classList.add('btn-primary');
        telBtn.classList.add('shortcut-bar__item');

        let msgs = {};
        msgs['es'] = 'Llamar por teléfono';
        msgs['en'] = 'Call by phone';

        telBtn.innerHTML = `<a href="tel:${
          ThemeSetup.socio.gg_socio_telefono
        }" target="_blank" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="${
          msgs[ThemeSetup.lang]
        }"><i class="fas fa-phone-alt"></i></a>`;
        shortcutsBar.appendChild(telBtn);
      }

      if (
        ThemeSetup.socio.gg_socio_whatsapp !== undefined &&
        ThemeSetup.socio.gg_socio_whatsapp !== ''
      ) {
        const whatsappBtn = document.createElement('button');
        whatsappBtn.id = 'shortcut-whatsapp';
        whatsappBtn.classList.add('btn');
        whatsappBtn.classList.add('btn-primary');
        whatsappBtn.classList.add('shortcut-bar__item');

        let msgs = {};
        msgs['es'] = 'Contacto desde la Guía Gastronómica de CDMX';
        msgs['en'] = 'Contact from Guía Gastronómica';
        let msgsPop = {};
        msgsPop['es'] = 'Enviar mensaje por whatsapp';
        msgsPop['en'] = 'Send a WhatsApp message';

        let mensaje = encodeURIComponent(msgs[ThemeSetup.lang]);
        /*
                            $waMsg = "*" . __('Contacto desde la Guía Gastronómica de CDMX', 'guia-gastronomica') . "* - " . get_the_permalink();
                            $waLink = "https://wa.me/{$waNumber}?text=" . urlencode($waMsg);

      */

        whatsappBtn.innerHTML = `<a href="https://wa.me/${
          ThemeSetup.socio.gg_socio_whatsapp
        }?text=${mensaje}" target="_blank" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="${
          msgsPop[ThemeSetup.lang]
        }"><i class="fab fa-whatsapp"></i></a>`;
        shortcutsBar.appendChild(whatsappBtn);
      }
    }

    if (navigator.share) {
      const shareBtn = document.createElement('button');
      shareBtn.id = 'native-share';
      shareBtn.classList.add('btn');
      shareBtn.classList.add('btn-primary');
      shareBtn.classList.add('native-share');
      shareBtn.classList.add('shortcut-bar__item');
      shareBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share-fill" viewBox="0 0 16 16">
      <path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z"/>
    </svg>`;

      shareBtn.addEventListener('click', showShare);

      shortcutsBar.appendChild(shareBtn);
    }

    var popoverTriggerList = [].slice.call(
      shortcutsBar.querySelectorAll('[data-bs-toggle="popover"]')
    );
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl);
    });

    document.body.appendChild(shortcutsBar);
  };

  const showShare = () => {
    let url = document.location.href;

    navigator
      .share({ url })
      .then(() => console.log('Successful share'))
      .catch(error => {
        //advancedToaster.create('Error', 'Error al intentar compartir')
        //console.log(error);
      });
  };

  return {
    init: init,
  };
})();

export { NativeShare };
