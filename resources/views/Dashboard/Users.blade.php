@extends('layouts.dashboard-layouts')

@section('content')
    <div>
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="modal-control-users" variant="emerald" act="create">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah User
            </x-fragments.modal-button>
        </div>

        <x-fragments.form-modal id="modal-control-users" title="Tambah User" createTitle="Tambah Materi" editTitle="Edit Materi"
            action="{{ route('users.store') }}">
            <div class="grid grid-cols-2 gap-4">
                <x-fragments.text-field label="name" name="name" placeholder="Masukkan name untuk login" required />
                <x-fragments.select-field label="Role" name="role" :options="['admin' => 'Admin', 'pengajar' => 'Pengajar']" required />
            </div>

            <x-fragments.text-field label="Email" name="email" type="email" required class="mt-4" />
            <div id="kelas-field" class="hidden mt-4">
                <x-fragments.multiple-select-badges label="Kelas" name="kelas_id" :options="$kelas
                    ->map(fn($k) => ['value' => (string) $k->id, 'label' => $k->nama])
                    ->values()
                    ->toArray()"
                    placeholder="Pilih kelas untuk pengajar" required />
            </div>
            <x-fragments.text-field label="Password" name="password" type="password" required />
        </x-fragments.form-modal>

        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Data Admin</h2>
            <x-reusable-table :headers="['No', 'Avatar', 'Name', 'Email']" :data="$admins" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->getAvatarHtml(),
                fn($row) => $row->name,
                fn($row) => $row->email,
            ]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalTarget' => 'modal-control-users',
                'editData' => [
                    'id' => $row->id,
                    'fetchEndpoint' => '/users/' . $row->id,
                    'updateEndpoint' => '/users/' . $row->id,
                    'act' => 'update',
                ],
                'deleteRoute' => route('users.destroy', $row->id),
            ])" />
        </div>

        <div class="mt-10">
            <h2 class="text-lg font-semibold mb-2">Data Pengajar</h2>
            <x-reusable-table :headers="['No', 'Avatar', 'Name', 'Email', 'Nama Lengkap', 'Kelas']" :data="$pengajars" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->getAvatarHtml(),
                fn($row) => $row->name,
                fn($row) => $row->email,
                fn($row) => $row->pengajarDetail->nama_lengkap ?? '-',
                fn($row) => $row->pengajarDetail && $row->pengajarDetail->kelas
                    ? $row->pengajarDetail->kelas->pluck('nama')->join(', ')
                    : '-',
            ]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalTarget' => 'modal-control-users',
                'deleteRoute' => route('users.destroy', $row->id),
                'editData' => [
                    'id' => $row->id,
                    'fetchEndpoint' => '/users/' . $row->id,
                    'updateEndpoint' => '/users/' . $row->id,
                    'act' => 'update',
                ],
            ])" />
        </div>
    </div>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log(@json($admins->merge($pengajars)));
            const roleSelect = document.querySelector('select[name="role"]');
            const kelasField = document.getElementById("kelas-field");
            const name = document.querySelector('input[name="name"]');
            const role = document.querySelector('select[name="role"]');
            const email = document.querySelector('input[name="email"]');
            const password = document.querySelector('input[name="password"]');

            document.addEventListener('modalCreate', function(e) {
                if (e.detail.modalId === 'modal-control-users') {
                    showKelasField();
                    password.required = true;
                    resetMultipleSelect();
                }
            });

            document.addEventListener('modalUpdate', function(e) {
                if (e.detail.modalId === 'modal-control-users') {
                    showKelasField();
                    currentModa = 'update';

                    const userData = e.detail.data;
                    console.log('User Data:', userData);

                    name.value = userData.name || '';
                    role.value = userData.role || '';
                    email.value = userData.email || '';
                    password.required = false;

                    if (userData.role === 'pengajar' && userData.pengajar_detail) {
                        kelasField.classList.remove("hidden");

                        setMultipleSelectValues(userData.pengajar_detail.kelas);
                    } else {
                        kelasField.classList.add("hidden");
                        resetMultipleSelect();
                    }
                }
            });

            function showKelasField() {
                if (roleSelect && kelasField) {
                    roleSelect.addEventListener("change", function() {
                        if (this.value === "pengajar") {
                            kelasField.classList.remove("hidden");
                        } else {
                            kelasField.classList.add("hidden");
                            resetMultipleSelect();
                        }
                    });

                    if (roleSelect.value === "pengajar") {
                        kelasField.classList.remove("hidden");
                    }
                }
            }

            function setMultipleSelectValues(kelasData) {
                const selectedIds = kelasData.map(kelas => String(kelas.id));

                window.dispatchEvent(new CustomEvent('setMultipleSelectValues', {
                    detail: {
                        fieldId: 'kelas_id',
                        values: selectedIds
                    }
                }));

                setTimeout(() => {
                    const multipleSelectDiv = kelasField.querySelector('[x-data]');
                    if (multipleSelectDiv) {
                        try {
                            const alpineComponent = Alpine.$data(multipleSelectDiv);
                            if (alpineComponent) {
                                alpineComponent.selectedValues = selectedIds;
                            }
                        } catch (error) {
                            console.log('Fallback: Setting values via direct manipulation');
                            setHiddenInputValues(selectedIds);
                        }
                    }
                }, 150);
            }

            function resetMultipleSelect() {
                window.dispatchEvent(new CustomEvent('resetMultipleSelect', {
                    detail: {
                        fieldId: 'kelas_id'
                    }
                }));

                setTimeout(() => {
                    const multipleSelectDiv = kelasField.querySelector('[x-data]');
                    if (multipleSelectDiv) {
                        try {
                            const alpineComponent = Alpine.$data(multipleSelectDiv);
                            if (alpineComponent) {
                                alpineComponent.selectedValues = [];
                                alpineComponent.searchQuery = '';
                                alpineComponent.isOpen = false;
                            }
                        } catch (error) {
                            const hiddenInputs = kelasField.querySelectorAll('input[type="hidden"]');
                            hiddenInputs.forEach(input => input.remove());
                        }
                    }
                }, 150);
            }

            function setHiddenInputValues(values) {
                const existingInputs = kelasField.querySelectorAll('input[type="hidden"][name="kelas_id[]"]');
                existingInputs.forEach(input => input.remove());

                const container = kelasField.querySelector('[x-data]');
                values.forEach(value => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'kelas_id[]';
                    input.value = value;
                    container.appendChild(input);
                });
            }
        });
    </script>
@endsection
