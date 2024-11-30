<script setup>
// import Layout from './Layout'
import { Head, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Button } from '@/components/ui/button'
// Layout
import Layout from "../../Layout/App.vue";
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { ArrowUpDown, ChevronDown } from 'lucide-vue-next'

import { h, ref, computed } from 'vue'
import DropdownAction from './DataTableDemoColumn.vue'
import { Checkbox } from '@/components/ui/checkbox'
import { Badge } from '@/components/ui/badge'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { valueUpdater } from '@/lib/utils'
import { ChevronRightIcon, ChevronLeftIcon, DoubleArrowLeftIcon, DoubleArrowRightIcon } from "@radix-icons/vue";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'

import { Plus, Upload } from 'lucide-vue-next'

import { Textarea } from '@/components/ui/textarea'
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input'
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'

//Props Data 
const props = defineProps({
  data: Array,
  filter: Array,
})
const data = props.data.data;
const columns = [
  {
    id: 'select',
    header: ({ table }) => h(Checkbox, {
      'checked': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:checked': value => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Select all',
    }),
    cell: ({ row }) => h(Checkbox, {
      'checked': row.getIsSelected(),
      'onUpdate:checked': value => row.toggleSelected(!!value),
      'ariaLabel': 'Select row',
    }),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('name')),
  },
  {
    accessorKey: 'title',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Title', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('title')),
  },
  {
    accessorKey: 'price',
    header: () => h('div', { class: 'text-right' }, 'Price'),
    cell: ({ row }) => {
      const amount = Number.parseFloat(row.getValue('price'))

      // Format the amount as a dollar amount
      const formatted = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(amount)

      return h('div', { class: 'text-right font-medium' }, formatted)
    },
  },
  {
    accessorKey: 'is_active',
    header: 'Status',
    cell: ({ row }) => {
      const status = row.getValue('is_active');

      if (status) {
        return h('div', h(Badge, 'Active'))
      } else {
        return h('div', h(Badge, { variant: 'outline' }, 'Inactive'))
      }
    },
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const payment = row.original

      return h('div', { class: 'relative' }, h(DropdownAction, {
        payment,
        onEdit,
        onExpand: row.toggleExpanded,
      }))
    },
  },
]

const sorting = ref([])
const columnFilters = ref(props.filter ?? [])
const columnVisibility = ref({})
const rowSelection = ref({})
const expanded = ref({})
const pageSizes = [1, 2, 3, 5, 10, 15, 30, 40, 50, 100,];
const pagination = ref({
  pageIndex: props.data.current_page - 1,
  pageSize: props.data.per_page,
})


const table = useVueTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  pageCount: props.data.last_page,
  manualPagination: true,
  manualSorting: true,
  manualFiltering: true,
  onPaginationChange: updater => {
    if (typeof updater === 'function') {
      pagination.value = updater(pagination.value);
    } else {
      pagination.value = updater;
    }
    router.get(
      route('product.index'),
      {
        page: pagination.value.pageIndex + 1,
        per_page: pagination.value.pageSize,
        sort_field: sorting.value[0]?.id,
        sort_direction: sorting.value.length == 0 ? undefined : (sorting.value[0]?.desc ? "desc" : "asc"),
      },
      { preserveState: false, preserveScroll: true }
    );
  },
  onSortingChange: updaterOrValue => {
    if (typeof updaterOrValue === 'function') {
      sorting.value = updaterOrValue(sorting.value)
    } else {
      sorting.value = updaterOrValue
    }
    let filters = {};
    if (columnFilters.value) {
      filters = columnFilters.value.reduce((acc, filter) => {
        acc[filter.id] = filter.value
        return acc
      }, {})
    }
    router.get(
      route('product.index'),
      {
        page: pagination.value.pageIndex + 1,
        per_page: pagination.value.pageSize,
        sort_field: sorting.value[0]?.id,
        sort_direction: sorting.value.length == 0 ? undefined : (sorting.value[0]?.desc ? "desc" : "asc"),
        ...filters
      },
      { preserveState: false, preserveScroll: true }
    );
  },
  onColumnFiltersChange: updaterOrValue => {
    if (typeof updaterOrValue === 'function') {
      columnFilters.value = updaterOrValue(columnFilters.value)
    } else {
      columnFilters.value = updaterOrValue
    }
    let filters = {};
    if (columnFilters.value) {
      filters = columnFilters.value.reduce((acc, filter) => {
        acc[filter.id] = filter.value
        return acc
      }, {})
    }
    router.get(
      route('product.index'),
      {
        page: pagination.value.pageIndex + 1,
        per_page: pagination.value.pageSize,
        sort_field: sorting.value[0]?.id,
        sort_direction: sorting.value.length == 0 ? undefined : (sorting.value[0]?.desc ? "desc" : "asc"),
        ...filters

      },
      { preserveState: false, preserveScroll: true }
    );
  },
  onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
    get pagination() { return pagination.value },
  },
})
import {
  CheckCircledIcon,
  MinusCircledIcon,
} from "@radix-icons/vue";
import Filter from './Filter.vue'
//Filter
const filter_status = {
  title: 'Filter Status',
  column: 'is_active',
  data: [
    {
      value: "1",
      label: "Active",
      icon: h(CheckCircledIcon),
    },
    {
      value: "0",
      label: "Inactive",
      icon: h(MinusCircledIcon),
    },
  ]
}

