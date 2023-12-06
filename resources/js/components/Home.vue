<script setup lang="ts">
import { ref } from "vue";
import axios from "axios";

const name = ref('');
const email = ref('');
const phoneNumber = ref('');
const vehicleMakeModel = ref('');

const date = ref();
const formattedDate = ref('');

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

    } catch (error) {
        console.error("Error creating booking:", error);
    }
}
const formatDate = (date) => {
    if (date) {
        const options = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            // timeZone: 'UTC', // You can specify the timezone if needed
        };
        return new Intl.DateTimeFormat('en-US', options)
            .format(new Date(date))
            .replace(/\//g, '-')
            .replace(/,/, ''); // Remove the comma between date and time
    }
    return '';
};

</script>
<template>
    <div>
      <!-- <h2>Home</h2> -->
      <!-- <router-link to="/about">Go to About</router-link> -->
      <div class="surface-section px-4 py-8 md:px-6 lg:px-8">
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
                    <!-- <span>{{ date.toISOString().replace('Z', '').split('T')[0] }}</span> -->
                </div>
            
            
        </div>
    </div>
</div>
    </div>
  </template>

  