<?php
$_SESSION['uID'] = strtotime('now');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>SSE</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
    <div id="actions">
        <button onclick="stopSync();">Stop sync</button>
    </div>
    <div id="data">
    </div>
    <script>
        var source = new EventSource('<?= url('/') ?>/live');

        source.addEventListener('open', function(e) {
            jQuery('#data').html("");
            console.log(e);
        }, false);

        source.addEventListener('message', function(e) {

            try {
                data = JSON.parse(e.data);
                console.log(data);

                data.forEach(row => {
                    jQuery('#data').prepend("<br>" + row.username + " " + row.email);
                });

            } catch (e) {
                console.log('Error json decodeing ');
                console.log(e);
            }
        }, false);

        source.addEventListener('error', function(e) {
            console.log(e);
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