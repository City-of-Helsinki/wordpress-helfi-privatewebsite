"use strict";

window.onload = function () {
  function getRedirectParam() {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('query');
  }

  function savePossibleRedirectParam() {
    var redirect = getRedirectParam();

    if (redirect !== null && redirect !== '') {
      sessionStorage.setItem('helsinki-login-redirect', redirect);
    }
  }

  function insertRedirectToLoginForm() {
    var input = document.querySelector('.login-submit input[name="redirect_to"]');
    var sessionRedirect = sessionStorage.getItem('helsinki-login-redirect');

    if (input != null) {
      if (sessionRedirect !== null && sessionRedirect !== '') {
        input.value = sessionRedirect;
      }
    }
  }

  function removeRedirectFromStorage() {
    sessionStorage.removeItem('helsinki-login-redirect');
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
    } else {
      removeRedirectFromStorage();
    }
  }

  init();
};