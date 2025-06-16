@component('mail::message')
# 🚗 New "Sell Your Car" Submission

---

## 📋 Customer Details

- **Full Name:** {{ $data->full_name }}
- **Email:** {{ $data->email }}
- **Phone:** {{ $data->phone ?? 'N/A' }}
- **Postcode:** {{ $data->postcode }}
- **Registration:** {{ $data->vehicle->registration ?? 'N/A' }}

---

## 🚘 Vehicle Information

- **Make:** {{ $data->vehicle->make ?? 'N/A' }}
- **Model:** {{ $data->vehicle->model ?? 'N/A' }}
- **Colour:** {{ $data->vehicle->primary_colour ?? 'N/A' }}
- **Fuel Type:** {{ $data->vehicle->fuel_type ?? 'N/A' }}
- **Engine Size:** {{ $data->vehicle->engine_size ?? 'N/A' }}
- **Mileage:** {{ $data->vehicle->odometer_value ?? 'N/A' }} {{ $data->vehicle->odometer_unit ?? '' }}
- **First Used:** {{ $data->vehicle->first_used_date ?? 'N/A' }}
- **Registration Date:** {{ $data->vehicle->registration_date ?? 'N/A' }}

---

Thanks,  
**{{ config('app.name') }} Sales Team**
@endcomponent