const filter_toolbar = [
  filter_status,
];



const showDialog = ref(false);
const showDialogCreate = () => {
  showDialog.value = true
}

const imageUrl = ref([]);
const productSize = ref(['20 ml', ' 50ml']);

const form = useForm({
  name: '',
  title: '',
  description: '',
  category_ref_id: null,
  brand_ref_id: null,
  price: 0,
  discount_price: 0,
  benefits_content: '',
  ingredients_content: '',
  howtouse_content: '',
  product_size_id: '',
  is_active: 1,
  image: imageUrl.value
})

const errors = ref({});

const submit = () => {
  form.product_size_id = productSize.value.toString()
  form.image = imageUrl.value
  //form here
  form.post('/products', {
    preserveState: true,
    onError: (error) => {
      //show error
      errors.value = error;
    },
    onSuccess: () => {
      //show success
      toast('Event has been created', {
        description: 'Sunday, December 03, 2023 at 9:00 AM',
        action: {
          label: 'Undo',
          onClick: () => console.log('Undo'),
        },
      })

      //Refresh Data
      showDialog.value = false
      setTimeout(() => {
        window.location.reload()
      }, 3000)
    }
  })

}
// Coonfig Key Of Cloudinary:
const cloudName = 'dcqr1t9j4';
const uploadPreset = 'cloudinary-stripe-vuets';


const myWidget = cloudinary.createUploadWidget(
  {
    cloudName: cloudName,
    uploadPreset: uploadPreset
  },
  (error, result) => {
    if (!error && result && result.event === 'success') {
      //Do somthing
      imageUrl.value.push(result.info.secure_url)
    }
  }
);

const openWidget = () => {
  myWidget.open();
}


