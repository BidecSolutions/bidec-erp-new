@php
    use App\Helpers\CommonHelper;
@endphp

@extends('layouts.layouts')

@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6">
                {{ CommonHelper::displayPageTitle('Edit Role Detail') }}
            </div>
            <div class="col-lg-6 text-right">
                <a href="{{ route('roles.index') }}" class="btn btn-success btn-xs">+ View List</a>
            </div>
        </div>

        <form method="POST" action="{{ route('roles.update', $role->id) }}">
            @csrf
            <input type="hidden" name="id" value="{{ $role->id }}" />

            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Role Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Permissions</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="checkPermissionAll">
                    <label class="form-check-label" for="checkPermissionAll">Select All</label>
                </div>
                <hr>

                @php $i = 1; @endphp
                @foreach ($permission_groups as $group)
                    @if ($loop->index % 3 == 0)
                        <div class="row">
                    @endif

                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="group-{{ $i }}" 
                                   onclick="checkPermissionByGroup('group-{{ $i }}', this)">
                            <label class="form-check-label fw-bold">{{ $group->menu_name }}</label>
                        </div>

                        <div class="ms-3 group-{{ $i }}" style="padding-left: 25px;">
                            @php
                                $permissions = App\Models\CustomPermission::getpermissionsByGroupName($group->group_id);
                            @endphp
                            @foreach ($permissions as $permission)
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" 
                                           name="permissions[]" 
                                           id="perm-{{ $permission->id }}" 
                                           value="{{ $permission->name }}"
                                           {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm-{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @php $i++; @endphp

                    @if ($loop->index % 3 == 2 || $loop->last)
                        </div>
                        <hr />
                    @endif
                @endforeach
            </div>

            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="reset" class="btn btn-secondary">Clear</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("checkPermissionAll").addEventListener("change", function() {
        document.querySelectorAll("input[name='permissions[]']").forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    document.querySelectorAll("input[name='permissions[]']").forEach(checkbox => {
        checkbox.addEventListener("change", function() {
            document.getElementById("checkPermissionAll").checked = 
                document.querySelectorAll("input[name='permissions[]']:checked").length === 
                document.querySelectorAll("input[name='permissions[]']").length;
        });
    });
</script>
@endsection
