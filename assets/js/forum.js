let currentPageNumber = 1;

fetchPosts();

let searchButton = document.querySelector("section#search input[type='submit']");
searchButton.addEventListener("click", e => {
    e.preventDefault();
    currentPageNumber = 1;
    fetchPosts();
});

async function fetchPosts() {
    let searchParams = getSearchParams();
    let response = await post(HOST + "/models/posts/getposts.php", searchParams);
    switch (response.status) {
        case 200:
            response.json().then(
                json => {
                    loadPosts(json["posts"]);
                    showPages(json["postCount"]);
                }
            )
            break;
    
        default:
            showGetPostsError();
            break;
    }
}

function getSearchParams() {
    let params = new URLSearchParams(window.location.toString());
    let forumId = params.get("forumId");
    let search = document.querySelector("section#search input[type='text']").value;
    let sort = document.querySelector("section#search #sort").value;
    let perPage = document.querySelector("section#search #perPage").value;
    let filters = getFilters();

    return {
        "forumId": forumId,
        "search": search,
        "sort": sort,
        "perPage": perPage,
        "pageNumber": currentPageNumber,
        "filters": filters
    };
}

function getFilters() {
    let filters = document.querySelectorAll("#filters input[type='checkbox']");
    let arr = [];
    for (const element of filters) {
        if (element.checked) {
            let filterId = parseInt(element.value.split("-")[1]);
            arr.push(filterId);
        }
    }
    return arr;
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
                <a class='reset-link' href='index.php?page=post&postId=${element["id"]}'>
                    <div class='flex-container cnt-between'>
                        ${img}
                        <div class='post-text-wrapper'>
                            <h3>${element["title"]}</h3>
                            <p class='clamp-text'>${element["text"]}</p>
                        </div>
                        <span class='post-info'>
                            <p class='post-author'>Post by : ${element["username"]}</p>
                            <i class='${liked} fa-thumbs-up'></i>${element["like_count"]}
                            <span class='end'>${element["tags"] != null ? element["tags"] : ""}</span>
                        </span>
                    </div>
                </a>
            </div>
        `;
    }
    return $html;
}

function showPages(postCount) {
    if (!postCount) {
        return;
    }
    let pageWrapper = document.createElement("div");
    pageWrapper.id = "page-wrapper";
    pageWrapper.append(makePageNumbers(postCount, currentPageNumber));
    let postsWrapper = document.querySelector("section#posts");
    postsWrapper.append(pageWrapper);
}

function makePageNumbers(postCount, currentPageNumber) { 
    let wrapper = document.createElement("div");
    let perPage = parseInt(document.querySelector("section#search #perPage").value);
    let perPageObj = {
        "1": 5,
        "2": 10,
        "3": 15
    }
    perPage = perPageObj[perPage];
    let lastPageNumber = Math.ceil(postCount / perPage);
    
    if (currentPageNumber != 1) {
        wrapper.append(previousPage());
    }

    let currentPage = document.createElement("span");
    currentPage.innerText = currentPageNumber;
    currentPage.classList.add("page-number");
    currentPage.style = "font-weight: bold";
    wrapper.append(currentPage);

    if (currentPageNumber != lastPageNumber && currentPageNumber < lastPageNumber) {
        wrapper.append(nextPage());
    }

    return wrapper;
}

function nextPage() {
    let nextPage = document.createElement("span");
    nextPage.classList.add("page-number");
    nextPage.innerText = currentPageNumber + 1;
    nextPage.addEventListener("click", () => {
        currentPageNumber++;
        fetchPosts();
    });
    return nextPage;
}

function previousPage() {
    let previousPage = document.createElement("span");
    previousPage.classList.add("page-number");
    previousPage.innerText = currentPageNumber - 1;
    previousPage.addEventListener("click", () => {
        currentPageNumber--;
        fetchPosts();
    });
    return previousPage;
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