<script setup lang="ts">
import Customers from '../customers/Customer.vue'
// import { ref, onMounted } from 'vue';
// import axios from 'axios';

// const bookings = ref([]);
// const getAllUsers = async () => {
//     try {
//     const response = await axios.get('/api/bookings'); // Adjust the API endpoint as needed
//     bookings.value = response.data;
//   } catch (error) {
//     console.error('Error fetching bookings:', error);
//   }
// }
// onMounted(async () => {
//     getAllUsers();
// });

</script>

<template>

<div class="min-h-screen flex flex-column surface-ground">
    <div class="bg-gray-900 py-3 px-6 shadow-2 flex align-items-center justify-content-between relative lg:static border-bottom-1 border-gray-800" style="min-height: 84px">
        <!-- <img src="images/blocks/logos/bastion-300.svg" alt="Image" height="40" class="mr-0 lg:mr-6"> -->
        <a v-ripple class="cursor-pointer block lg:hidden text-gray-400 p-ripple" v-styleclass="{ selector: '@next', enterClass: 'hidden', leaveToClass: 'hidden', hideOnOutsideClick: true }">
            <i class="pi pi-bars text-4xl"></i>
        </a>
        <div class="align-items-center flex-grow-1 justify-content-between hidden lg:flex absolute lg:static w-full bg-gray-900 left-0 top-100 z-1 shadow-2 lg:shadow-none border-1 lg:border-none border-gray-800">
            <ul class="list-none p-0 m-0 flex lg:align-items-center select-none flex-column lg:flex-row">
                <li>
                    <a v-ripple class="flex px-6 p-3 lg:px-3 lg:py-2 align-items-center text-gray-400 hover:text-white hover:bg-gray-800 font-medium border-round cursor-pointer transition-colors transition-duration-150 p-ripple">
                        <i class="pi pi-home mr-2"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a v-ripple class="flex px-6 p-3 lg:px-3 lg:py-2 align-items-center text-gray-400 hover:text-white hover:bg-gray-800 font-medium border-round cursor-pointer transition-colors transition-duration-150 p-ripple"
                        v-styleclass="{ selector: '@next', enterClass: 'hidden', enterActiveClass: 'scalein', leaveToClass: 'hidden', leaveActiveClass: 'fadeout', hideOnOutsideClick: true }">
                        <i class="pi pi-users mr-2"></i>
                        <span>Customers</span>
                        <i class="pi pi-angle-down ml-auto lg:ml-3"></i>
                    </a>
                    <ul class="list-none py-3 px-6 m-0 lg:px-0 lg:py-0 border-round shadow-0 lg:shadow-2 lg:absolute bg-gray-900 hidden origin-top w-full lg:w-15rem cursor-pointer lg:border-1 border-gray-800">
                        <li>
                            <a v-ripple class="flex p-3 align-items-center text-gray-400 hover:text-white hover:bg-gray-800 transition-colors transition-duration-150 p-ripple">
                                <i class="pi pi-user-plus mr-2"></i>
                                <span class="font-medium">Add New</span>
                            </a>
                        </li>
                        <li class="relative">
                            <a v-ripple class="flex p-3 align-items-center text-gray-400 hover:text-white hover:bg-gray-800 transition-colors transition-duration-150 p-ripple"
                                v-styleclass="{ selector: '@next', enterClass: 'hidden', enterActiveClass: 'scalein', leaveToClass: 'hidden', leaveActiveClass: 'fadeout', hideOnOutsideClick: true }">
                                <i class="pi pi-search mr-2"></i>
                                <span class="font-medium">Search</span>
                                <i class="pi pi-angle-down ml-auto lg:-rotate-90"></i>
                            </a>
                            <ul class="list-none py-3 pl-3 m-0 lg:px-0 lg:py-0 border-round shadow-0 lg:shadow-2 lg:absolute bg-gray-900 hidden origin-top w-full lg:w-15rem cursor-pointer left-100 top-0 lg:border-1 border-gray-800">
                                <li>
                                    <a v-ripple class="flex p-3 align-items-center text-gray-400 hover:text-white hover:bg-gray-800 transition-colors transition-duration-150 p-ripple">
                                        <i class="pi pi-shopping-cart mr-2"></i>
                                        <span class="font-medium">Purchases</span>
                                    </a>
                                </li>
                                <li class="relative">
                                    <a v-ripple class="flex p-3 align-items-center text-gray-400 hover:text-white hover:bg-gray-800 transition-colors transition-duration-150 p-ripple">
                                        <i class="pi pi-comments mr-2"></i>
                                        <span class="font-medium">Messages</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <a v-ripple class="flex px-6 p-3 lg:px-3 lg:py-2 align-items-center text-gray-400 hover:text-white hover:bg-gray-800 font-medium border-round cursor-pointer transition-colors transition-duration-150 p-ripple">
                        <i class="pi pi-calendar mr-2"></i>
                        <span>Calendar</span>
                    </a>
                </li>
                <li>
                    <a v-ripple class="flex px-6 p-3 lg:px-3 lg:py-2 align-items-center text-gray-400 hover:text-white hover:bg-gray-800 font-medium border-round cursor-pointer transition-colors transition-duration-150 p-ripple">
                        <i class="pi pi-chart-line mr-2"></i>
                        <span>Stats</span>
                    </a>
                </li>
            </ul>
            <ul class="list-none p-0 m-0 flex lg:align-items-center select-none flex-column lg:flex-row">
                <li class="flex-order-2 lg:flex-order-0">
                    <div class="flex px-6 p-3 lg:px-3 lg:py-2">
                        <span class="p-input-icon-left w-full">
                            <i class="pi pi-search text-gray-400"></i>
                            <InputText type="text" placeholder="Search" class="bg-gray-800 text-gray-400 border-gray-800 w-full" />
                        </span>
                    </div>
                </li>
                <li class="border-top-1 border-gray-800 lg:border-top-none">
                    <a v-ripple class="flex px-6 p-3 lg:px-3 lg:py-2 align-items-center hover:bg-gray-800 font-medium border-round cursor-pointer transition-colors transition-duration-150 p-ripple">
                        <!-- <img src="images/blocks/avatars/circle/avatar-f-1.png" class="mr-3 lg:mr-0" style="width: 32px; height: 32px"/> -->
                        <div class="block lg:hidden">
                            <div class="text-white font-medium">Josephine Lillard</div>
                            <span class="text-gray-400 font-medium text-sm">Marketing Specialist</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <ul class="list-none py-3 px-5 m-0 surface-section flex align-items-center font-medium overflow-x-auto border-top-1 border-bottom-1 surface-border">
        <li class="pr-3">
            <a class="cursor-pointer">
                <i class="pi pi-home text-blue-500"></i>
            </a>
        </li>
        <li class="px-2">
            <i class="pi pi-angle-right text-500"></i>
        </li>
        <li class="px-2">
            <a class="cursor-pointer text-blue-500 white-space-nowrap">Level 3</a>
        </li>
        <li class="px-2">
            <i class="pi pi-angle-right text-500"></i>
        </li>
        <li class="px-2">
            <a class="cursor-pointer text-blue-500 white-space-nowrap">Level 2</a>
        </li>
        <li class="px-2">
            <i class="pi pi-angle-right text-500"></i>
        </li>
        <li class="px-2">
            <a class="cursor-pointer text-900 white-space-nowrap">Level 1</a>
        </li>
    </ul>
    <div class="p-5 flex flex-column flex-auto">
        <div class="border-2  surface-border border-round surface-section flex-auto">
            <!-- router will go here -->
            <!-- {{ bookings }} -->
            
            <Customers />
        </div>
    </div>
</div>
</template>
