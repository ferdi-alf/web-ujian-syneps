@extends('layouts.dashboard-layouts')

@section('content')
    <div class="px-4 py-6">
        <div class="">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Profile Settings</h2>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-fragments.text-field label="Name" name="name" placeholder="Masukkan name untuk login"
                            value="{{ old('name', auth()->user()->name) }}" required />
                    </div>

                    @if (in_array(auth()->user()->role, ['siswa', 'pengajar']))
                        <div class="mb-4">
                            <x-fragments.text-field label="Nama Lengkap" name="nama_lengkap"
                                placeholder="Masukkan nama lengkap"
                                value="{{ old('nama_lengkap', auth()->user()->nama_lengkap) }}" required />
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                            {{ ucfirst(auth()->user()->role) }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru (Kosongkan jika tidak ingin mengubah)
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Masukkan password baru">
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Konfirmasi password baru">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            @if (auth()->user()->role === 'admin')
                <div class="bg-white rounded-lg shadow-md p-6 mt-6 border-l-4 border-red-500">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <h3 class="text-xl font-bold text-red-600">Danger Zone - Reset Database</h3>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-red-700 text-sm font-medium">
                            <strong>⚠️ PERINGATAN:</strong> Aksi ini akan menghapus data secara permanen dan tidak dapat
                            dikembalikan!
                            Pastikan Anda telah melakukan backup data sebelum melanjutkan.
                        </p>
                    </div>

                    <form id="resetDataForm">
                        @csrf

                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="reset_users" name="reset_data[]" value="users"
                                    class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Reset Data Users (Pilih Role)</span>
                            </label>

                            <div id="user_roles" class="ml-6 mt-3 space-y-2" style="display: none;">
                                <p class="text-xs text-gray-500 mb-2 font-medium">Pilih role yang akan dihapus:</p>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="reset_data[]" value="pengajar"
                                        class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 user-role-checkbox"
                                        disabled>
                                    <span class="ml-2 text-sm text-gray-600">Reset Data Pengajar</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="reset_data[]" value="siswa"
                                        class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 user-role-checkbox"
                                        disabled>
                                    <span class="ml-2 text-sm text-gray-600">Reset Data Siswa</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="reset_data[]" value="ujian"
                                    class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Reset Data Ujian</span>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="reset_data[]" value="kelas"
                                    class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Reset Data Kelas</span>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="reset_data[]" value="hasil_ujian"
                                    class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Reset Data Hasil Ujian</span>
                            </label>
                        </div>

                        <div class="mt-6">
                            <button type="button" id="resetDataBtn"
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Reset Selected Data
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @if (auth()->user()->role === 'admin')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const resetUsersCheckbox = document.getElementById('reset_users');
                const userRolesDiv = document.getElementById('user_roles');
                const userRoleCheckboxes = document.querySelectorAll('.user-role-checkbox');
                const resetDataBtn = document.getElementById('resetDataBtn');
                const allCheckboxes = document.querySelectorAll('input[name="reset_data[]"]');
                const resetDataForm = document.getElementById('resetDataForm');

                resetUsersCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        userRolesDiv.style.display = 'block';
                        userRoleCheckboxes.forEach(checkbox => {
                            checkbox.disabled = false;
                        });
                        userRoleCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                    } else {
                        userRolesDiv.style.display = 'none';
                        userRoleCheckboxes.forEach(checkbox => {
                            checkbox.disabled = true;
                            checkbox.checked = false;
                        });
                    }
                    toggleResetButton();
                });

                userRoleCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            resetUsersCheckbox.checked = false;
                        }
                        toggleResetButton();
                    });
                });

                function toggleResetButton() {
                    const hasChecked = Array.from(allCheckboxes).some(checkbox => checkbox.checked);
                    resetDataBtn.disabled = !hasChecked;
                }

                allCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', toggleResetButton);
                });

                resetDataBtn.addEventListener('click', function() {
                    const selectedData = Array.from(allCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    if (selectedData.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Pilih minimal satu data untuk direset!'
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Konfirmasi Reset Data',
                        html: `
                <p><strong>Apakah Anda benar-benar yakin untuk menghapus data yang dipilih?</strong></p>
                <p class="text-red-600 mt-2">Data yang akan dihapus: <strong>${selectedData.join(', ')}</strong></p>
                <p class="text-sm text-gray-600 mt-2">⚠️ Harap backup data jika belum melakukannya!</p>
            `,
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Konfirmasi Password',
                                html: `
                        <p class="mb-4">Harap konfirmasi dengan password Anda:</p>
                        <input type="password" id="swal-password" class="swal2-input" placeholder="Masukkan password Anda">
                    `,
                                showCancelButton: true,
                                confirmButtonColor: '#dc2626',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Reset Data',
                                cancelButtonText: 'Batal',
                                preConfirm: () => {
                                    const password = document.getElementById(
                                        'swal-password').value;
                                    if (!password) {
                                        Swal.showValidationMessage('Password harus diisi!');
                                        return false;
                                    }
                                    return password;
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const password = result.value;

                                    // Submit form with AJAX
                                    const formData = new FormData(resetDataForm);
                                    formData.append('password_confirmation', password);

                                    fetch('{{ route('admin.reset.data') }}', {
                                            method: 'POST',
                                            body: formData,
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector(
                                                        'meta[name="csrf-token"]')
                                                    .getAttribute('content')
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Berhasil!',
                                                    text: data.message,
                                                    timer: 3000,
                                                    showConfirmButton: false
                                                }).then(() => {
                                                    resetDataForm.reset();
                                                    userRolesDiv.style.display =
                                                        'none';
                                                    userRoleCheckboxes.forEach(
                                                        checkbox => {
                                                            checkbox.disabled =
                                                                true;
                                                        });
                                                    toggleResetButton();
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Gagal!',
                                                    text: data.message ||
                                                        'Terjadi kesalahan saat reset data'
                                                });
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                text: 'Terjadi kesalahan pada server'
                                            });
                                        });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endif
@endsection
