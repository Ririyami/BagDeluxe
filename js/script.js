navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   profile.classList.remove('active');
}

profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   navbar.classList.remove('active');
}

window.onscroll = () =>{
   navbar.classList.remove('active');
   profile.classList.remove('active');
}

function loader(){
   document.querySelector('.loader').style.display = 'none';
}

function fadeOut(){
   setInterval(loader, 2000);
}

window.onload = fadeOut;

document.querySelectorAll('input[type="number"]').forEach(numberInput => {
   numberInput.oninput = () =>{
      if(numberInput.value.length > numberInput.maxLength) numberInput.value = numberInput.value.slice(0, numberInput.maxLength);
   };
});

document.addEventListener('DOMContentLoaded', () => {
   const userBtn = document.getElementById('user-btn');
   const profile = document.querySelector('.profile');

   // Toggle the profile section visibility
   userBtn.addEventListener('click', () => {
      profile.classList.toggle('active');
   });

   // Close the profile section when clicking outside
   document.addEventListener('click', (e) => {
      if (!userBtn.contains(e.target) && !profile.contains(e.target)) {
         profile.classList.remove('active');
      }
   });
});