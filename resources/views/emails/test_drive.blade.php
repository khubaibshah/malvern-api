@component('mail::message')
# New Test Drive Request

**Name:** {{ $lead->name }}  
**Email:** {{ $lead->email }}  
@if($lead->phone)
**Phone:** {{ $lead->phone }}  
@endif

**Vehicle:** {{ $lead->vehicle->make ?? 'N/A' }} {{ $lead->vehicle->model ?? '' }}  
**Vehicle Reg:** {{ $lead->vehicle->registration }}

---

Thanks,<br>
Stanley Car Sales
@endcomponent
