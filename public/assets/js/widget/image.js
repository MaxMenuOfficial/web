(function waitForImageModalReady() {
  if (!document.getElementById("image-modal") || !document.querySelector("img.expandable-image")) {
    return requestAnimationFrame(waitForImageModalReady);
  }

  const modal = document.getElementById("image-modal");
  const modalImg = document.getElementById("image-modal-content");
  const closeBtn = document.getElementById("image-modal-close");

  document.querySelectorAll("img.expandable-image").forEach(function (img) {
    console.log("🖼️ Imagen lista:", img.src);

    img.addEventListener("click", function () {
      console.log("🚀 Click en imagen:", this.src);
      modalImg.src = this.src;
      modal.classList.add("show");
    });
  });

  closeBtn.addEventListener("click", function () {
    modal.classList.remove("show");
    modalImg.src = "";
  });

  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      modal.classList.remove("show");
      modalImg.src = "";
    }
  });

  // 📱 Swipe-down para cerrar modal en móviles
  let touchStartY = 0;
  modal.addEventListener("touchstart", function (e) {
    touchStartY = e.changedTouches[0].screenY;
  });

  modal.addEventListener("touchend", function (e) {
    let touchEndY = e.changedTouches[0].screenY;
    if (touchEndY - touchStartY > 100) {
      modal.classList.remove("show");
      modalImg.src = "";
    }
  });
})();