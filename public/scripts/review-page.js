
console.log(gameData);

const total_words = gameData.length;
let counter = 0;
const targetWord = document.getElementById('target-word');
const userAnswer = document.getElementById('user-answer');
const submitAnswerBtn = document.getElementById('submit-answer');
const hint = document.getElementById('hint');
const problemWords = document.getElementById('problem-words');
console.log(total_words);
console.log(submitAnswerBtn);

const shuffle = (word) => {
    return word.split('')
    .sort(() => 0.5 - Math.random())
    .join('');
}

const endGame = () => {
    alert('Well done you\'ve completed the review');
    window.location.href = "./review.php";
}

const nextWord = () => { 
    console.log(gameData[counter]['src_lang_content'], gameData[counter]['targ_lang_content']);
    userAnswer.value = ``;
    targetWord.textContent = gameData[counter]['targ_lang_content'];
    let wordsArr = gameData[counter]['src_lang_content'].split(' ');
    console.log(wordsArr);
    let html = ``;
    for (let word of wordsArr) {
        html += `<div class="word">`;
        word = word.trim();
        word = shuffle(word);
        console.log('SHUFFLED WORD', word);
        for(let letter of word) {
            console.log('LETTER', letter);
            html += `<div class="letter">${letter}</div>`
        }
        html += `</div>`
    }
    problemWords.innerHTML = html;
}

const checkAnswer = () => {
    if(userAnswer.value === gameData[counter]['src_lang_content']) {
        counter ++;
        if(counter === total_words) {
            endGame();
        } else {
            nextWord();
        }
    }
}

const provideHint = () => {
    let provideAnswer = '';
    for (let i=0; i < gameData[counter]['src_lang_content'].length; i++) {
        provideAnswer += gameData[counter]['src_lang_content'][i];
        if(userAnswer.value[i] !== gameData[counter]['src_lang_content'][i]) {
            break
        }
    }
    userAnswer.value = provideAnswer;
}

nextWord();
submitAnswerBtn.onclick = checkAnswer;
hint.onclick = provideHint;

