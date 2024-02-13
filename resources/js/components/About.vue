<!-- resources/js/components/About.vue -->
<template>
    <div>
        <div class="surface-section md:px-7 lg:px-8">
   <h2>About</h2>
      <router-link to="/">Go to Home</router-link>
      
    <!-- {{ bookings }} -->

    <DataTable :value="bookings" tableStyle="min-width: 50rem">
        <Column field="id" header="User Id"></Column>
        <Column field="name" header="Name"></Column>
        <Column field="email" header="Email"></Column>
        <Column field="phone_number" header="Phone"></Column>
        <Column field="vehicle_make_model" header="Vehicle"></Column>
        <Column field="booking_datetime" header="Booking Time"></Column>
        <Column field="created_at" header="Booking created"></Column>
        <Column field="updated_at" header="Booking updated"></Column>
    </DataTable>

        </div>
   
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted } from "vue";
  import axios from "axios";
  
  const bookings = ref();
  
  const getAllUsers = async () => {
      try {
          const response = await axios.get("/api/bookings");
          bookings.value = response.data;
      } catch (error) {
          console.error("Error fetching bookings:", error);
      }
  };
  
  onMounted(async () => {
      getAllUsers();
  });
  </script>
  