function toggleSidebar() {
  if (window.innerWidth > 768) {
    document.getElementById("sidebar").classList.toggle("collapsed");
    document.getElementById("header").classList.toggle("collapsed");
    document.getElementById("mainContent").classList.toggle("collapsed");
    document.getElementById("footer").classList.toggle("collapsed");
  } else {
    toggleMobileSidebar();
  }
}

function toggleMobileSidebar() {
  document.getElementById("sidebar").classList.toggle("mobile-open");
  document.querySelector(".overlay").classList.toggle("active");
}
