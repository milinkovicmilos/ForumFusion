let commentForm = document.querySelector("#comments form");
let sendCommentBtn = document.querySelector("#comments form input[type='submit']");
sendCommentBtn.addEventListener("click", e => {
    e.preventDefault();
    if (!formErrors(commentForm)) {
        return;
    }
    let textarea = commentForm.querySelector("textarea");
    let text = textarea.value;
    let params = new URLSearchParams(window.location.toString());
    let postId = params.get("postId");
    let data = {
        "postId" : postId,
        "text" : text
    };
    let response = post(HOST + "/models/comments/addcomment.php", data);
    response.then(x => {
        switch (x.status) {
            case 200:
                removeError(commentForm, "comment");
                x.json().then(
                    json => {
                        loadComment(json);
                        textarea.value = "";
                        checkForEmpty();
                    }
                );
                break;
            case 400:
                showError(commentForm, "comment", "Invalid request. Try again.")
                break;
            case 500:
                showError(commentForm, "comment", "Failed to add a comment. Please try again later.")
                break;
            default:
                showError(commentForm, "comment", "There was en error. Please try again later.")
                break;
        }
    });
});

function loadComment(json) { 
    let username = json["username"];
    let text = json["text"];
    text = processText(text);
    let html = `
        <span class='comment-user'>${username}</span>
        <p>${text}</p>
    `;
    let comment = document.createElement("div");
    comment.classList.add("comment");
    comment.innerHTML = html;
    document.querySelector("#comments-container h2").after(comment);
}

function checkForEmpty() {
    let message = document.querySelector("#comments-container h3");
    if (message) {
        message.remove();
    }
}