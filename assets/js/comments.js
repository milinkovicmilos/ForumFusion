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

let commentLikes = document.querySelectorAll(".comment-like");
commentLikes.forEach(x => {
    if (x.classList.contains("liked")) {
        x.addEventListener("click", unlikeEventListener);
    } else {
        x.addEventListener("click", likeEventListener);
    }
});

function likeEventListener() {
    let commentId = this.id.split("-")[1];
    let data = {
        "commentId" : commentId
    };
    let response = post(HOST + "/models/comments/likecomment.php", data);
    response.then(x => {
        switch (x.status) {
            case 200:
                likeComment(commentId);
                break;
            
            default:
                break;
        }
    });
}

function unlikeEventListener() {
    let commentId = this.id.split("-")[1];
    let data = {
        "commentId" : commentId
    };
    let response = post(HOST + "/models/comments/unlikecomment.php", data);
    response.then(x => {
        switch (x.status) {
            case 200:
                unlikeComment(commentId);
                break;
            
            default:
                break;
        }
    });
}

function likeComment(commentId) {
    let likeElement = document.querySelector(`#cl-${commentId}`);

    likeElement.classList.add("fa-solid");
    likeElement.classList.remove("fa-regular");
    let likeCountElement = likeElement.nextElementSibling;
    let likeCount = parseInt(likeCountElement.innerText);
    likeCountElement.innerText = likeCount + 1;

    likeElement.removeEventListener("click", likeEventListener);
    likeElement.addEventListener("click", unlikeEventListener);
}

function unlikeComment(commentId) {
    let likeElement = document.querySelector(`#cl-${commentId}`);

    likeElement.classList.add("fa-regular");
    likeElement.classList.remove("fa-solid");
    let likeCountElement = likeElement.nextElementSibling;
    let likeCount = parseInt(likeCountElement.innerText);
    likeCountElement.innerText = likeCount - 1;

    likeElement.removeEventListener("click", unlikeEventListener);
    likeElement.addEventListener("click", likeEventListener);
}

function loadComment(json) { 
    let commentId = json["commentId"];
    let username = json["username"];
    let text = json["text"];
    text = processText(text);
    let html = `
        <span class='comment-user'>${username}</span>
        <div class='wrapper'>
            <p>${text}</p>
            <div>
                <i id='cl-${commentId}' class='comment-like fa-regular fa-thumbs-up'></i>
                <span class='comment-like-count'>0</span>
            </div>
        </div>
    `;
    let comment = document.createElement("div");
    comment.classList.add("comment");
    comment.innerHTML = html;
    let likeElement = comment.querySelector(".comment-like");
    likeElement.addEventListener("click", likeEventListener);
    document.querySelector("#comments-container h2").after(comment);
}

function checkForEmpty() {
    let message = document.querySelector("#comments-container h3");
    if (message) {
        message.remove();
    }
}