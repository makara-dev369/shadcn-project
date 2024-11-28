<script setup>
// import Layout from './Layout'
import { Head } from '@inertiajs/vue3'
import { Button} from '@/components/ui/button'
// Layout
import Layout from "../../Layout/App.vue";
</script>

<template>
  <Layout>
    <Head title="Welcome" />
    <h1 class="text-blue-600 text-center p-5">Welcome</h1>
    <Button>Click Me </Button>
    <!-- <p>Hello {{ user.name }}, welcome to your first Inertia app!</p> -->
  </Layout>
</template>