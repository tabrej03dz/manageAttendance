// Select the hamburger button and mobile navbar container
const hamburger = document.getElementById('hamburger');
const navbarSlide = document.getElementById('navbarSlide');

// Add event listener to the hamburger button to toggle the menu
hamburger.addEventListener('click', () => {
  // Toggle the 'hidden' class to show or hide the navbar
  navbarSlide.classList.toggle('hidden');

  // Toggle the 'open' class for the hamburger icon animation
  hamburger.classList.toggle('open');
});

document.querySelectorAll(`.navLink`).forEach((e => e.addEventListener(`click`, () => {
  // Toggle the 'hidden' class to show or hide the navbar
  navbarSlide.classList.toggle('hidden');

  // Toggle the 'open' class for the hamburger icon animation
  hamburger.classList.toggle('open');
})))



// review slider start 
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');
const sliderContainer = document.querySelector('#sliderContainer');
const slides = document.querySelectorAll('.reviewBox');
let currentIndex = 0;

// Function to move to the next slide
function goToNextSlide() {
  if (currentIndex < slides.length - 1) {
    currentIndex++;
  } else {
    currentIndex = 0; // loop back to the first slide
  }
  updateSliderPosition();
}

// Function to move to the previous slide
function goToPrevSlide() {
  if (currentIndex > 0) {
    currentIndex--;
  } else {
    currentIndex = slides.length - 1; // loop back to the last slide
  }
  updateSliderPosition();
}

// Update the slider container's position to show the correct slide
function updateSliderPosition() {
  sliderContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
}

// Event listeners for buttons
nextBtn.addEventListener('click', goToNextSlide);
prevBtn.addEventListener('click', goToPrevSlide);

// Auto-slide every 5 seconds
setInterval(goToNextSlide, 5000);
// review slider end


// heart like button
document.addEventListener('DOMContentLoaded', () => {
  console.log("DOM fully loaded");
  let likeBtn = document.querySelector(`#like`);

  // Debugging: Check if the like button is found
  console.log('likeBtn:', likeBtn);

  if (likeBtn) {
    const like = () => {
      if (likeBtn.classList.contains(`fa-regular`)) {
        likeBtn.classList.replace(`fa-regular`, `fa-solid`);
        likeBtn.classList.add(`like-animation`);
      } else {
        likeBtn.classList.replace(`fa-solid`, `fa-regular`);
        likeBtn.classList.remove(`like-animation`);
      }
    };

    likeBtn.addEventListener(`click`, like);
  } else {
    console.error("The like button is not found.");
  }
});


// share 
// Add event listener for the button click
document.getElementById("dropdownDefaultButton").addEventListener("click", function () {
  var dropdown = document.getElementById("dropdown");
  var isVisible = dropdown.classList.contains("hidden");

  // Toggle visibility of the dropdown
  if (isVisible) {
    dropdown.classList.remove("hidden"); // Show the dropdown
    dropdown.classList.add("block"); // Ensure it's visible
    this.setAttribute("aria-expanded", "true"); // Update aria-expanded to true
  } else {
    dropdown.classList.add("hidden"); // Hide the dropdown
    dropdown.classList.remove("block"); // Ensure it's hidden
    this.setAttribute("aria-expanded", "false"); // Update aria-expanded to false
  }
});



// =====

// window.addEventListener(`load`, () => {
//   const loader = document.querySelector(`.loader`);

//   loader.classList.add(`loader-hidden`);

//   loader.addEventListener(`transitionend`, () => {
//     document.body.removeChild(`loader`);
//   })
// })
// // pending