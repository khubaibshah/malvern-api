<script setup lang="ts">
import { ref } from "vue";
import axios from "axios";

const name = ref('');
const email = ref('');
const phoneNumber = ref('');
const vehicleMakeModel = ref('');

const date = ref();
const formattedDate = ref('');
const successMessage = ref('');
const errorMessage = ref('');

const createBooking = async () => {
    try {
        const userBooking = {
            name: name.value,
            email: email.value,
            phone_number: phoneNumber.value,
            vehicle_make_model: vehicleMakeModel.value,
            booking_datetime: formatDate(date.value), // Format the date before sending
        };

        const response = await axios.post("/api/bookings", userBooking);
        console.log('Booking response:', response.data);

        // Optionally, you can reset the form fields after successful submission
        name.value = '';
        email.value = '';
        phoneNumber.value = '';
        vehicleMakeModel.value = '';
        date.value = null;

        // Set success message
        successMessage.value = "Booking created successfully";
        // Clear any previous error message
        errorMessage.value = '';

    } catch (error) {
        console.error("Error creating booking:", error);

        // Set error message
        errorMessage.value = "Booking couldn't be created. Please try again.";
        // Clear any previous success message
        successMessage.value = '';
    }
}

const formatDate = (date) => {
    if (date) {
        const formattedDate = new Date(date);
        const year = formattedDate.getFullYear();
        const month = String(formattedDate.getMonth() + 1).padStart(2, '0');
        const day = String(formattedDate.getDate()).padStart(2, '0');
        const hours = String(formattedDate.getHours()).padStart(2, '0');
        const minutes = String(formattedDate.getMinutes()).padStart(2, '0');
        const seconds = String(formattedDate.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }
    return '';
};
</script>
<template>
    <div>
        
        <div class="surface-section px-4 py-5 md:px-6 lg:px-8 mt-5">
            <div v-if="successMessage" class="mb-5">
      <Message severity="success">{{ successMessage }}</Message>
    </div>

    <!-- Error Message -->
    <div v-if="errorMessage" class="mb-5">
      <Message severity="error">{{ errorMessage }}</Message>
    </div>
    <div class="flex md:align-items-center md:justify-content-between flex-column md:flex-row pb-4 border-bottom-1 surface-border">
        
        <div class="mb-3 lg:mb-0">
            
            <div class="text-3xl font-medium text-900 mb-3">Create a booking</div>
            <div class="text-500 mr-0 md:mr-3">The garage is open between
                9am and 5:30pm, Monday to Friday. Only 1 booking is allowed per slot.</div>
        </div>
        <span class="p-input-icon-left w-full md:w-auto">
            <i class="pi pi-search"></i>
            <InputText type="text" placeholder="Search" class="w-full md:w-auto"/>
        </span>
    </div>
</div>
      <!-- <h2>Home</h2> -->
      <!-- <router-link to="/about">Go to About</router-link> -->
      <div class="surface-section md:px-7 lg:px-8">
    <div class="grid">
        <div class="col-12 md:col-6">
            <div class="p-fluid pr-0 md:pr-6">
                <div class="field">
            <label for="name" class="font-medium">Full name</label>
            <InputText v-model="name" id="name" type="text" class="py-3 px-2 text-lg" />
        </div>
        <div class="field">
            <label for="email" class="font-medium">Email</label>
            <InputText v-model="email" id="email" type="text" class="py-3 px-2 text-lg" />
        </div>
        <div class="field">
            <label for="company" class="font-medium">Phone number</label>
            <InputText v-model="phoneNumber" id="company" type="text" class="py-3 px-2 text-lg" />
        </div>
        <div class="field">
            <label for="message" class="font-medium">Vehicle make & model</label>
            <Textarea v-model="vehicleMakeModel" id="message" :rows="6" :autoResize="true" class="py-3 px-2 text-lg"></Textarea>
        </div>

        <Button label="Send Message" icon="pi pi-send" class="w-auto" @click="createBooking"></Button>
            </div>
        </div>
        <div class="col-12 md:col-6 bg-no-repeat bg-right-bottom" style="background-image: url('../../assets/images/contact-1.png')">
            <div class="text-900 text-2xl font-medium mb-3">Please pick a date</div>
            <Calendar v-model="date" inline showTime hourFormat="24" />
                <div>
                    <span>Formatted Date: </span>
                    <span>{{ formatDate(date) }}</span>
                    
                </div>
            
            
        </div>
    </div>
</div>
    </div>
  </template>

  