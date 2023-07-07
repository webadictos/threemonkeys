const OutboundLinks = (() => {
  const baseURI = window.location.host;

  const init = () => {
    document.querySelector('body').addEventListener('click', trackingClics);
  };

  const trackingClics = e => {
    if (typeof ga !== 'function') return;

    const link = e.target.closest('a');

    if (!link || baseURI === link.host) return;

    const href = link.href;

    ga('send', {
      hitType: 'event',
      eventCategory: 'outbound-link',
      eventAction: 'link',
      eventLabel: href,
      transport: 'beacon',
    });
  };

  return {
    init: init,
  };
})();

OutboundLinks.init();
