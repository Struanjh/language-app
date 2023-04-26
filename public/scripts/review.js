
import * as functions from './functions.js';

const collapsibleElements = document.querySelectorAll('button.collapsible');
const contentElements = document.querySelectorAll('.content');

console.log(contentElements);

collapsibleElements.forEach(
    btn => {
        btn.addEventListener('click', (
            e => {
                let content = e.target.nextElementSibling;
                if (content.style.display === "block") {
                    content.style.display = "none";
                  } else {
                    content.style.display = "block";
                  }
            }
        ));
        btn.addEventListener("mouseenter", 
            e => e.target.style.opacity = '0.8'
        );
        btn.addEventListener("mouseleave", 
            e => e.target.style.opacity = '1'
        );
    }
)


contentElements.forEach(
    content => {
        content.addEventListener('click',
            async e => {
                if(e.target.nodeName === 'LI') {
                    let contentType = e.target.parentElement.getAttribute('data-content-type');
                    let contentId = e.target.getAttribute('content-id');
                    window.location.href = `/language-app/public/views/review-page.php?content_id=${contentId}&content_type=${contentType}`;
                } else if (e.target.nodeName === 'BUTTON') {
                    console.log('DELETE THE ELEMENT');
                    let contentType = e.target.parentElement.parentElement.getAttribute('data-content-type');
                    let contentId = e.target.parentElement.getAttribute('content-id');
                    let request = functions.prepareRequest(
                        '/language-app/public/views/review.php', 
                        null, 
                        [
                            {
                                'deleteContent': true,
                                'contentId': contentId,
                                'contentType': contentType
                            }
                        ],  
                        {
                            method: 'POST'
                        }
                    );
                    let res = await functions.makeRequest(request.url, request.options);
                    if(res.success === true) {
                        e.target.parentElement.remove()
                    }
                }
            }
        )
    }
)