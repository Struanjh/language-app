
import * as functions from './functions.js';

const logoutBtn = document.getElementById('logout');
const showDropdown = document.querySelector('nav.nav > div.show-dropdown');
const dropdownContent = document.querySelector('nav.nav > div.show-dropdown > div.dropdown-content');


showDropdown.addEventListener('mouseover', () => {
    dropdownContent.style.display = 'flex';
})

showDropdown.addEventListener('mouseleave', () => {
    dropdownContent.style.display = 'none';
})

dropdownContent.addEventListener('mouseleave', () => {
    dropdownContent.style.display = 'none';
})

logoutBtn.addEventListener('click', async () => {
    let request = functions.prepareRequest(
        '/language-app/public/views/login.php', 
        null, 
        [{'logout': true}],  
        {method: 'POST'}
    );
    console.log(request);
    let res = await functions.makeRequest(request.url, request.options);
    console.log(res.json);
    window.location.href = "/language-app/public/views/login.php";
})

