document.addEventListener('DOMContentLoaded', function () {
    if (Notification.permission !== "granted")
        Notification.requestPermission();
});

function Notifications() {
    var _this = this;

    _this.notify = function(title, message, icon, url) {
        if (Notification.permission === "granted") {
            if (!icon) {
                icon = app.notification.logo;
            }
            var notification = new Notification(title, {
                body: message,
                icon: icon
            });

            notification.onclick = function () {
                window.open(url);
            };
        }
    }
}