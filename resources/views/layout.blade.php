<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feed</title>
    <link rel="stylesheet" href="/styles/style.css">
    <script src="js/login.js"></script>
</head>
<body>
<div id="loginFormBackground" class="loginFormBackground" onclick="closeLoginForm()"></div>
<div id="loginFormContainer" class="loginFormContainer">
    <button class="closeLoginFormButton" onclick="closeLoginForm()">
        <svg fill="#232323" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px"><path d="M 4.7070312 3.2929688 L 3.2929688 4.7070312 L 10.585938 12 L 3.2929688 19.292969 L 4.7070312 20.707031 L 12 13.414062 L 19.292969 20.707031 L 20.707031 19.292969 L 13.414062 12 L 20.707031 4.7070312 L 19.292969 3.2929688 L 12 10.585938 L 4.7070312 3.2929688 z"/></svg>
    </button>
    <div id="loginDataContainer">
        <div class="loginFormHead">
            <div class="loginFormHeadGrid">
                <strong class="loginFormLabel">Log In</strong>
                <a class="loginFormHeadSwitcher" onclick="switchForms()">Sign Up</a>
            </div>
        </div>
        <div class="loginForm">
            <p class="loginFormInputTitle">username:</p>
            <input class="loginFormInput" type="text" placeholder="username">
            <p class="loginFormInputTitle">password:</p>
            <input class="loginFormInput" type="password" placeholder="***************">
            <button class="loginFormButton">Log In</button>
        </div>
    </div>
    <div id="regDataContainer">
        <div class="loginFormHead">
            <div class="loginFormHeadGrid">
                <strong class="loginFormLabel">Sign Up</strong>
                <a class="loginFormHeadSwitcher" onclick="switchForms()">Log In</a>
            </div>
        </div>
        <div class="loginForm">
            <p class="loginFormInputTitle">username:</p>
            <input class="loginFormInput" type="text" placeholder="username">
            <p class="loginFormInputTitle">email:</p>
            <input class="loginFormInput" type="text" placeholder="exmaple@gmail.com">
            <p class="loginFormInputTitle">password:</p>
            <input class="loginFormInput" type="password" placeholder="***************">
            <button class="loginFormButton">Sign Up</button>
        </div>
    </div>
</div>

<header>
    <div class="headerContent">
        <a class="headerLogo">Lottery</a>
        <a class="headerLoginButton" onclick="showLoginForm()">Log in</a>
    </div>
</header>

@yield('content')

<footer>
    <div class="footerContent">
        <a class="footerLogo">Lottery</a>
        <strong class="footerEmail">Somelottery@gmail.com</strong>
    </div>
</footer>
</body>
</html>
