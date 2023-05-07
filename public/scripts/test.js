import * as functions from './functions.js';

console.log('Hrllo')

let mybtn = document.getElementById('go');

const getCaptions = async (e) => {
    e.preventDefault();
    console.log(e.srcElement.href)
    //Make fetch request to this URL!
    //Use DOM Parser to get p elements into a big string....
}

mybtn.addEventListener('click', async () => {
    console.log('click');
      let options = {
        method: 'GET',
        headers: {
          'X-RapidAPI-Key': 'ebf7749346mshaa94e9ad5723107p177984jsn856b99838357',
          'X-RapidAPI-Host': 'youtube-video-stream-download.p.rapidapi.com'
        }
      };
      try {
        let res = await fetch('https://youtube-video-stream-download.p.rapidapi.com/api/v1/Youtube/getAllDetails/OdW2ENwEIVc', options);
        let data = await res.json();
        console.log(data.data, data.message, data.status, data.data.captions);
        //data.data.captions contains an array of available caption langauges
        let availableCaps = data.data.captions;
        console.log(availableCaps);
        console.log(availableCaps.length);
        //Loop through and extract language and url and output as buttons
        let availableCapsContainer = document.createElement('div');
        availableCapsContainer.id = 'caps-container';
        for(let i=0; i<availableCaps.length; i++) {
            console.log(availableCaps[i].language);
            console.log(availableCaps[i].url);
            let cap = document.createElement('a');
            cap.href = availableCaps[i].url;
            cap.innerHTML = availableCaps[i].language;
            cap.addEventListener('click', getCaptions);
            availableCapsContainer.appendChild(cap);
        }
        document.querySelector('body').appendChild(availableCapsContainer);

      }
      catch (err){
        console.log(err);
      }

//       parser = new DOMParser();
// xmlDoc = parser.parseFromString(text,"text/xml");


// let request = functions.prepareRequest(url, params, null, options);
// console.log(request);
// let response = await functions.makeRequest(request.url, request.options);
// console.log(response);
// .then(response => response.json())
// 	.then(response => console.log(response))
// 	.catch(err => console.error(err));
})


