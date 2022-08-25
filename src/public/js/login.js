window.onload = function() {

    function getRedirectParam() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('query');
    }

    function savePossibleRedirectParam() {
        const redirect = getRedirectParam();
        if (redirect !== null && redirect !== '') {
            sessionStorage.setItem('helsinki-login-redirect', redirect);
        }
    }

    function insertRedirectToLoginForm() {
        const input = document.querySelector('.login-submit input[name="redirect_to"]');
        if (input != null) {
            if (input.value !== null && input.value === '') {
                input.value = sessionStorage.getItem('helsinki-login-redirect');
            }
        }
    }

    function removeRedirectFromStorage() {
        sessionStorage.removeItem('helsinki-login-redirect')
    }

    function isUserLoggedIn() {
        if (document.body.classList.contains('logged-in')) {
            return true;
        }
        return false;
    }

    function init() {
        if (!isUserLoggedIn()) {
            savePossibleRedirectParam();
            insertRedirectToLoginForm();
        }
        else {
            removeRedirectFromStorage()
        }
    }
    init();
}