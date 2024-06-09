fetchPosts();

let searchButton = document.querySelector("section#search input[type='submit']");
searchButton.addEventListener("click", e => {
    e.preventDefault();
    fetchPosts();
});

function fetchPosts() {
    let searchParams = getSearchParams();
    let response = post(HOST + "/models/posts/getposts.php", searchParams);
    response.then(x => {
        switch (x.status) {
            case 200:
                x.json().then(
                    json => loadPosts(json)
                )
                break;
        
            default:
                showGetPostsError();
                break;
        }
    });
}

function getSearchParams() {
    let params = new URLSearchParams(window.location.toString());
    let forumId = params.get("forumId");
    let search = document.querySelector("section#search input[type='text']").value;
    let sort = document.querySelector("section#search #sort").value;
    let perPage = document.querySelector("section#search #perPage").value;

    return {
        "forumId": forumId,
        "search": search,
        "sort": sort,
        "perPage": perPage
    };
}

function loadPosts(posts) {
    let html = formPostsHtml(posts);
    let postsWrapper = document.querySelector("section#posts");
    postsWrapper.innerHTML = html;
}

function formPostsHtml(posts) {
    if (!posts.length) {
        return "<h3>There are no posts on this forum yet !</h3>";
    }
    $html = "";
    for (const element of posts) {
        let img = Boolean(element["thumbnail"]) ? `<img src='${element["thumbnail"]}'>` : "";
        let liked = Boolean(element["liked"]) ? "fa-solid" : "fa-regular";
        $html += `
            <div class='post'>
                <a class='reset-link' href='index.php?page=post&postId=$result->id'>
                    <div class='flex-container cnt-between'>
                        ${img}
                        <div class='post-text-wrapper'>
                            <h3>${element["title"]}</h3>
                            <p class='clamp-text'>${element["text"]}</p>
                        </div>
                        <span class='post-info'>
                            <p class='post-author'>Post by : ${element["username"]}</p>
                            <i class='${liked} fa-thumbs-up'></i>${element["like_count"]}
                        </span>
                    </div>
                </a>
            </div>
        `;
    }
    return $html;
}

function showGetPostsError() {
    let postsWrapper = document.querySelector("section#posts");
    let errorElement = document.createElement("h3");
    errorElement.innerText = `
        There was an error while trying to get posts for this forum.
        Try again later.
    `;
    postsWrapper.append(errorElement);
}