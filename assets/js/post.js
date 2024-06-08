let postLikeElement = document.querySelector("section#post i");
if (postLikeElement.classList.contains("liked-post")) {
    postLikeElement.addEventListener("click", unlikePostEventListener);
} else {
    postLikeElement.addEventListener("click", likePostEventListener);
}

function likePostEventListener() {
    let params = new URLSearchParams(window.location.toString());
    let postId = params.get("postId");
    let data = {
        "postId" : postId
    };
    let response = post(HOST + "/models/posts/likepost.php", data);
    response.then(x => {
        switch (x.status) {
            case 200:
                likePost(postId);
                break;
            
            default:
                break;
        }
    });
}

function unlikePostEventListener() {
    let params = new URLSearchParams(window.location.toString());
    let postId = params.get("postId");
    let data = {
        "postId" : postId
    };
    let response = post(HOST + "/models/posts/unlikepost.php", data);
    response.then(x => {
        switch (x.status) {
            case 200:
                unlikePost();
                break;
            
            default:
                break;
        }
    });
}

function likePost() {
    let likeElement = document.querySelector("section#post i");

    likeElement.classList.add("fa-solid");
    likeElement.classList.remove("fa-regular");
    let likeCountElement = likeElement.nextElementSibling;
    let likeCount = parseInt(likeCountElement.innerText);
    likeCountElement.innerText = likeCount + 1;

    likeElement.removeEventListener("click", likePostEventListener);
    likeElement.addEventListener("click", unlikePostEventListener);
}

function unlikePost() {
    let likeElement = document.querySelector("section#post i");

    likeElement.classList.add("fa-regular");
    likeElement.classList.remove("fa-solid");
    let likeCountElement = likeElement.nextElementSibling;
    let likeCount = parseInt(likeCountElement.innerText);
    likeCountElement.innerText = likeCount - 1;

    likeElement.removeEventListener("click", unlikePostEventListener);
    likeElement.addEventListener("click", likePostEventListener);
}