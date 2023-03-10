const slider_css = `
    #slider_slideshow_container {
        display: flex;
        width: 100%;
        height: 100%;
    }

    #slider_Images {
        position: relative;
        width: 100%;
        overflow: hidden;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        border-radius: 15px;
    }

    .slider_arrow {
        display: flex;
        position: relative;
        z-index: 5;
        justify-content: center;
        align-items: center;
        padding: 10px;
        cursor: pointer;
        height: fit-content;
        background-color: white;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        border-radius: 150px;
        align-self: center;
        margin: 20px;
        opacity: 1;
    }

    #slider_ImagesContainer {
        display: flex;
        position: relative;
        width: 100%;
        height: 100%;
        background-color: white;
        transition: 0.5s;
        transform: translateX(0%);
    }

    .slider_image {
        flex: 0 0 auto;
        width: 100%;
        height: 100%;
        background-color: white;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }

    #slider_imageIndexContainer {
        display: flex;
        justify-content: center;
        position: absolute;
        width: 100%;
        bottom: 0;
    }

    #slider_indexContainer {
        padding: 15px 20px;
        margin: 15px;
        border-radius: 15px;
        background-color: rgba(0, 0, 0, 0.4);
        color: white;
        font-weight: bold;
    }
`;

function slider_handleLeftArrowEventListener(e) {
    const imagesContainer = e.target.closest('#slider_slideshow_container').querySelector('#slider_ImagesContainer');
    if (imagesContainer.dataset.index == 0) {
        return;
    }
    imagesContainer.dataset.index--; 
    imagesContainer.style.transform = `translateX(-${imagesContainer.dataset.index}00%)`;
    slider_afterImageUpdate();
}

function slider_handleRightArrowEventListener(e) {
    const imagesContainer = e.target.closest('#slider_slideshow_container').querySelector('#slider_ImagesContainer');
    if (imagesContainer.dataset.index >= imagesContainer.dataset.size - 1) {
        return;
    };
    imagesContainer.dataset.index++; 
    imagesContainer.style.transform = `translateX(-${imagesContainer.dataset.index}00%)`;
    slider_afterImageUpdate();
}

function slider_afterImageUpdate() {
    const imagesContainer = document.getElementById('slider_ImagesContainer');
    const leftArrow = document.getElementById('slider_leftArrow');
    const rightArrow = document.getElementById('slider_rightArrow');
    const indexContainer = document.getElementById('slider_indexContainer');
    // update image index
    indexContainer.innerHTML = (Number.parseInt(imagesContainer.dataset.index) + 1) + " / " + indexContainer.innerHTML.split(" / ")[1]
    // update arrow opacity
    if (imagesContainer.dataset.index == 0) {
        leftArrow.style.opacity = "0.5";
    } else {
        leftArrow.style.opacity = "1";
    }
    if (imagesContainer.dataset.index >= imagesContainer.dataset.size - 1) {
        rightArrow.style.opacity = "0.5";
    } else {
        rightArrow.style.opacity = "1";
    }
}

function slider_initImageSlider(elementId, imageArray) {
    // adding css to the document
    var style = document.createElement('style');
    style.innerHTML = slider_css;
    document.head.appendChild(style);
    // adding the images
    const el = document.getElementById(elementId);
    el.innerHTML = `<div id="slider_slideshow_container">
        <div id="slider_leftArrow" class="slider_arrow"><svg style="transform: translateX(-2.5px) scale(0.75);" xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"><path d="M655 976 255 576l400-400 56 57-343 343 343 343-56 57Z"/></svg></div>
        <div id="slider_Images">
            <div id="slider_ImagesContainer" data-index="0" data-size="${imageArray.length}">
                ${imageArray.map(image => `<div class="slider_image" style="background-image: url('${image}')"></div>`).join("")}
            </div>
            <div id="slider_imageIndexContainer">
                <div id="slider_indexContainer">1 / ${imageArray.length}</div>
            </div>
        </div>
        <div id="slider_rightArrow" class="slider_arrow"><svg style="transform: translateX(2.5px) scale(0.75);" xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"><path d="m304 974-56-57 343-343-343-343 56-57 400 400-400 400Z"/></svg></div>
    </div>`;
    // adding even listener for left and right arrows
    const leftArrow = document.getElementById('slider_leftArrow');
    const rightArrow = document.getElementById('slider_rightArrow');
    leftArrow.addEventListener('click', slider_handleLeftArrowEventListener);
    rightArrow.addEventListener('click', slider_handleRightArrowEventListener);
    // deciding the opacity for the first load
    slider_afterImageUpdate();
}