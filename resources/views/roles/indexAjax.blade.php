@foreach ($roles as $index => $role)
    <tr>
        <td>{{ $index + 1 }}</td> <!-- Add 1 to $index since index starts from 0 -->
        <td>{{ $role->name }}</td>
        <td>
            @if ($role->permissions->isNotEmpty())
                @php $counter = 0; @endphp
                @foreach ($role->permissions as $permission)
                    <span class="badge badge-primary mb-1 mr-1">{{ $permission->name }}</span>
                    @php $counter++; @endphp

                    @if ($counter % 6 == 0)
                        <br />
                    @endif
                @endforeach
            @else
                <span class="text-muted">No Permissions</span>
            @endif
        </td>
        <td class="text-center hidden-print">
            <div class="dropdown">
                <button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action <span
                        class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('roles.edit', ['id' => $role->id]) }}">Edit</a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach