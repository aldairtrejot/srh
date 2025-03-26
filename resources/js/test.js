
import './bootstrap';
import { createApp } from 'vue';

const app_test = createApp({});

import test from './components/test.vue';
app_test.component('test-component', test);

