
  //Select DOM Elements
  const newsSearchBtn = document.getElementById('news-search-submit');
  const searchResults = document.querySelector('div#search-results');
  let searchTerms = document.getElementById('search-terms');
  let searchTopic = document.getElementById('search-topic');
  let searchLang = document.getElementById('search-lang');
  let errorMsg = document.querySelector('h4.errMsg');
  let pageNum;

  newsSearchBtn.addEventListener('click', (e) => {
    e.preventDefault();
    //Query Param is mandatory for API so end function if no value provided by user
    if(document.getElementById('search-terms').value == '') {
      return errorMsg.textContent = 'Please complete all fields before submitting the form!';
    }
    prepareRequest(false);
  })

  const prepareRequest = function (paginate) {
    switch(true) {
      case paginate === 'up':
        pageNum ++;
        break;
      case paginate === 'down':
        pageNum --;
        break;
      case paginate === false:
        pageNum = 1;
    }
    //Create URL OBJ
    let newsApiUrl = new URL('https://newscatcher.p.rapidapi.com/v1/search_enterprise');
    let params = {  q: searchTerms.value, 
                    topic: searchTopic.value,
                    lang: searchLang.value, 
                    sort_by: 'relevancy', 
                    page: pageNum
                };
    //Add search params to URL Obj
    newsApiUrl.search = new URLSearchParams(params).toString();
    let headers = {
      'X-RapidAPI-Key': 'ebf7749346mshaa94e9ad5723107p177984jsn856b99838357',
      'X-RapidAPI-Host': 'newscatcher.p.rapidapi.com'
    };
    fetchResults(newsApiUrl, 'GET', headers)
  } 
  
  const fetchResults = async function (url, method, headers) {
    try {
      const response = await fetch(url, {
        method: method,
        headers: headers
      })
      let results = await response.json();
      console.log('results', results);
      displaySearchResults(results);
    } catch (error) {
      console.log('Fetch error: ', error);
    }
  }

  const displaySearchResults =  async function (results) {
    //Your total results are....!
    let currPage = results.page;
    let totalPages = results.total_pages;
    let totalResults = results.total_hits;
    let articles = results.articles;
    console.log(pageNum, totalPages, totalResults, articles);
    let html = '';
    let resultHeading = `
                        <p>Page ${currPage} of ${totalPages}. Total results: ${totalResults}</p>
                        <p>Click button to jump to next page. </p>
                        <button href="#" class="previous-page">&#8249;</button>
                        <button href="#" class="next-page">&#8250;</button>
                        `
    html += resultHeading;
    for(let i=0; i<articles.length; i++) {
      console.log(articles[i]);
      let articleHtml = `<div class="article">
                          <h4>${articles[i]['title']}</h4>
                          <p>View Original Article: <a href="${articles[i]['link']}" target="_blank">${articles[i]['link']}</a></p>
                          <p>AUTHOR: ${articles[i]['author']}</p>
                          <p>PUBLISHED ON: ${articles[i]['published_date']}</p>
                          <p>LANGUAGE: ${articles[i]['language']}</p>
                          <p>COUNTRY: ${articles[i]['country']}</p>
                          <button class="read-more">Read More</button>
                          <p style="display: none;">${articles[i]['summary']}</p>
                        </div>`;
      html += articleHtml;
    }
    searchResults.innerHTML = html;
    //---ADD EVENT LISTENERS----//
    document.querySelectorAll('button.read-more').forEach(item => {
      item.addEventListener('click', () => {
        let articleText = item.nextElementSibling;
        if (articleText.style.display === "none") {
          articleText.style.display = "block";
          item.textContent = 'Hide';
        } else {
          articleText.style.display = "none";
          item.textContent = 'Read More';
        }
      })
    })
    document.querySelector('button.previous-page').addEventListener('click', () => {
      console.log('BACK');
      prepareRequest('down');
    })
    document.querySelector('button.next-page').addEventListener('click', () => {
      console.log('FORWARD');
      prepareRequest('up');
    })
}

    
   