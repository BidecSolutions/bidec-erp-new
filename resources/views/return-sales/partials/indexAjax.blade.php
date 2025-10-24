@foreach ($returnSales as $key => $row)
    <tr>
        <td class="text-center">{{ $key + 1 }}</td>
        <td>{{ $row->return_sale_no }}</td>
        <td>{{ $row->order_no }}</td>
        <td>{{ \Carbon\Carbon::parse($row->return_sale_date)->format('Y-m-d') }}</td>
        <td>{{ $row->customer_name }}</td>
        <td>{{ $row->reason }}</td>
        <td class="text-center">
            @if($row->return_sale_status != 2)
                <div class="hidden-print">
                    <label class="switch">
                        @php
                            $toggleUrl =
                                $row->status == 1
                                ? route('sales-return.destroy', $row->id)
                                : route('sales-return.status', $row->id);
                            $toggleId = $row->status == 1 ? 'inactive-record' : 'active-record';
                        @endphp
                        <input type="checkbox" id="{{ $toggleId }}" data-url="{{ $toggleUrl }}" data-id="{{ $row->id }}" {{ $row->status == 1 ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="d-none d-print-inline-block">
                    {{ $row->status == 1 ? 'Active' : 'In-Active' }}
                </div>
            @else
                {{ $row->status == 1 ? 'Active' : 'In-Active' }}
            @endif
        </td>
        <td class="text-center hidden-print">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" data-toggle="dropdown">Action <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    @if ($row->status == 1 && $row->return_sale_status != 2)
                        <li><a href="{{ route('sales-return.edit', $row->id) }}">Edit</a></li>
                    @endif
                    <li>
                        <a onclick="showDetailModelOneParamerter('sales-return/show', '{{ $row->id }}', 'View Return Sale Detail')">
                            <span class="glyphicon glyphicon-eye-open"></span> View
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
