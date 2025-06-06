@component('mail::message')
# 🚗 New Vehicle Lead

You’ve received a new Lead from your website:

---

**Name:** {{ $lead->name }}  
**Email:** {{ $lead->email }}  
**Phone:** {{ $lead->phone ?? 'N/A' }}  

**Message:**  
> {{ $lead->message }}

---

@if($lead->vehicle)
## Vehicle Details

@isset($lead->vehicle->main_image)
<img src="{{ $lead->vehicle->main_image }}" alt="Vehicle image" style="width: 100%; max-width: 400px; border-radius: 8px; margin-bottom: 10px;">
@endisset

- **Make:** {{ $lead->vehicle->make }}  
- **Model:** {{ $lead->vehicle->model }}  
- **Variant:** {{ $lead->vehicle->variant ?? 'N/A' }}  
- **Reg:** {{ $lead->vehicle->registration }}  
- **Year:** {{ $lead->vehicle->year ?? 'N/A' }}  
- **Price:** £{{ number_format($lead->vehicle->price, 2) }}

@endif

@component('mail::button', ['url' => url('/')])
Go to Website
@endcomponent

Thanks,  
**SCS Car Sales Ltd**
@endcomponent
