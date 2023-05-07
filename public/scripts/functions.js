

//GENERIC FUNCTIONS WHICH ARE CALLED FROM MULTIPLE PLACES WITHIN THE APPLICATION

export const prepareRequest = function (url, queryParams, data, options) {
  if(options.method === 'POST') {
    let reqData = new FormData();
    for(let i=0; i<data.length; i++) {
        for (let property in data[i]) {
            reqData.append(property, data[i][property]);
          }
    }
    options.body = reqData;
  } else if (options.method === 'GET') {
    url = new URL(url);
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
  let focusArticleHtml = 
    `
      <div id="saved-words"></div>
      <div id="title-header">
        <input type="hidden" id="content-type" value="news"/>
        <h5 id="focus-article-title">${articleContent[0].innerText}</h5>
        <p id="focus-article-topic">${articleContent[7].innerText}</p>
        <p id="focus-article-author">${articleContent[1].innerText}</p>
        <p id="focus-article-published_date">${articleContent[2].innerText}</p>
        <p id="focus-article-language">${articleContent[3].innerText}</p>
        <p id="focus-article-country">${articleContent[4].innerText}</p>
      </div>
      <div id="article-contents">
      </div>
    `;
  container.innerHTML = focusArticleHtml;
  let articleContentsArr = articleContent[6].innerText.split(" ");
  let articleContentsFormattedHtml = ``;
  for(let i = 0; i < articleContentsArr.length; i++) {
    articleContentsFormattedHtml += `<span class=word><p>${articleContentsArr[i]}</p></span>`;
  }
  let articleContents = document.getElementById('article-contents');
  articleContents.innerHTML = articleContentsFormattedHtml;
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