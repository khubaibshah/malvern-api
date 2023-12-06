import { createApp } from 'vue';
import './style.css';
import router from './router';
import App from './components/App.vue';
import "primeflex/primeflex.css";
import 'primeicons/primeicons.css'

import StyleClass from 'primevue/styleclass';
       
import Ripple from 'primevue/ripple';

import Button from "primevue/button"
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';



import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';                   // optional


import PrimeVue from 'primevue/config';
import 'primevue/resources/themes/lara-light-green/theme.css'

const app = createApp(App);

app.config.globalProperties.$filters = {
    formatDateTime: (value) => {
        const options = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        };
        return new Intl.DateTimeFormat('en-GB', options).format(new Date(value));
    },

}
app.use(PrimeVue, { ripple: true, style: true });
app.use(router);


app.directive('styleclass', StyleClass);
app.directive('ripple', Ripple);



app.component('InputText', InputText);
app.component('Button', Button);
app.component('DataTable', DataTable);
app.component('Column', Column);
app.component('Textarea', Textarea);
app.component('Calendar', Calendar);


app.mount('#app');
