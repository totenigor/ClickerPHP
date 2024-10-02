const button = document.querySelector('.clickButton');
const clickCount = document.querySelector('.clickCount');

let counter = 0;
clickCount.textContent = counter;
let clickCountValue = document.querySelector('#clickcountvalue');
clickCountValue.value = counter;

button.addEventListener('click', ()=>{
    counter++;
    clickCount.textContent = counter;
    clickCountValue.value = counter;
});