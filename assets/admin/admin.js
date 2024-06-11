let response = post(HOST + "/models/admin/getpageaccess.php");
response.then(x => {
    switch (x.status) {
        case 200:
            x.json().then(json => showStats(json))
            break;

        default:
            showError();
            break;
    }
});

function showStats(stats) {
    let count = stats.length;
    let access = {};
    stats.forEach(x => {
        access[x["page"]] != null ? access[x["page"]]++ : access[x["page"]] = 0;
    });
    renderStats(count, access);
}

function renderStats(count, access) {
    let statsContainer = document.querySelector("section#statistics .container");
    for (const [key, value] of Object.entries(access)) {
        let wrapper = document.createElement("div");

        let rangeWrap = document.createElement("div");
        rangeWrap.classList.add("range-wrapper");

        let rangeElement = document.createElement("div");
        rangeElement.classList.add("range-element");
        let val = value / count * 100
        rangeElement.value = val;
        rangeElement.style = `width: ${val}%`;

        let label = document.createElement("label");
        label.innerText = `${key} - ${value} visits`;

        rangeWrap.append(rangeElement);
        wrapper.append(label, rangeWrap);
        statsContainer.append(wrapper);
    }
}

function showError() {
    let errorText = "There was an error while fetching page access data.";
    let errorElement = document.createElement("p");
    errorElement.innerText = errorText;
    errorElement.classList.add("err-text");
    errorElement.style = "text-align: center;";
    statsContainer.append(errorElement);
}