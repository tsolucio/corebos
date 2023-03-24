<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$APP.LBL_SHARE_LINK_IMAGE_SLIDER_TITLE}</title>
    <script src='include/js/imageSlider.js'></script>
    <style>
        html {
            height: 100%;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 0;
            margin: 0;
            background-color: #E6F7FF;
        }

        #mainSliderContainer {
            position: relative;
            width: 90%;
            max-width: 1250px;
            height: 90%;
        }
    </style>
    <script>
        window.onload = function() {
            const images = JSON.parse('{$images}');
            slider_initImageSlider('mainSliderContainer', images);
        };
    </script>
</head>
<body>
    <div id="mainSliderContainer"></div>
</body>
</html>