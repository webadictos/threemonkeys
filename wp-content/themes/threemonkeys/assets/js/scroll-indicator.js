window.addEventListener('scroll', function (ev) {
  const winScroll =
    window.pageYOffset ||
    document.documentElement.scrollTop ||
    document.body.scrollTop ||
    0;
  var height =
    document.documentElement.scrollHeight -
    document.documentElement.clientHeight;
  var scrolled = (winScroll / height) * 100;

  document.getElementById('scrollIndicator').style.width = scrolled + '%';

  //const stickyHeader = document.getElementById("masthead");

  //console.log(stickyHeader.offsetHeight);
  //console.log(scrollTop);stickyHeader.offsetHeight
});
