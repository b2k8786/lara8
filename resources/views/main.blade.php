<?php
$_SESSION['uID'] = strtotime('now');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>SSE</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>

<body>
    <button onclick="stopSync();">Stop sync</button>
    <script>
        var source = new EventSource('http://bitoku.com/live');
        source.addEventListener('open', function(e) {
            // Connection was opened.
        }, false);

        source.addEventListener('message', function(e) {

            try {
                data = JSON.parse(e.data);
                console.log(data);
                var br = document.createElement("br");
                data.forEach(row => {
                    document.body.prepend(br);
                    document.querySelector('body').prepend(row.name + " " + row.email);
                    document.body.prepend(br);
                });

            } catch (e) {
                document.querySelector('body').html(e);
                console.log('Error json decodeing ');
            }
            // jQuery('body').html(e.data);
        }, false);

        source.addEventListener('error', function(e) {
            if (e.readyState == EventSource.CLOSED) {
                // Connection was closed.
            }
        }, false);

        window.onbeforeunload = (e) => {
            source.close();
            e.returnValue = 'Sure?';
        }

        function stopSync() {
            source.close();
        }
    </script>
</body>

</html>