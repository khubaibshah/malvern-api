@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <!-- <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo"> -->
            <img src="https://scs.netlify.app/assets/newlogo-DR-U47z6.png" 
            alt="SCS Car Sales Ltd" 
            class="logo" 
            style="height: 122px;
                max-height: 137px;
                width: 118px;"
            >

            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>