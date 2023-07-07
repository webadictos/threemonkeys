const retailImages = document.querySelectorAll('.bushmill-retail-logo');

// console.log(retailImages);

[...retailImages].forEach(retail => {
  retail.addEventListener('mouseover', event => {
    let target = event.target;
    let imgHover = target.dataset.imgHover;
    target.dataset.imgHover = target.src;
    target.srcset = '';

    if (imgHover) {
      target.src = imgHover;
    }
  });

  retail.addEventListener('mouseout', event => {
    let target = event.target;
    let imgHover = target.dataset.imgHover;
    target.dataset.imgHover = target.src;

    if (imgHover) {
      target.src = imgHover;
    }
  });
});
