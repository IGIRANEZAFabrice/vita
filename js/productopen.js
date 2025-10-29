// Image gallery functionality
function changeImage(thumbnail, imageUrl) {
  document.getElementById("mainImage").src = imageUrl;
  document
    .querySelectorAll(".thumbnail")
    .forEach((t) => t.classList.remove("active"));
  thumbnail.classList.add("active");
}

// Tab functionality
function openTab(evt, tabName) {
  const tabContents = document.getElementsByClassName("tab-content");
  for (let i = 0; i < tabContents.length; i++) {
    tabContents[i].classList.remove("active");
  }

  const tabs = document.getElementsByClassName("tab");
  for (let i = 0; i < tabs.length; i++) {
    tabs[i].classList.remove("active");
  }

  document.getElementById(tabName).classList.add("active");
  evt.currentTarget.classList.add("active");
}

// Quantity controls
function incrementQuantity() {
  const input = document.getElementById("quantity");
  const currentValue = parseInt(input.value);
  if (currentValue < parseInt(input.max)) {
    input.value = currentValue + 1;
  }
}

function decrementQuantity() {
  const input = document.getElementById("quantity");
  const currentValue = parseInt(input.value);
  if (currentValue > parseInt(input.min)) {
    input.value = currentValue - 1;
  }
}

// Star rating input
document.querySelectorAll(".star-rating-input i").forEach((star) => {
  star.addEventListener("click", function () {
    const rating = this.getAttribute("data-rating");
    document.querySelectorAll(".star-rating-input i").forEach((s, index) => {
      if (index < rating) {
        s.classList.remove("far");
        s.classList.add("fas");
      } else {
        s.classList.remove("fas");
        s.classList.add("far");
      }
    });
  });
});
