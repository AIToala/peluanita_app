<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { ColumnDef, SortingState } from '@tanstack/vue-table';
import {
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
} from 'lucide-vue-next';

import { valueUpdater } from '@/lib/utils';
import {
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { ref } from 'vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from './table';

interface DataTableProps<T> {
    columns: ColumnDef<T, any>[];
    data: T[];
    current_page: number;
    per_page: number;
    last_page: number;
    columnFilters?: any[];
    axiosCall: (payload: {
        data: {};
        pagination: { page: number; per_page: number };
    }) => Promise<void>;
}
const props = defineProps<DataTableProps<any>>();
const columnFilters = ref(props.columnFilters);
const sorting = ref<SortingState>([]);
const rowSelection = ref({});
const pagination = ref({
    pageIndex: props.current_page - 1,
    pageSize: props.per_page,
});

const table = useVueTable({
    get data() {
        return props.data || [];
    },
    get columns() {
        return props.columns;
    },
    pageCount: props.last_page,
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
        get rowSelection() {
            return rowSelection.value;
        },
        get pagination() {
            return pagination.value;
        },
    },
    enableRowSelection: true,
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: (updaterOrValue) =>
        valueUpdater(updaterOrValue, columnFilters),
    onRowSelectionChange: (updaterOrValue) =>
        valueUpdater(updaterOrValue, rowSelection),
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    manualPagination: true,
    onPaginationChange: async (updater) => {
        valueUpdater(updater, pagination);
        await props.axiosCall({
            data: {},
            pagination: {
                page: pagination.value.pageIndex + 1,
                per_page: pagination.value.pageSize,
            },
        });
    },
});
</script>

<template>
    <div class="min-w-full space-y-4 overflow-x-auto">
        <div class="rounded-md border">
            <Table class="w-full">
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                        >
                            <FlexRender
                                v-if="!header.isPlaceholder"
                                :render="header.column.columnDef.header"
                                :props="header.getContext()"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            :data-state="row.getIsSelected() && 'selected'"
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </TableCell>
                        </TableRow>
                    </template>

                    <TableRow v-else>
                        <TableCell
                            :colspan="props.columns.length"
                            class="h-24 text-center"
                        >
                            No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-between px-2">
            <div class="flex-1 text-sm text-muted-foreground">
                {{ table.getFilteredSelectedRowModel().rows.length }} of
                {{ table.getFilteredRowModel().rows.length }} row(s) selected.
            </div>
            <div class="flex items-center space-x-6 lg:space-x-8">
                <div class="flex items-center space-x-2">
                    <p class="text-sm font-medium">Rows per page</p>
                    <Select
                        :model-value="`${table.getState().pagination.pageSize.toString()}`"
                        @update:model-value="
                            (value) => table.setPageSize(Number(value))
                        "
                    >
                        <SelectTrigger class="h-8 w-[70px]">
                            <SelectValue
                                :placeholder="`${table.getState().pagination.pageSize.toString()}`"
                            />
                        </SelectTrigger>
                        <SelectContent side="top">
                            <SelectItem
                                v-for="pageSize in [10, 20, 30, 40, 50]"
                                :key="pageSize"
                                :value="`${pageSize.toString()}`"
                            >
                                {{ pageSize }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div
                    class="flex w-[100px] items-center justify-center text-sm font-medium"
                >
                    Page {{ table.getState().pagination.pageIndex + 1 }} of
                    {{ table.getPageCount() }}
                </div>
                <div class="flex items-center space-x-2">
                    <Button
                        variant="outline"
                        class="hidden h-8 w-8 p-0 lg:flex"
                        :disabled="!table.getCanPreviousPage()"
                        @click="table.setPageIndex(0)"
                    >
                        <span class="sr-only">Go to first page</span>
                        <ChevronsLeft class="h-4 w-4" />
                    </Button>
                    <Button
                        variant="outline"
                        class="h-8 w-8 p-0"
                        :disabled="!table.getCanPreviousPage()"
                        @click="table.previousPage()"
                    >
                        <span class="sr-only">Go to previous page</span>
                        <ChevronLeft class="h-4 w-4" />
                    </Button>
                    <Button
                        variant="outline"
                        class="h-8 w-8 p-0"
                        :disabled="!table.getCanNextPage()"
                        @click="table.nextPage()"
                    >
                        <span class="sr-only">Go to next page</span>
                        <ChevronRight class="h-4 w-4" />
                    </Button>
                    <Button
                        variant="outline"
                        class="hidden h-8 w-8 p-0 lg:flex"
                        :disabled="!table.getCanNextPage()"
                        @click="table.setPageIndex(table.getPageCount() - 1)"
                    >
                        <span class="sr-only">Go to last page</span>
                        <ChevronsRight class="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media (max-width: 640px) {
    .table-cell {
        display: block;
        width: 100%;
        box-sizing: border-box;
    }
    .table-row {
        display: block;
        margin-bottom: 1rem;
    }
}
</style>
