
import * as functions from './functions.js';
import {translationApiKey} from '../../config.js';

//DOCS: https://mymemory.translated.net/doc/spec.php

const myMemoryApiKey = translationApiKey;
const synth = window.speechSynthesis;
//SELECT DOM ELEMENTS
const translateBtn = document.getElementById('translate-btn');
const switchBtn = document.getElementById('switch');
const srcTxt = document.getElementById('src-lang-text');
const srcLng = document.getElementById('src-lang');
const speechButtons = document.querySelectorAll('button.speech');
const translateLngSpeech = document.getElementById('translate-lang-speech');
const translateTxt = document.getElementById('translate-lang-text');
const translateLng = document.getElementById('translate-lang');
const toggleOn = document.querySelector('i.fa-toggle-on');
const toggleOff = document.querySelector('i.fa-toggle-off');
const translatorTool = document.querySelector('div.translate-content-container');
let errMsg = document.getElementById('error-translating-msg');
let addToSavedWordsBtn = document.getElementById('add-to-saved-words');
let langPair;
let savedWordsContainer;

speechButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        let speech;
        if(btn.classList.contains('src-lang')) {
            speech = new SpeechSynthesisUtterance(srcTxt.value);
            speech.lang = srcLng.value;
        } else {
            speech = new SpeechSynthesisUtterance(translateTxt.value);
            speech.lang = translateLng.value;
        }
        synth.speak(speech);
    })
})


switchBtn.addEventListener('click', (e) => {
    e.preventDefault();
    //Store source text & lang in var's so their values aren't lost
    let tempLng = srcLng.value, tempText = srcTxt.value;
    srcLng.value = translateLng.value;
    srcTxt.value = translateTxt.value;
    translateLng.value = tempLng;
    translateTxt.value = tempText;
})


//VALIDATE FORM WAS POPULATED CORRECTLY
translateBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    console.log('TEXT', srcTxt.value);
    if(!translatorValidation()) return;
    langPair = srcLng.value.concat("|", translateLng.value);
    let params = { 
        q: srcTxt.value, 
        langpair: langPair
    };
    let request = functions.prepareRequest(
        'https://api.mymemory.translated.net/get?', 
        params, 
        null, 
        { method: 'GET' }
        );
    console.log(request);
    let response = await functions.makeRequest(request.url, request.options);
    console.log(response);
    translateTxt.value = response.responseData.translatedText;
  })


  //HANDLES USER SAVING WORDS FROM TRANSLATOR TO SAVED WORDS PANEL......
  addToSavedWordsBtn.addEventListener('click', () => {
    if(!translatorValidation()) return;
    savedWordsContainer = document.getElementById('saved-words');
    if(!savedWordsContainer.hasChildNodes()) {
        //No words added yet, so create save button....
        let sendWordsDbBtn = document.createElement('i');
        sendWordsDbBtn.classList.add('fa-solid', 'fa-xl', 'fa-floppy-disk', 'send-words-db');
        savedWordsContainer.appendChild(sendWordsDbBtn);
        sendWordsDbBtn = document.querySelector('i.send-words-db');
        sendWordsDbBtn.addEventListener('click', sendSavedWordsToDb);
    }
    let newWordsContainer = document.createElement('div');
    newWordsContainer.classList.add('word-container');
    newWordsContainer.setAttribute("data_targ_lang", translateLng.value);
    newWordsContainer.setAttribute("data_src_lang", srcLng.value);
    newWordsContainer.setAttribute("data_targ_lang_content", translateTxt.value);
    newWordsContainer.setAttribute("data_src_lang_content", srcTxt.value);
    newWordsContainer.innerHTML = 
        `
        <i class="fa-regular fa-circle-xmark remove-saved-word-btn"></i>
        <h3>${translateTxt.value}</h3>
        <p>${srcTxt.value}</p>
        `;
    savedWordsContainer.appendChild(newWordsContainer);
    translateTxt.value = '';
    srcTxt.value = '';
    let removeSavedWordBtns = document.querySelectorAll('i.remove-saved-word-btn');
    for (const button of removeSavedWordBtns) {
        button.addEventListener('click', e =>  e.target.parentElement.remove());
    }
  })

const translatorValidation = () => {
    if(srcTxt.value == '') {
        errMsg.textContent = 'Enter some text to translate';
        return false;
    } else if (srcLng.value === translateLng.value) {
        errMsg.textContent = 'Select a different language to translate';
        return false;
    } else {
        return true;
    }
}


const sendSavedWordsToDb = async () => {
    console.log(savedWordsContainer, typeof(savedWordsContainer));
    if(!savedWordsContainer.hasChildNodes()) {
        console.log('NO WORDS');
        return;
    }
    let data = {
        requestIdentifier: 'newWords',
        contentType: document.getElementById('content-type').value,
        contentDetails: null,
        words: []
    }

    ///-------------NEWS------------------//
    if(data.contentType == 'news') {
        let article = {};
        const articleDetails = document.getElementById('title-header');
        for (const child of articleDetails.children) {
            let attribute, value;
            attribute = child.getAttribute('id');
            attribute = attribute.substring(14);
            value = child.textContent;
            value = value.split(':').pop();
            article[attribute] = value;
            console.log('ARTICLEINFO', article);
        }
        data.contentDetails = article;
    } else {
        const videoDetails = document.getElementById('video-container');
        let video = {};
        for(const child of videoDetails.children) {
            if(child.nodeName != 'INPUT' && child.getAttribute('type') != 'hidden') continue;
            video[child.getAttribute('id')] = child.getAttribute('value');
        }
        data.contentDetails = video;

    }
    let targetWords = document.querySelectorAll('#saved-words > div');
    console.log(typeof(targetWords), targetWords);
    for (const element of targetWords) {
        let entry = {};
        entry['src_lang'] = element.getAttribute('data_src_lang');
        entry['targ_lang'] = element.getAttribute('data_targ_lang');
        entry['src_lang_content'] = element.getAttribute('data_src_lang_content');
        entry['targ_lang_content'] = element.getAttribute('data_targ_lang_content');
        data.words.push(entry);
    }
    console.log('PREPARED DATA SENT TO SERVER', data);
    let res = await functions.makeRequest(
        '/language-app/routers/addContent.php', 
        {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            }
        }
    );
    console.log(res);
}