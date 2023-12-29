<script>

    $(function () {
        $('.select2').select2();
    });

    /*document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            // Page is being hidden, perform actions here
            $.ajax({
                url: '/logout', // Replace with your logout route
                method: 'POST',
                async: false, // Make it synchronous if needed
            });
            // Clear sessionStorage
            sessionStorage.clear();
            // Clear localStorage
            localStorage.clear();
        }
    });*/

    /*window.addEventListener('beforeunload', function (event) {
        // Asynchronous AJAX request
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/logout-on-close', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send();

        // Clear sessionStorage
        sessionStorage.clear();
        // Clear localStorage
        localStorage.clear();

    });*/

</script>
<script src="{{ asset('js/common.js') }}"></script>
</body>
</html>
