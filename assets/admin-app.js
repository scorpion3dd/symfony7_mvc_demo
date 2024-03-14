import './bootstrap.js';
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import 'bootstrap';
import bsCustomFileInput from 'bs-custom-file-input';
import zoomPlugin from 'chartjs-plugin-zoom';

// start the Stimulus application
import './bootstrap';

console.log('admin-app.js - start');

document.addEventListener('chartjs:init', function (event) {
    console.log('EventListener chartjs:init - added');
    console.log(event);

    const Chart = event.detail.Chart;
    Chart.register(zoomPlugin);
    console.log(Chart);
});

bsCustomFileInput.init();
