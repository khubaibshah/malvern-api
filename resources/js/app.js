import { createApp } from 'vue';
import './style.css';
import App from './components/App.vue';
import "primeflex/primeflex.css";
import 'primeicons/primeicons.css'

import StyleClass from 'primevue/styleclass';
       
import Ripple from 'primevue/ripple';

import Button from "primevue/button"
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';                   // optional


import PrimeVue from 'primevue/config';
import 'primevue/resources/themes/lara-light-green/theme.css'

const app = createApp(App);


app.use(PrimeVue, { ripple: true, style: true });

app.directive('styleclass', StyleClass);
app.directive('ripple', Ripple);



app.component('InputText', InputText);
app.component('Button', Button);
app.component('DataTable', DataTable);
app.component('Column', Column);


app.mount('#app');
