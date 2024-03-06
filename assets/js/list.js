//modal

// Get DOM Elements

//const modalBtn = document.querySelector('.movie');


// Events
//modalBtn.addEventListener('click', openModal);
//closeBtn.addEventListener('click', closeModal);
//window.addEventListener('click', outsideClick);

const movies = document.querySelectorAll('.movie');
movies.forEach((movie, i) => {
  movie.addEventListener("click", (e) =>{
    let id = movie.dataset.movie;
    window.location.href = `https://mkas.softlukas.sk/rocnikovy/detail?movie=${id}`;
  })
});



