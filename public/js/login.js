
const showLoginForm = () => {
    loginFormContainer.style.display = 'block';
    loginFormBackground.style.display = 'block';
}

const closeLoginForm = () => {
    loginFormContainer.style.display = 'none';
    loginFormBackground.style.display = 'none';
}

const switchForms = () => {
    let x = window.getComputedStyle(regDataContainer).display;
    if (x == 'block'){
        regDataContainer.style.display = 'none';
        loginDataContainer.style.display = 'block';
    } else {
        loginDataContainer.style.display = 'none';
        regDataContainer.style.display = 'block';
    }
}
