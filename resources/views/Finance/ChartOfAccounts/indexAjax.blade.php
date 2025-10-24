<!-- @php
    $counter = 1;
@endphp
@foreach($chartOfAccounts as $key => $dRow)
    @php
        $array = explode('-',$dRow->code);
		$level = count($array);
        $nature = $array[0];
        $rowColor = '';
        if($dRow->status != 1){
            $rowColor = 'danger';
        }

    @endphp
    <tr class="{{$rowColor}}">
        <td class="text-center">{{$counter++}}</td>
        <td>{{$dRow->code}}</td>
        <td>
            @if($level == 1)
                {{ $dRow->name}}
            @elseif($level == 2)
                &emsp;&emsp;{{$dRow->name}}
            @elseif($level == 3)
                &emsp;&emsp;&emsp;&emsp;{{$dRow->name}}
            @elseif($level == 4)
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$dRow->name}}
            @elseif($level == 5)
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$dRow->name}}
            @elseif($level == 6)
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$dRow->name}}
            @elseif($level == 7)
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; {{$dRow->name}}
            @endif
        </td>
        <td>
            @if(empty($dRow->parent))
                -
            @else
                {{$dRow->parent->name}}
            @endif
        </td>
        <td>
            @if($dRow->coa_type == 1)
                <span class="badge bg-success">Normal Chart of Account</span>
            @else
                <span class="badge bg-primary">Related Master Table</span>
            @endif
        </td>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action<span class="caret"></span></button>
                <ul class="dropdown-menu">
                    @if($dRow->coa_type == 1)
                        @if($dRow->status == 1)
                            <li><a href="{{ route('chartofaccounts.edit', $dRow->id) }}">Edit</a></li>
                            <li><a id="inactive-record" data-url="{{ route('chartofaccounts.status', $dRow->id) }}">Inactive</a></li>
                        @else
                            <li><a id="active-record" data-url="{{ route('chartofaccounts.activeStatus', $dRow->id) }}">Active</a></li>
                        @endif
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach -->
@php
    $counter = 1;
@endphp
@foreach($chartOfAccounts as $key => $dRow)
    @php
        $array = explode('-', $dRow->code);
        $level = count($array);
        $nature = $array[0];
        $rowColor = '';
        if($dRow->status != 1){
            $rowColor = 'danger';
        }
    @endphp
    <tr class="{{ $rowColor }}">
        <td class="text-center">{{ $counter++ }}</td>
        <td>{{ $dRow->code }}</td>
        <td>
            @if($level == 1)
                {{ $dRow->name }}
            @elseif($level == 2)
                &emsp;&emsp;{{ $dRow->name }}
            @elseif($level == 3)
                &emsp;&emsp;&emsp;&emsp;{{ $dRow->name }}
            @elseif($level == 4)
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{ $dRow->name }}
            @elseif($level == 5)
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{ $dRow->name }}
            @elseif($level == 6)
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{ $dRow->name }}
            @elseif($level == 7)
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{ $dRow->name }}
            @endif
        </td>
        <td>
            @if(empty($dRow->parent))
                -
            @else
                {{ $dRow->parent->name }}
            @endif
        </td>
        <td>
            @if($dRow->coa_type == 1)
                <span class="badge bg-success">Normal Chart of Account</span>
            @else
                <span class="badge bg-primary">Related Master Table</span>
            @endif
        </td>

        {{-- 🎨 ACTION ICONS --}}
        <td class="text-center">
            @if($dRow->coa_type == 1)
                {{-- ✏️ Edit icon --}}
                <a href="{{ route('chartofaccounts.edit', $dRow->id) }}" 
                   class="theme-icon text-primary me-2" 
                   title="Edit">
                    <i class="fas fa-edit"></i>
                </a>

                {{-- 🔴 Inactive or 🟢 Active toggle --}}
                @if($dRow->status == 1)
                    {{-- Record Active → show Inactive icon --}}
                    <a href="javascript:void(0)" 
                       class="theme-icon text-danger" 
                       id="inactive-record" 
                       data-url="{{ route('chartofaccounts.status', $dRow->id) }}" 
                       title="Mark as Inactive">
                        <i class="fas fa-toggle-on"></i>
                    </a>
                @else
                    {{-- Record Inactive → show Active icon --}}
                    <a href="javascript:void(0)" 
                       class="theme-icon text-success" 
                       id="active-record" 
                       data-url="{{ route('chartofaccounts.activeStatus', $dRow->id) }}" 
                       title="Mark as Active">
                        <i class="fas fa-toggle-off"></i>
                    </a>
                @endif
            @endif
        </td>
    </tr>
@endforeach

{{-- 💅 Icon Styling --}}
<style>
.theme-icon {
    font-size: 18px;
    margin: 0 4px;
    cursor: pointer;
}
.theme-icon:hover {
    opacity: 0.8;
    transform: scale(1.1);
}
</style>

{{-- ✅ Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

{{-- ✅ SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function showAlert(type, message) {
    Swal.fire({
        icon: type,
        text: message,
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

// 🔴 Mark Inactive
$(document).on('click', '#inactive-record', function(e) {
    e.preventDefault();
    let url = $(this).data('url');

    Swal.fire({
        title: "Are you sure?",
        text: "This account will be marked as inactive.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Inactivate"
    }).then((result) => {
        if (result.isConfirmed) {
            $.get(url, function(response) {
                showAlert('success', response.success || "Account marked as inactive!");
                setTimeout(() => location.reload(), 1500);
            }).fail(() => {
                showAlert('error', "Something went wrong!");
            });
        }
    });
});

// 🟢 Mark Active
$(document).on('click', '#active-record', function(e) {
    e.preventDefault();
    let url = $(this).data('url');

    Swal.fire({
        title: "Activate this account?",
        text: "This account will be reactivated.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Activate"
    }).then((result) => {
        if (result.isConfirmed) {
            $.get(url, function(response) {
                showAlert('success', response.success || "Account activated!");
                setTimeout(() => location.reload(), 1500);
            }).fail(() => {
                showAlert('error', "Something went wrong!");
            });
        }
    });
});
</script>

{{-- ✅ Laravel Session Sweet Alerts --}}
@if(session('success'))
<script>
    showAlert('success', "{{ session('success') }}");
</script>
@endif

@if(session('error'))
<script>
    showAlert('error', "{{ session('error') }}");
</script>
@endif