const onEdit = async (id) => {
  console.log(id);
  // showDialog.value = true
  try {
    const res = await fetch(`/products/${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })
    if (!res.ok) {
      console.error('Error ');
    }
    const data = await res.json();
    console.log(data)

    // Set to form 
    form.name = data.data.name;
    form.price = data.data.price;
    form.discount_price = data.data.discount_price;
    form.howtouse_content = data.data.howtouse_content;
    form.benefits_content = data.data.benefits_content;
    form.description = data.data.description;
    form.category_ref_id = data.data.category_ref_id?.toString();
    form.ingredients_content = data.data.ingredients_content;
    form.brand_ref_id = data.data.brand_ref_id?.toString();
    form.is_active = data.data.is_active;
    form.title = data.data.title;
    form.product_size_id = data.data.product_size_id;

    const imageUrls = data.data.image.map((e) => e.url);
    imageUrl.value = imageUrls


    //Open Dialog
    showDialog.value = true


  } catch (error) {
    console.error(error);
  }


}


</script>

<template>
  <Layout>

    <Head title="Welcome" />
    <template #title>
      Product Page
    </template>
    <div>
      <div class="w-full">
        <div class="flex gap-2 items-center justify-between py-4">
          <div class="flex gap-2">
            <Input class="max-w-sm" placeholder="Filter name..." :model-value="table.getColumn('name')?.getFilterValue()" @update:model-value=" table.getColumn('name')?.setFilterValue($event)" />
            <div v-for="filter in filter_toolbar" :key="filter.title">
              <Filter :column="table.getColumn(filter.column)" :title="filter.title" :options="filter.data"></Filter>
            </div>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" @click="showDialogCreate">
              <Plus class="h-4"></Plus>
              Create New
            </Button>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" class="ml-auto">
                  Columns
                  <ChevronDown class="ml-2 h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuCheckboxItem v-for="column in table.getAllColumns().filter((column) => column.getCanHide())" :key="column.id" class="capitalize" :checked="column.getIsVisible()" @update:checked="(value) => {
                  column.toggleVisibility(!!value)
                }">
                  {{ column.id }}
                </DropdownMenuCheckboxItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
        <div class="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                <TableHead v-for="header in headerGroup.headers" :key="header.id">
                  <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="table.getRowModel().rows?.length">
                <template v-for="row in table.getRowModel().rows" :key="row.id">
                  <TableRow :data-state="row.getIsSelected() && 'selected'">
                    <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                      <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                    </TableCell>
                  </TableRow>
                  <TableRow v-if="row.getIsExpanded()">
                    <TableCell :colspan="row.getAllCells().length">
                      {{ JSON.stringify(row.original) }}
                    </TableCell>
                  </TableRow>
                </template>
              </template>

              <TableRow v-else>
                <TableCell :colspan="columns.length" class="h-24 text-center">
                  No results.
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <div class="flex items-center justify-end space-x-2 py-4">
          <div class="flex-1 text-sm text-muted-foreground">
            {{ table.getFilteredSelectedRowModel().rows.length }} of
            {{ table.getFilteredRowModel().rows.length }} row(s) selected.
          </div>
          <div class="flex items-center space-x-2">
            <p class="text-sm font-medium">Rows per page</p>
            <Select :model-value="table.getState().pagination.pageSize.toString()" @update:model-value="(value) => table.setPageSize(Number(value))">
              <SelectTrigger class="h-8 w-[70px]">
                <SelectValue :placeholder="table.getState().pagination.pageSize.toString()" />
              </SelectTrigger>
              <SelectContent side="top">
                <SelectItem v-for="pageSize in pageSizes" :key="pageSize" :value="pageSize.toString()">
                  {{ pageSize }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="space-x-2">
            <div class="flex items-center space-x-2">
              <Button variant="outline" class="hidden h-8 w-8 p-0 lg:flex" :disabled="!table.getCanPreviousPage()" @click="table.setPageIndex(0)">
                <DoubleArrowLeftIcon class="h-4 w-4" />
              </Button>
              <Button variant="outline" class="h-8 w-8 p-0" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
                <ChevronLeftIcon class="h-4 w-4" />
              </Button>
              <Button variant="outline" class="h-8 w-8 p-0" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                <ChevronRightIcon class="h-4 w-4" />
              </Button>
              <Button variant="outline" class="hidden h-8 w-8 p-0 lg:flex" :disabled="!table.getCanNextPage()" @click="table.setPageIndex(table.getPageCount() - 1)">
                <DoubleArrowRightIcon class="h-4 w-4" />
              </Button>
            </div>

            <!-- <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
              Previous
            </Button>
            <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
              Next
            </Button> -->
          </div>
        </div>
      </div>
      <!-- Dialog -->
      <Dialog v-model:open="showDialog">
        <DialogContent class="max-w-[1225px]">
          <DialogHeader>
            <DialogTitle>Create Product</DialogTitle>
            <DialogDescription>
              Make changes to your profile here. Click save when you're done.
            </DialogDescription>
          </DialogHeader>
          <form>
            <div class="grid grid-cols-2 gap-5 ">
              <!-- Col 1 -->
              <div class="flex flex-col gap-2">
                <div class="grid gap-2">
                  <Label for="name">
                    Name
                  </Label>
                  <Input id="name" v-model="form.name" />
                  <span v-if="errors?.name" class="text-red-600 text-sm">{{ errors.name }}</span>
                </div>
                <div class="grid gap-2">
                  <Label for="title">
                    Title
                  </Label>
                  <Input id="title" v-model="form.title" />
                  <span v-if="errors?.title" class="text-red-600 text-sm">{{ errors.title }}</span>
                </div>
                <div class="grid gap-2">
                  <Label for="title">
                    Description
                  </Label>
                  <Textarea v-model="form.description" />
                </div>
                <div class="grid gap-2">
                  <Label for="category_ref_id">
                    Category
                  </Label>
                  <Select v-model="form.category_ref_id">
                    <SelectTrigger>
                      <SelectValue placeholder="Select a category" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem value="1">
                          Apple
                        </SelectItem>
                        <SelectItem value="2">
                          Banana
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <span v-if="errors?.category_ref_id" class="text-red-600 text-sm">{{ errors.category_ref_id }}</span>

                </div>
                <div class="grid gap-2">
                  <Label for="brand_ref_id">
                    Brand
                  </Label>
                  <Select v-model="form.brand_ref_id">
                    <SelectTrigger>
                      <SelectValue placeholder="Select a brand" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem value="1">
                          Brand 1
                        </SelectItem>
                        <SelectItem value="2">
                          Brand 2
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <span v-if="errors?.brand_ref_id" class="text-red-600 text-sm">{{ errors.brand_ref_id }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                  <div class="grid gap-2">
                    <Label for="price">
                      Price
                    </Label>
                    <Input type="number" v-model="form.price" />
                  </div>
                  <div class="grid gap-2">
                    <Label for="discount_price">
                      Discount Price
                    </Label>
                    <Input type="number" v-model="form.discount_price" />
                  </div>
                </div>
              </div>
              <!-- Col 2 -->
              <div class="flex flex-col gap-2">
                <div class="grid gap-2">
                  <!-- <img alt="Product image" class="aspect-square w-full rounded-md object-cover" height="100" src="https://www.shadcn-vue.com/placeholder.svg" width="300"> -->
                  <div class="grid grid-cols-3 gap-2">
                    <div v-for="img in imageUrl">
                      <img alt="Product image" class="aspect-square w-full rounded-md object-cover" height="45" :src="img" width="45">
                    </div>

                    <button @click="openWidget" class="flex aspect-square w-full items-center justify-center rounded-md border border-dashed" type="button">
                      <Upload class="h-4 w-4 text-muted-foreground" />
                      <span class="sr-only">Upload</span>
                    </button>
                  </div>
                </div>

                <div class="grid gap-2">
                  <Label for="benefits_content">
                    Benefits content
                  </Label>
                  <Textarea v-model="form.benefits_content" />
                </div>
                <div class="grid gap-2">
                  <Label for="ingredients_content">
                    Ingredients content
                  </Label>
                  <Textarea v-model="form.ingredients_content" />
                </div>
                <div class="grid gap-2">
                  <Label for="howtouse_content">
                    Howtouse content
                  </Label>
                  <Textarea v-model="form.howtouse_content" />
                </div>
                <div class="grid gap-2">
                  <Label for="product_size_id">
                    Product size
                  </Label>
                  <TagsInput v-model="productSize">
                    <TagsInputItem v-for="item in productSize" :key="item" :value="item">
                      <TagsInputItemText />
                      <TagsInputItemDelete />
                    </TagsInputItem>
                    <TagsInputInput placeholder="ml , kg .." />
                  </TagsInput>
                </div>
                <div class="flex  items-center gap-2 pt-5">
                  <Label for="is_active" @click="form.is_active = !form.is_active" class="flex items-center gap-2">
                    <Checkbox v-model="form.is_active" :checked="form.is_active == 1" />
                    <div>
                      Status
                    </div>
                  </Label>
                  <!-- Have default 0 -->
                </div>
              </div>
            </div>
          </form>
          <DialogFooter>
            <Button @click="submit">
              Create
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  </Layout>
</template>