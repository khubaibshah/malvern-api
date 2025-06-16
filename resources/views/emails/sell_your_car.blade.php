@component('mail::message')
# New "Sell Your Car" Submission

**Name:** {{ $data['fullName'] }}  
**Email:** {{ $data['email'] }}  
**Phone:** {{ $data['phone'] ?? 'N/A' }}  
**Postcode:** {{ $data['postcode'] }}  
**Registration:** {{ $data['registration'] }}

---

## Vehicle Details
**Make:** {{ $data['vehicle']['make'] ?? 'N/A' }}  
**Model:** {{ $data['vehicle']['model'] ?? 'N/A' }}  
**Colour:** {{ $data['vehicle']['primaryColour'] ?? 'N/A' }}  
**Fuel Type:** {{ $data['vehicle']['fuelType'] ?? 'N/A' }}  
**Engine Size:** {{ $data['vehicle']['engineSize'] ?? 'N/A' }}  
**Mileage:** {{ $data['vehicle']['odometerValue'] ?? 'N/A' }} {{ $data['vehicle']['odometerUnit'] ?? '' }}

@endcomponent
