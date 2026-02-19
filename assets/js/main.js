// Mensajes globales
function showAlert(message) {
  alert(message);
}

// Cerrar sesión
function logout() {
  window.location.href = BASE_URL + "/middleware/logout.php";
}

(function () {

    if (window.__alertifyAuthHandler__) return;
    window.__alertifyAuthHandler__ = true;

    let alertActive = false;

    $(document).ajaxError(function (event, jqXHR) {

        if (alertActive) return;

        const res = jqXHR.responseJSON;
        if (!res) return;

        if (res.code === 401 || res.code === 403) {
            alertActive = true;
            alertify.error(res.message);

            setTimeout(() => {
                alertActive = false;
            }, 2000);
        }
    });

})();