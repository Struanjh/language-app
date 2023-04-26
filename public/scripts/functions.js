

//GENERIC FUNCTIONS WHICH ARE CALLED FROM MULTIPLE PLACES WITHIN THE APPLICATION

export const prepareRequest = function (url, queryParams, data, options) {
  if(options.method === 'POST') {
    //Add data to req body
    let reqData = new FormData();
    for(let i=0; i<data.length; i++) {
        for (let property in data[i]) {
            reqData.append(property, data[i][property]);
          }
    }
    options.body = reqData;
  } else if (options.method === 'GET') {
    url = new URL(url);
    //Add search params to URL Obj
    if(queryParams) url.search = new URLSearchParams(queryParams).toString();
  }
    console.log(url);
    console.log(options);
    return {url: url, options: options};
}

export const makeRequest = async function (url, options) {
    console.log(url, options);
    try {
        let response = await fetch(url, options);
        let data = await response.json();
        return data;
      } catch (err) {
        console.log(err);
      }
}

//CLICKED ARTICLE PASSED IN AS NODELIST
export const renderFocusNewsArticleView = (container, articleContent) => {
  //Being passed in as HTML Collection, probably need to do something to convert to make it reusable when data is being pulled from DB instead...
  console.log(typeof(articleContent), articleContent);
  //Index 6 is read more button - not needed. Assumption that focus article is always a NEW article, so not querying DB here to get existing words..........
  let focusArticleHtml = 
    `
      <div id="saved-words"></div>
      <div id="title-header">
        <input type="hidden" id="content-type" value="news"/>
        <a id="focus-article-title" href="${articleContent[1].firstElementChild.href}" target="_blank">${articleContent[0].innerText}</a>
        <p id="focus-article-topic">${articleContent[8].innerText}</p>
        <p id="focus-article-author">${articleContent[2].innerText}</p>
        <p id="focus-article-published_date">${articleContent[3].innerText}</p>
        <p id="focus-article-language">${articleContent[4].innerText}</p>
        <p id="focus-article-country">${articleContent[5].innerText}</p>
      </div>
      <div id="article-contents">
        <p>${articleContent[7].innerText}</p>
      </div>
    `;
  container.innerHTML = focusArticleHtml;
}

export const renderFocusYouTubeVideoView = (container, videoInfo, captionsContent) => {
  console.log(videoInfo);
  let focusVideoHtml =
  `
  <div id="saved-words"></div>
  <div id="video-container">
    <input type="hidden" id="content-type" value="youtube"/>
    <input type="hidden" id="video-id" value="${videoInfo.videoID}"/>
    <input type="hidden" id="video-title" value="${videoInfo.videoTitle}"/>
    <input type="hidden" id="video-upload-date" value="${videoInfo.videoUploadDate}"/>
    <input type="hidden" id="video-duration" value="${videoInfo.videoDuration}"/>
    <input type="hidden" id="video-url" value="${videoInfo.videoUrl}"/>
    <h3>${videoInfo.videoTitle}</h3>
    <iframe width="90%" height="80%" src="https://www.youtube.com/embed/${videoInfo.videoID}"></iframe>
  </div>
  <div id="captions-contents">
    ${captionsContent}  
  </div>
  `;
  container.innerHTML = focusVideoHtml;

}