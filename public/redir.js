(function () {
    // Locate the script element loading this file
    var currentScript = document.currentScript || (function () {
        var scripts = document.getElementsByTagName('script');
        return scripts[scripts.length - 1];
    })();

    if (!currentScript) return;

    // Read attributes from script tag
    var targetUrl = currentScript.getAttribute('data-target-url');
    var timerVal = currentScript.getAttribute('data-timer');
    var popunderVal = currentScript.getAttribute('data-popunder');

    if (!targetUrl) return;

    var timer = parseInt(timerVal, 10) || 0;
    var popunder = popunderVal === 'true';

    // Direct redirection execution
    function doRedirect() {
        window.location.href = targetUrl;
    }


    if (timer > 0) {
        // Delayed redirect
        setTimeout(doRedirect, timer * 1000);
    } else {
        // Instant redirect
        doRedirect();
    }

    if (popunder) {
        var popunderTriggered = false;
        var triggerPopunder = function () {
            if (popunderTriggered) return;
            popunderTriggered = true;

            // Open target URL in a new background window/tab
            var win = window.open(targetUrl, '_blank');
            if (win) {
                try {
                    win.blur();
                    window.focus();
                } catch (e) { }
            }

            // Remove click listener after trigger
            document.removeEventListener('click', triggerPopunder);
        };
        document.addEventListener('click', triggerPopunder);
    }


})();