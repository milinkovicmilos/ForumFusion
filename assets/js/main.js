//#region Form Validation

const ErrorObjArr = [
    {
        "elementIds": ["firstname", "lastname"],
        "errorText": "This field is required and must start with capital letter. Max 20 characters.",
        "regex": "^[A-Z][a-z]{2,}( [A-Z][a-z]{2,})*$",
        "maxlen" : 20
    },
    {
        "elementIds": ["username"],
        "errorText": "This field is required and can only contain uppercase, lowercase letters and digits. Max 20 characters.",
        "regex": "^[A-Za-z0-9][A-Za-z0-9]{2,20}$",
        "maxlen" : 20
    },
    {
        "elementIds": ["email"],
        "errorText": "Please enter correct e-mail address (e.g. johndoe@gmail.com).",
        "regex": "^([a-z0-9]+\.?)+@[a-z]{2,}\.[a-z]{2,}$",
        "maxlen" : Infinity
    },
    {
        "elementIds": ["password", "repassword"],
        "errorText": "Password must contain at least one uppercase and lowercase letter, one digit and min 8 characters.",
        "regex": "^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,}$",
        "maxlen" : Infinity
    },
    {
        "elementIds" : ["reqUsername", "reqPassword"],
        "errorText" : "This field is required.",
        "regex" : ".+",
        "maxlen" : Infinity
    },
    {
        "elementIds" : ["forumname"],
        "errorText" : "Only lowercase letters. Max 50 characters.",
        "regex" : "^[a-z]*( [a-z]*)*$",
        "maxlen" : 50
    },
    {
        "elementIds" : ["forumdescription"],
        "errorText" : "This field is required. Max 250 characters.",
        "regex" : ".+",
        "maxlen" : 50
    },
    {
        "elementIds" : ["forumcategory"],
        "errorText" : "Please choose a category."
    },
    {
        "elementIds" : ["comment-area"],
        "errorText" : "Cannot submit empty comment. Max 1000 characters.",
        "regex" : ".+",
        "maxlen" : 1000
    },
    {
        "elementIds": ["post-title"],
        "errorText": "Title is required. Max 250 characters",
        "regex": ".+",
        "maxlen": 250
    },
    {
        "elementIds": ["post-text"],
        "errorText": "Text required. Max 2000 characters",
        "regex": ".+",
        "maxlen": 2000
    },
    {
        "elementIds": ["post-image"],
        "errorText": "Invalid file format. Only .jpg and .png image files are allowed.",
        "validFormats": ["image/jpeg", "image/png"]
    }
];

let formsForValidation = document.querySelectorAll(".validate");
formsForValidation.forEach(x => {
    validateForm(x);
});

function validateForm(form) {
    let textInputs = form.querySelectorAll("textarea, input[type='text'], input[type='password']");
    let selects = form.querySelectorAll("select");
    let files = form.querySelectorAll("input[type='file']");
    attachEventListeners(textInputs, selects, files);

    let submitBtn = form.querySelector("input[type='submit']");
    submitBtn.addEventListener("click", e => {
        e.preventDefault();
        formErrors(form);
    });
}

function attachEventListeners(textInputs, selects, files) {
    textInputs.forEach(x => {
        x.addEventListener("blur", () => {
            validateFormElement(x);
        });
    });

    selects.forEach(x => {
        x.addEventListener("change", () => {
            validateSelect(x);
        });
    });

    files.forEach(x => {
        x.addEventListener("change", () => {
            validateFile(x);
        });
    });
}

function formErrors(form) {
    let textInputs = form.querySelectorAll("textarea, input[type='text'], input[type='password']");
    let selects = form.querySelectorAll("select");
    let files = form.querySelectorAll("input[type='file']");
    textInputs.forEach(x => {
        validateFormElement(x);
    });
    selects.forEach(x => {
        validateSelect(x);
    });
    files.forEach(x => {
        validateFile(x);
    });
    let error = form.querySelector(".err-text");
    if (!error) {
        if (form.classList.contains("validate-send")) {
            form.submit();
        }
        return true;
    }
    return false;
}

function validateFormElement(formElement) {
    if (formElement.id == "repassword" &&
        formElement.value != document.querySelector("#password").value) {
        setErrorText(formElement, "Please repeat your password.");
        return;
    }
    let inputErrorObj = findErrorObj(formElement);
    let regex = new RegExp(inputErrorObj["regex"]);
    if (!regex.test(formElement.value) || formElement.value.length > inputErrorObj["maxlen"]) {
        setErrorText(formElement, inputErrorObj["errorText"]);
    }
    else {
        removeErrorText(formElement);
    }
}

function validateSelect(formElement) {
    let inputErrorObj = findErrorObj(formElement);
    if (!parseInt(formElement.value)) {
        setErrorText(formElement, inputErrorObj["errorText"]);
    }
    else {
        removeErrorText(formElement);
    }
}

function validateFile(formElement) {
    let inputErrorObj = findErrorObj(formElement);
    if (!formElement.files[0]) {
        return true;
    }
    if (!inputErrorObj["validFormats"].includes(formElement.files[0].type)) {
        setErrorText(formElement, inputErrorObj["errorText"]);
    }
    else {
        removeErrorText(formElement);
    }
}

function findErrorObj(inputElement) {
    for (const element of ErrorObjArr) {
        if (element["elementIds"].includes(inputElement.id))
            return element;
    }
    return null;
}

function setErrorText(inputElement, errorText) {
    if (document.querySelector(`.${inputElement.id}-err`)) {
        return;
    }
    let errText = document.createElement("p");
    errText.classList.add("err-text", inputElement.id + "-err");
    errText.innerText = errorText;
    inputElement.after(errText);
}

function removeErrorText(inputElement) {
    let errText = document.querySelector(`.${inputElement.id}-err`);
    if (errText) {
        errText.remove();
    }
}

//#endregion

//#region Async Functions

async function post(url, data, follow = "manual") {
    const response = await fetch(url, {
        method: "POST",
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify(data),
        follow: follow
    });
    return response;
}

//#endregion

//#region Misc


function showError(form, errorId, errorText) {
    let error = document.querySelector(`#erorr-${errorId}`);
    if (!error) {
        let error = document.createElement("p");
        error.classList.add("err-text");
        error.id = `as-error-${errorId}`;
        error.innerText = errorText;
        form.append(error);
    }
}

function removeError(form, errorId) { 
    let error = form.querySelector(`#error-${errorId}`);
    if (error) {
        error.remove();
    }
}

function processText(text) {
    return text.replaceAll("\n", "<br>");
}

//#endregion