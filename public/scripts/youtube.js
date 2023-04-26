
//https://www.captionsgrabber.com/

import {youTubeApiKey} from '../../config.js';
import * as functions from './functions.js';

const videoSubmitBtn = document.getElementById('video-submit');
const url = document.getElementById('video-url');
const errorMsg = document.querySelector('p.errorMsg');
const iFrameContainer = document.getElementById('video-container');
const resultsContainer = document.getElementById('results-container');
const captionsContainer = document.getElementById('captions-contents');
const translatorTool = document.querySelector('div.translator-container');
const formContainer = document.querySelector('div.form-container');
const formResults = document.querySelector('div.form-container > div.search-results');
const focusArticleContainer = document.querySelector('div.focus-article');
let videoInfo;


/////-------------------MOVE TO CSS FILE OR PHP------------------------//
translatorTool.style.display = 'none';
focusArticleContainer.style.display = 'none';

const addWordToTranslateTool = (e) => {
        console.log(e.target.nodeName);
        if(e.target.nodeName === "P" || e.target.nodeName === "SPAN") {
            let newWord = e.target.innerText;
            console.log(newWord);
            document.getElementById('src-lang-text').value += ` ${newWord}`;
        }
  }

const getApiData = async (videoID) => {
    let req = functions.prepareRequest(
        `https://youtube-video-stream-download.p.rapidapi.com/api/v1/Youtube/getAllDetails/${videoID}`,
        null,
        null,
        {
            method: 'GET',
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-RapidAPI-Key": youTubeApiKey,
                "X-RapidAPI-Host": 'youtube-video-stream-download.p.rapidapi.com'
            }
        }
    )
    try {
        let data = await functions.makeRequest(req.url, req.options);
        return data;
    } catch(e) {
        console.error(e);
    }
}

const validateUrl = function () {
    console.log(url.value);
    let regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    let match = url.value.match(regExp);
    if (match && match[2].length === 11) {
        console.log(match);
        console.log(match[2]);
        return match[2];
    } else {
        errorMsg.textContent = "Please enter a valid URL";
        return false;
    }
}


const makeRequest = async function (url, method, headers, params) {
    let options = {};
    options.method = method; 
    options.headers = headers; 
    return fetch(url, options);
}

const displayAvailableCaptions = (data) => {
    //Extract relevant Info from API res
    console.log("DATA", data);
    videoInfo = {
        'videoID': data.data.id,
        'videoTitle': data.data.title,
        'videoUploadDate': data.data.uploadDate,
        'videoDuration': data.data.duration,
        'videoUrl': data.data.url,
        'availableCaps': data.data.captions,
        'channelTitle': data.data.channelTitle,
        'channelUrl': data.data.channelUrl
    };
    // //data.data.captions contains an array of available caption langauges
    if(videoInfo.availableCaps.length === 0) {
        formResults.innerHTML = '<h6>No captions available for this video. Try another URL</h6>';
        return;
    }
    formResults.innerHTML = '<h6>Available Captions: </h6>';
    for(let i=0; i<videoInfo.availableCaps.length; i++) {
        let cap = document.createElement('a');
        cap.href = videoInfo.availableCaps[i].url;
        cap.innerHTML = videoInfo.availableCaps[i].language;
        cap.addEventListener('click', getCaptions);
        formResults.appendChild(cap);
    }
}

const displayCaptionResults = (captionsXML) => {
    console.log(captionsXML);
    let text = captionsXML.getElementsByTagName("s");
    let htmlResults = '';
    for(let i = 0; i < text.length; i++) {
        htmlResults += `<span class=word><p>${text[i].textContent}</p></span>`;
    }
    console.log(htmlResults);
    return htmlResults;
}

const createBackButton = () => {
  //Add Event Listener to back button --- on click clear focusArticleContainer && display none, show everything else....
  let btn = document.createElement('i');
  btn.classList.add("fa-solid", "fa-left-long", "back-btn");
  let body = document.querySelector('body');
  body.appendChild(btn);
  let backToSearchResults = document.querySelector('i.back-btn');
  backToSearchResults.addEventListener('click', () => {
    //Clear current focus article
    body.removeChild(backToSearchResults);
    focusArticleContainer.innerHTML = '';
    focusArticleContainer.style.display = 'none';
    url.value = '';
    formContainer.style.display = 'block';
  })
}

const getCaptions = async function (e) {
    e.preventDefault();
    let captionsLink = e.target.getAttribute('href');
    console.log('CAPTIONS LINK', captionsLink);
    try {
        let captionsData = await makeRequest(captionsLink, 'GET', {} , null);
        let captionsText = await captionsData.text();
        let captionsXML = new window.DOMParser().parseFromString(captionsText, "text/xml");
        let captionsHTML = displayCaptionResults(captionsXML);
        //Render focus view
        functions.renderFocusYouTubeVideoView(focusArticleContainer, videoInfo, captionsHTML);
        focusArticleContainer.style.display = 'grid';
        translatorTool.style.display = 'block';
        focusArticleContainer.appendChild(translatorTool);
        formContainer.style.display = 'none';
        formResults.innerHTML = '';
        document.getElementById('captions-contents').addEventListener('click', addWordToTranslateTool);
        createBackButton();
    } catch (e) {
        console.log(e);
    }
}

videoSubmitBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    let videoID = validateUrl();
    //Don't go any further if the url isn't valid
    if(!videoID) return;
    getApiData(videoID)
    .then(
        data => displayAvailableCaptions(data)
    );
})