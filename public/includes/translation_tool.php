
<div class="translator-container">
    <div class="translate-content-container flex">
        <div class="src-lang-container">
            <textarea placeholder="Enter text" name="src-lang-text" id="src-lang-text" cols="30" rows="5"></textarea>
        </div>
        <div class="translate-lang-container">
            <textarea placeholder="Translation" name="translate-lang-text" id="translate-lang-text" cols="30" rows="5" readonly disabled></textarea>
        </div>
        <div class="control-bar flex">
            <button class="speech src-lang"><i class="fa-solid fa-volume-high"></i></button>
            <select name="src-lang" id="src-lang">
                <option value="" selected disabled>Translate From Lang</option>
                <option value="en-GB">English</option>
                <option value="ja-JP">Japanese</option>
                <option value="ko-KR">Korean</option>
                <option value="fr-FR">French</option>
                <option value="de-DE">German</option>
                <option value="en-ES">Spanish</option>
            </select>
            <button id="switch"><i class="fa-solid fa-repeat"></i></button>
            <button class="speech translate-lang"><i class="fa-solid fa-volume-high"></i></button>
            <select name="translate-lang" id="translate-lang">
                <option value="" selected disabled>Translate To Lang</option>
                <option value="en-GB">English</option>
                <option value="ja-JP">Japanese</option>
                <option value="ko-KR">Korean</option>
                <option value="fr-FR">French</option>
                <option value="de-DE">German</option>
                <option value="en-ES">Spanish</option>
            </select>
        </div>
        <div class="error-container">
            <p id="error-translating-msg"></p>
        </div>
        <div class="button-container">
            <button type="button" id="translate-btn">Translate</button>
            <button type="button" id="add-to-saved-words">Add New Word</button>
        </div>
    </div>
</div>
