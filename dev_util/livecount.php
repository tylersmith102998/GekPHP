<!doctype html>

<html>
    <head>
        <title>ApexFramework3 Live Line Count</title>

        <link rel="stylesheet" type="text/css" href="css/af3livecount.css" />

        <meta name="viewport" content="initial-scale=1, width=device-width, user-scalable=0" />
    </head>
    <body>

        <div id="central">
            <h1 id="title">GekPHP - Live Line Count</h1>
            <p class="small-text"><strong>Total Lines:</strong></p>
            <p id="total-count">&#216;</p>

            <p class="small-text">Significant Lines:</p>
            <p id="line-count">&#216;</p>

            <p class="small-text">Commented Lines:</p>
            <p id="comments-count">&#216;</p>

            <p class="small-text">Whitespace Lines:</p>
            <p id="whitespace-count">&#216;</p>


        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="js/af3livecount.js"></script>

    </body>
</html>
