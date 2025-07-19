@props([
    'headers' => [],
    'data' => [],
    'columns' => [],
    'showActions' => false,
])

<table class="w-full text-sm text-left text-gray-500">
    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
        <tr>
            @foreach ($headers as $header)
                <th scope="col" class="px-6 py-3">{{ $header }}</th>
            @endforeach
            @if ($showActions)
                <th scope="col" class="px-6 py-3">Action</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $row)
            <tr class="bg-white border-b">
                @foreach ($columns as $column)
                    <td class="px-6 py-4">
                        {{ $column($row, $index) }}
                    </td>
                @endforeach

                @if ($showActions)
                    <td class="px-6 py-4 flex space-x-2">
                        {{-- Action buttons slot --}}
                        {{ $actionButtons($row) }}
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
