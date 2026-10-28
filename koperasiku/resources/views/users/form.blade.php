<div class="row">
    <div class="col-md-6 form-group">
        <label>Nama</label>
        <input type="text" name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 form-group">
        <label>Email</label>
        <input type="email" name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 form-group">
        <label>Password @isset($user) <small class="text-muted">(kosongkan jika tidak diubah)</small> @endisset</label>
        <input type="password" name="password"
            class="form-control @error('password') is-invalid @enderror"
            autocomplete="new-password"
            @unless(isset($user)) required @endunless>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 form-group">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation"
            class="form-control" autocomplete="new-password"
            @unless(isset($user)) required @endunless>
    </div>

    <div class="col-md-6 form-group">
        <label>Role</label>
        <select name="role"
            class="form-control @error('role') is-invalid @enderror" required>
            @foreach(\App\Models\User::ROLES as $role)
                <option value="{{ $role }}"
                    @selected(old('role', $user->role ?? 'anggota') === $role)>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 form-group">
        <label class="d-block">Status Akun</label>
        <div class="custom-control custom-switch mt-2">
            <input type="checkbox" class="custom-control-input" id="is_active"
                name="is_active" value="1"
                @checked(old() ? old('is_active') : ($user->is_active ?? true))>
            <label class="custom-control-label" for="is_active">Aktif</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan
    </button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
</div>
