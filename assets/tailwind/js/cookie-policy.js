//document.body.addEventListener('DOMContentLoaded', () => {
const accept = document.getElementById('accept-cookie');
if(accept !== null) {
    accept.addEventListener('click', () => {
        let date = new Date();
        date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
        let expires = "; expires=" + date.toUTCString();
        document.cookie = 'accept-cookie' + '=' + ('accepted' || '') + expires + "; path=/";
        document.getElementById('cookie-alert').remove();
    });
}

//});