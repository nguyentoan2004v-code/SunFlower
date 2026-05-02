@extends('layouts.admin')

@section('title', 'Phân quyền Nhân viên')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Cấp quyền cho nhân viên: <span class="text-danger">{{ $nhanvien->hoten }}</span> ({{ $nhanvien->manv }})
                    </h6>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.nhanvien.updateRoles', $nhanvien->manv) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">Chọn các vai trò cho nhân viên này:</label>
                            
                            <div class="row mt-2">
                                @foreach($vaitros as $role)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check custom-checkbox">
                                            <input 
                                                class="form-check-input" 
                                                type="checkbox" 
                                                name="vaitros[]" 
                                                value="{{ $role->mavt }}" 
                                                id="role_{{ $role->mavt }}"
                                                {{-- Kiểm tra xem vai trò này nhân viên đã có chưa để checked --}}
                                                {{ $nhanvien->vaitros->contains('mavt', $role->mavt) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="role_{{ $role->mavt }}">
                                                <strong>{{ $role->tenvt }}</strong>
                                                @if($role->mota)
                                                    <br><small class="text-muted">{{ $role->mota }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @error('vaitros')
                                <div class="text-danger mt-2"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.nhanvien.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật quyền
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection