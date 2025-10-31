<div>
    <!-- Company Details -->
    <h3>Company Details</h3>
    <div>
        <p><strong>Company Name:</strong> {{ $locations->first()->company_name ?? 'N/A' }}</p>
        <p><strong>Registration No:</strong> {{ $locations->first()->registration_no ?? 'N/A' }}</p>
        <img src="{{ asset($locations->first()->company_logo ?? '') }}" alt="Company Logo" width="100">
    </div>

    <!-- Campus Details -->
    <h3>Locations Details</h3>
    <ul class="ban-list">
        @forelse ($locations as $location)
            <li>
                <div class="banq-box">
                    <a href="javascript:void(0);"
                        onclick="setCampusAndRedirect('{{ $location->company_id }}', '{{ $location->id }}', '{{ $location->company_name }}', '{{ $location->company_code }}')">
                        <h3 class="item-model-company theme-f-m">
                            {{ $location->name }}
                        </h3>
                        <h3 class="item-model-company theme-f-m">
                            {{ $location->phone_no }}
                        </h3>
                        <h3 class="item-model-company theme-f-m">
                            {{ $location->address }}
                        </h3>
                        <!-- <strong>Location Name:</strong><br> {{ $campus->name }}<br>
                            <strong>Phone No:</strong><br> {{ $campus->phone_no }}<br>
                            <strong>Email:</strong><br> {{ $campus->email }}<br>
                            <strong>Address:</strong><br> {{ $campus->address }}<br> -->
                    </a>
                </div>

            </li>
        @empty
            <li>
                <div class="banq-box">No locations available
                </div>
            </li>
        @endforelse
    </ul>
</div>
