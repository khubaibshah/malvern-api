<script setup lang="ts">
import { ref } from 'vue';

const msg = ref('');
import navbar from './nav/navbar.vue'
</script>

<template>
    <!-- <h1>
        How To Install Vue 3 in Laravel 10 : Laravel SPA :)
        <h1>Playground</h1>
    <InputText v-model="msg" />
    <p>{{ msg }}</p>
    <Button label="Check" icon="pi pi-users" />
</h1> -->

<navbar />
</template>
