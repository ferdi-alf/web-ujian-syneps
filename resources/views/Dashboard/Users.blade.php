@extends('layouts.dashboard-layouts')

@section('content')
    <div>
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-user-modal" variant="emerald">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah User
            </x-fragments.modal-button>
        </div>

        <x-fragments.form-modal id="add-user-modal" title="Tambah User" action="{{ route('users.store') }}">
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
            <script>
                console.log(@json($kelas));
            </script>
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
                'modalId' => 'modal-update-user-' . $row->id,
                'updateRoute' => route('users.update', $row->id),
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
                'modalId' => 'modal-update-user-' . $row->id,
                'updateRoute' => route('users.update', $row->id),
                'deleteRoute' => route('users.destroy', $row->id),
            ])" />
        </div>

        @foreach ($admins->merge($pengajars) as $user)
            <x-fragments.form-modal id="modal-update-user-{{ $user->id }}" title="Edit User"
                action="{{ route('users.update', $user->id) }}" method="PUT">
                <x-fragments.text-field label="Username" name="name" :value="$user->name" required />
                <x-fragments.text-field label="Email" name="email" type="email" :value="$user->email" required />
                <input type="hidden" name="role" value="{{ $user->role }}">

                @if ($user->role === 'pengajar')
                    <x-fragments.multiple-select-badges label="Kelas" name="kelas_id" :options="$kelas
                        ->map(fn($k) => ['value' => (string) $k->id, 'label' => $k->nama])
                        ->values()
                        ->toArray()" :value="$user->pengajarDetail && $user->pengajarDetail->kelas
                        ? $user->pengajarDetail->kelas->pluck('id')->map(fn($id) => (string) $id)->toArray()
                        : []"
                        placeholder="Pilih kelas untuk pengajar" required />
                @endif

                <x-fragments.text-field label="Password (Opsional)" name="password" type="password" />
            </x-fragments.form-modal>
        @endforeach

    </div>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const roleSelect = document.querySelector('select[name="role"]');
            const kelasField = document.getElementById("kelas-field");

            if (roleSelect && kelasField) {
                roleSelect.addEventListener("change", function() {
                    if (this.value === "pengajar") {
                        kelasField.classList.remove("hidden");
                    } else {
                        kelasField.classList.add("hidden");
                    }
                });

                if (roleSelect.value === "pengajar") {
                    kelasField.classList.remove("hidden");
                }
            }
        });
    </script>
@endsection
