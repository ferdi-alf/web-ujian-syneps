@props([
    'headers' => [],
    'data' => [],
    'columns' => [],
    'showActions' => false,
    'actionButtons' => null,
    'searchBar' => false,
    'truncate' => false,
    'rowPerPage' => 5,
    'position' => 'left',
    'autoFilter' => null, // ['column_index' => 'Filter Label']
    'filterPlaceholder' => 'Semua',
    'disablePagination' => false, // New prop to disable pagination
])

<div class="w-full">
    <div class="flex flex-col md:flex-row gap-4 mb-4">
        @if ($searchBar)
            <div class="flex-1">
                <x-fragments.search-input />
            </div>
        @endif

        @if ($autoFilter)
            @foreach ($autoFilter as $columnIndex => $filterLabel)
                <div class="flex-shrink-0 md:w-64">
                    <label for="auto-filter-{{ $columnIndex }}" class="block text-sm font-medium text-gray-700 mb-1">
                        Filter by {{ $filterLabel }}
                    </label>
                    <select id="auto-filter-{{ $columnIndex }}"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        data-column-index="{{ $columnIndex }}">
                        <option value="all">{{ $filterPlaceholder }}</option>
                    </select>
                </div>
            @endforeach
        @endif
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-{{ $position }} text-gray-600" id="enhanced-table">
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
            <tbody id="table-body">
                @foreach ($data as $index => $row)
                    <tr class="bg-white border-b table-row" data-index="{{ $index }}">
                        @foreach ($columns as $columnIndex => $column)
                            <td class="px-6 py-4" data-column="{{ $columnIndex }}">
                                @php
                                    $content = $column($row, $index);
                                @endphp
                                @if ($truncate)
                                    <div class="truncate-text">
                                        {!! $content !!}
                                    </div>
                                @else
                                    <div class="max-lines-2">
                                        {!! $content !!}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                        @if ($showActions)
                            <td class="px-6 py-4">
                                @if ($actionButtons)
                                    {!! $actionButtons($row) !!}
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (!$disablePagination)
        <div class="flex overflow-x-auto items-center md:flex-row justify-between px-6 py-3 bg-white">
            <div class="flex flex-col space-y-1.5 md:flex-row items-center space-x-2">
                <label for="rowsPerPage" class="text-sm text-gray-700">Rows per page:</label>
                <select id="rowsPerPage"
                    class="border w-14 appearance-none space-x-2 bg-white border-gray-300 rounded px-2 py-1 text-sm">
                    <option value="5" {{ $rowPerPage == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ $rowPerPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $rowPerPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $rowPerPage == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>

            <div class="flex flex-col space-y-1.5 md:flex-row items-center space-x-2">
                <span id="pagination-info" class="text-sm text-gray-700"></span>
                <div class="flex space-x-1">
                    <button id="prev-btn"
                        class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <div id="page-numbers" class="flex space-x-1"></div>
                    <button id="next-btn"
                        class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    .max-lines-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
        max-height: 2.8em;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('enhanced-table');
        const tableBody = document.getElementById('table-body');
        const searchInput = document.getElementById('search-input');
        const rowsPerPageSelect = document.getElementById('rowsPerPage');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const paginationInfo = document.getElementById('pagination-info');
        const pageNumbers = document.getElementById('page-numbers');

        let currentPage = 1;
        let rowsPerPage = parseInt(rowsPerPageSelect?.value) || {{ $rowPerPage }};
        let filteredRows = Array.from(document.querySelectorAll('.table-row'));
        let allRows = Array.from(document.querySelectorAll('.table-row'));
        let filterSelects = [];

        console.log('Total Rows Loaded:', allRows.length); // Debug: Log total rows
        console.log('Initial Rows Per Page:', rowsPerPage); // Debug: Log rowsPerPage

        @if ($autoFilter)
            @foreach ($autoFilter as $columnIndex => $filterLabel)
                initializeAutoFilter({{ $columnIndex }});
            @endforeach
        @endif

        @if (!$disablePagination)
            updateTable();
        @else
            // Show all rows if pagination is disabled
            allRows.forEach(row => row.style.display = '');
        @endif

        function initializeAutoFilter(columnIndex) {
            const filterSelect = document.getElementById('auto-filter-' + columnIndex);
            if (!filterSelect) return;

            const uniqueValues = new Set();
            allRows.forEach(row => {
                const cell = row.querySelector('td[data-column="' + columnIndex + '"]');
                if (cell) {
                    let cellText = cell.textContent || cell.innerText || '';
                    cellText = cellText.trim();
                    if (cellText && cellText !== '-') {
                        uniqueValues.add(cellText);
                    }
                }
            });

            const sortedValues = Array.from(uniqueValues).sort();
            sortedValues.forEach(value => {
                const option = document.create Bills
                option.value = value;
                option.textContent = value;
                filterSelect.appendChild(option);
            });

            filterSelect.addEventListener('change', function() {
                applyFilters();
            });

            filterSelects.push({
                element: filterSelect,
                columnIndex: columnIndex
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                applyFilters();
            });
        }

        if (rowsPerPageSelect) {
            rowsPerPageSelect.addEventListener('change', function() {
                rowsPerPage = parseInt(this.value);
                console.log('Rows Per Page Changed:', rowsPerPage); // Debug
                currentPage = 1;
                updateTable();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    updateTable();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    updateTable();
                }
            });
        }

        function applyFilters() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

            filteredRows = allRows.filter(row => {
                let isVisible = true;

                if (searchTerm) {
                    const text = row.textContent.toLowerCase();
                    isVisible = isVisible && text.includes(searchTerm);
                }

                filterSelects.forEach(filter => {
                    const filterValue = filter.element.value;
                    if (filterValue !== 'all') {
                        const cell = row.querySelector('td[data-column="' + filter.columnIndex +
                            '"]');
                        if (cell) {
                            const cellText = (cell.textContent || cell.innerText || '').trim();
                            isVisible = isVisible && (cellText === filterValue);
                        }
                    }
                });

                return isVisible;
            });

            currentPage = 1;
            updateTable();
        }

        function updateTable() {
            allRows.forEach(row => row.style.display = 'none');

            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = Math.min(startIndex + rowsPerPage, totalRows);

            console.log('Total Rows:', totalRows, 'Start Index:', startIndex, 'End Index:', endIndex); // Debug

            for (let i = startIndex; i < endIndex; i++) {
                if (filteredRows[i]) {
                    filteredRows[i].style.display = '';
                }
            }

            if (totalRows > 0) {
                paginationInfo.textContent = `${startIndex + 1}-${endIndex} of ${totalRows}`;
            } else {
                paginationInfo.textContent = '0-0 of 0';
            }

            if (prevBtn) prevBtn.disabled = currentPage <= 1;
            if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
            updatePageNumbers(totalPages);
        }

        function updatePageNumbers(totalPages) {
            pageNumbers.innerHTML = '';
            if (totalPages <= 1) return;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);

            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className =
                    `px-3 py-1 text-sm border rounded ${i === currentPage ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-50'}`;
                pageBtn.addEventListener('click', function() {
                    currentPage = i;
                    updateTable();
                });
                pageNumbers.appendChild(pageBtn);
            }
        }
    });
</script>
