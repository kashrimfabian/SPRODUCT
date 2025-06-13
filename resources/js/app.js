import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

import './bootstrap'; // Your existing bootstrap and axios setup
import Swal from 'sweetalert2';
window.Swal = Swal;

// You might not need these directly in your main app.js if you're moving to Vue components
// import 'jquery';
// import 'bootstrap-datepicker';
// import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';
// import './datepicker-init';

const app = createApp(App);
app.use(router);
app.mount('#app');