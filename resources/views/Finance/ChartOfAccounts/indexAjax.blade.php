@php
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
@endforeach