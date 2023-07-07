/**
 * Social Share
 */
const SocialShare = (() => {
  const Config = {
    Link: 'a.share-link',
    Width: 500,
    Height: 500,
  };

  const init = () => {
    // console.log('Ready For Share');
    var slink = document.querySelectorAll(Config.Link);
    for (var a = 0; a < slink.length; a++) {
      slink[a].onclick = PopupHandler;
    }
  };

  const PopupHandler = e => {
    e = e ? e : window.event;
    var t = e.target ? e.target : e.srcElement;
    // popup position
    var px = Math.floor(((screen.availWidth || 1024) - Config.Width) / 2),
      py = Math.floor(((screen.availHeight || 700) - Config.Height) / 2);

    let link = t.closest('a');
    console.log(link.dataset.socialNetwork);

    ga('send', 'event', {
      eventCategory: 'NTG social',
      eventAction: 'social share',
      eventLabel: link.dataset.socialNetwork,
      nonInteraction: false,
    });

    // open popup
    var popup = window.open(
      link.href,
      'social',
      'width=' +
        Config.Width +
        ',height=' +
        Config.Height +
        ',left=' +
        px +
        ',top=' +
        py +
        ',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1'
    );
    if (popup) {
      popup.focus();
      if (e.preventDefault) e.preventDefault();
      e.returnValue = false;
    }
    return !!popup;
  };

  return {
    init: init,
  };
})();

export { SocialShare };
