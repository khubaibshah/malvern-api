@component('mail::message')
# New "Sell Your Car" Submission

**Name:** {{ $data->full_name }}  
**Email:** {{ $data->email }}  
**Phone:** {{ $data->phone ?? 'N/A' }}  
**Postcode:** {{ $data->postcode }}  
**Registration:** {{ $data->vehicle->registration ?? 'N/A' }}

---

## Vehicle Details
**Make:** {{ $data->vehicle->make ?? 'N/A' }}  
**Model:** {{ $data->vehicle->model ?? 'N/A' }}  
**Colour:** {{ $data->vehicle->primary_colour ?? 'N/A' }}  
**Fuel Type:** {{ $data->vehicle->fuel_type ?? 'N/A' }}  
**Engine Size:** {{ $data->vehicle->engine_size ?? 'N/A' }}  
**Mileage:** {{ $data->vehicle->odometer_value ?? 'N/A' }} {{ $data->vehicle->odometer_unit ?? '' }}

@endcomponent
