import './bootstrap';
import Alpine from 'alpinejs';

import '@fontsource/inter';
import '@fontsource/playfair-display';
import '@fontsource/lato';
import '@fontsource/plus-jakarta-sans';
// Import specific weights if needed, or default imports usually load 400. 
// For better coverage:
import '@fontsource/inter/500.css';
import '@fontsource/inter/600.css';
import '@fontsource/inter/700.css';
import '@fontsource/playfair-display/700.css';
import '@fontsource/plus-jakarta-sans/500.css';
import '@fontsource/plus-jakarta-sans/600.css';
import '@fontsource/plus-jakarta-sans/700.css';

import Flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';

window.Alpine = Alpine;
window.flatpickr = Flatpickr;
Alpine.start();
