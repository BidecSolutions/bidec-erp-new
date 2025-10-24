@php
    use App\Helpers\CommonHelper;
@endphp
@foreach($products as $dRow)
<tr>
    <td class="text-center">{{ $loop->index + 1 }}</td>
    <td>{{ $dRow['category_name'] }}</td>
    <td>{{ $dRow['brand_name'] }}</td>
    <td>{{ $dRow['name'] }}</td>
    <td>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Size Name</th>
                    <th>Variant Image</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dRow['variants'] as $variant)
                    <tr>
                        <td>{{$variant['size_name'] ?? '-'}}</td>
                        <td>{{CommonHelper::display_document_two($variant['variant_image'] ?? 'assets/img/no_image.png')}}</td>
                        <td class="text-right">{{ number_format($variant['amount'] ?? '0',0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </td>
    <td class="text-center">
        <div class="hidden-print">
            <label class="switch">
                @php
                    $toggleUrl =
                    $dRow['status'] == 1
                            ? route('products.destroy', $dRow['id'])
                            : route('products.status', $dRow['id']);
                    $toggleId = $dRow['status'] == 1 ? 'inactive-record' : 'active-record';
                @endphp
                <input type="checkbox" id="{{ $toggleId }}" data-url="{{ $toggleUrl }}"
                    data-id="{{ $dRow['id'] }}" {{ $dRow['status'] == 1 ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </div>
        <div class="d-none d-print-inline-block">
            @if ($dRow['status'] == 1)
                Active
            @else
                In-Active
            @endif
        </div>
    </td>
    <td class="text-center hidden-print">
        <div class="dropdown">
            <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button>
            <ul class="dropdown-menu">
                @if($dRow['status'] == 1)
                    <!-- <li><a href="{{ route('products.edit', $dRow['id']) }}">Edit</a></li> -->
                @endif
            </ul>
        </div>
    </td>
</tr>
@endforeach
