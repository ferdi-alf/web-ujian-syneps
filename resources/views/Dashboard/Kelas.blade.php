@extends('layouts.dashboard-layouts')

@section('content')
    <div>
        <div class="flex justify-end">
            <x-fragments.modal-button target="add-jurusan-modal" variant="emerald">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Kelas
            </x-fragments.modal-button>
        </div>

        <x-fragments.form-modal id="add-jurusan-modal" title="Tambah Kelas" action="{{ route('kelas.store') }}">
            <x-fragments.text-field label="Nama Kelas" name="name" placeholder="Masukan Nama Kelas..." required />
        </x-fragments.form-modal>

        <div class="mt-6">
            <x-reusable-table :headers="['No', 'Nama Kelas']" :data="$kelas" :columns="[fn($row, $i) => $i + 1, fn($row) => $row->nama]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalId' => 'modal-update-kelas-' . $row->id,
                'updateRoute' => route('kelas.update', $row->id),
                'deleteRoute' => route('kelas.destroy', $row->id),
            ])"
                :searchBar="true" :truncate="true" :rowPerPage="10" position="left" />
        </div>
        @foreach ($kelas as $row)
            <x-fragments.form-modal id="modal-update-kelas-{{ $row->id }}" title="Edit Kelas"
                action="{{ route('kelas.update', $row->id) }}" method="PUT">
                <x-fragments.text-field label="Nama Kelas" name="name" :value="$row->nama" required />
            </x-fragments.form-modal>
        @endforeach
    </div>
@endsection
