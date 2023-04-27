
  import {newsApiKey} from '../../config.js';
  import * as functions from './functions.js';

  
  //Select DOM Elements
  const newsApiUrl = 'https://newscatcher.p.rapidapi.com/v1/search_enterprise';
  const newsForm = document.getElementById('news-article-form');
  const newsSearchBtn = document.getElementById('news-search-submit');
  const searchResults = document.querySelector('div#search-results');
  let searchTerms = document.getElementById('search-terms');
  let searchTopic = document.getElementById('search-topic');
  let searchLang = document.getElementById('search-lang');
  let errorMsg = document.querySelector('h4.errMsg');
  let pageNum, params, options;
  const translatorTool = document.querySelector('div.translator-container');
  const focusArticleContainer = document.querySelector('div.focus-article');
  translatorTool.style.display = 'none';
  focusArticleContainer.style.display = 'none';
  let resultsNode;

  //WHEN THE SEARCH BUTTON IS CLICKED, ENSURE SEARCH FIELD IN FORM WAS COMPLETED, THEN PREPARE REQUEST TO NEWS API
  newsSearchBtn.addEventListener('click', (e) => {
    e.preventDefault();
    pageNum = 1;
    console.log('User searched terms', searchTerms);
    //Query Param is mandatory for API so end function if no value provided by user
    if(document.getElementById('search-terms').value == '') {
      return errorMsg.textContent = 'Please enter search terms before submitting the form';
    }
    params = {  
      q: searchTerms.value, 
      topic: searchTopic.value,
      lang: searchLang.value, 
      sort_by: 'relevancy', 
      page: pageNum
    };
    options = {
      method: 'GET',
      headers: {
        'X-RapidAPI-Key': newsApiKey,
        'X-RapidAPI-Host': 'newscatcher.p.rapidapi.com'
      }
    };
    organizeEvents();
  });

  const addWordToTranslateTool = e => {
    console.log(e.target.nodeName);
    if(e.target.nodeName === "P" || e.target.nodeName === "SPAN") {
        let newWord = e.target.innerText;
        console.log(newWord);
        document.getElementById('src-lang-text').value += ` ${newWord}`;
    }
  }

  //HOLDER FUNCTION FOR PREPARING THEN SENDING REQUEST, THEN DISPLAYING RETURNED RESULTS
  const organizeEvents = async () => {
    let request = functions.prepareRequest(newsApiUrl, params, null, options);
    let response = await functions.makeRequest(request.url, request.options);
    displaySearchResults(response);
  };

  //FUNCTION LOOPS THROUGH RETURNED RESULTS, GENERATING A DIV CARD FOR EACH ARTICLE RETURNED
  //API RETURNS 50 RESULTS (50 ARTICLES) PER PAGE
  const displaySearchResults =  async function (results) {
    let currPage = results.page;
    let totalPages = results.total_pages;
    let totalResults = results.total_hits;
    let articles = results.articles;
    if(resultsNode) resultsNode.remove();
    resultsNode = document.createElement('div');
    resultsNode.classList.add('results-heading');
    resultsNode.innerHTML = `
      <p>Page ${currPage} of ${totalPages}. Total results: ${totalResults}</p>
      <p>Click button to jump to next page. </p>
      <button href="#" class="previous-page">&#8249;</button>
      <button href="#" class="next-page">&#8250;</button>
    `;
    let html = '';
    for(let i=0; i<articles.length; i++) {
      articles[i]['published_date'] = new Date(articles[i]['published_date']).toLocaleDateString();
      let articleHtml = 
        `
        <div class="article flex">
          <h4>TITLE: ${articles[i]['title']}<a href="${articles[i]['link']}" target="_blank"></a></h4>
          <p>AUTHOR ✍️ : ${articles[i]['author']}</p>
          <p>PUBLISHED ON 🕒 : ${articles[i]['published_date']}</p>
          <p>LANGUAGE 🗣 : ${articles[i]['language']}</p>
          <p>COUNTRY 🌏 : ${articles[i]['country']}</p>
          <button class="read-more"><i class="fa-solid fa-graduation-cap"></i>Study Article Contents</button>
          <p style="display: none;">${articles[i]['summary']}</p>
          <p hidden>${searchTopic.value}</p>
        </div>
        `;
      html += articleHtml;
    }
    newsForm.after(resultsNode);
    searchResults.innerHTML = html;
    resultsNode.scrollIntoView({ behavior: "smooth" });

    //---ADD EVENT LISTENERS TO RENDER FOCUS MODE ON CLICKED ARTICLE----//
    document.querySelectorAll('button.read-more').forEach(item => {
      item.addEventListener('click', () => {
        let articleContent = item.parentElement.children;
        console.log('Article content', articleContent);
        functions.renderFocusNewsArticleView(focusArticleContainer, articleContent);
        focusArticleContainer.style.display = 'grid';
        //HIDE: Search form and all search results..
        console.log(translatorTool);
        translatorTool.style.display = 'block';
        focusArticleContainer.appendChild(translatorTool);
        newsForm.style.display = 'none';
        searchResults.style.display = 'none';
        document.getElementById('article-contents').addEventListener('click', addWordToTranslateTool);
        createBackButton();
      })
    })
     //---ON PREV/NEXT PAGE CLICK, NEWS API IS CALLED AGAIN WITH PAGE PARAM INCREMENTED/DECREMENTED----//
    document.querySelector('button.previous-page').addEventListener('click', () => {
      if(params.page == 1) return;
      params.page --;
     organizeEvents();
    });
    document.querySelector('button.next-page').addEventListener('click', () => {
      if(params.page == results.totalPages) return;
      params.page ++;
      organizeEvents();
    });
};

  const createBackButton = () => {
      //Add Event Listener to back button --- on click clear  focusArticleContainer && display none, show everything else....
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
      newsForm.style.display = 'block';
      searchResults.style.display = 'grid';
    })
  }

