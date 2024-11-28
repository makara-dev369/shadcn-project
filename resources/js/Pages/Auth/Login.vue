<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
//Form 
const form = useForm({
    email: '',
    password: '',
});

const errors = ref({});

const submit = () =>{
    form.post('/login', {
        onError:(error) =>{
            errors.value = error;
        }
    })
}
</script>

<template>
    <div class="mx-auto h-screen w-screen flex justify-center items-center ">
        <Card class="w-full max-w-sm h-100">
            <CardHeader>
            <CardTitle class="text-2xl">
                Login
            </CardTitle>
            <CardDescription>
                Enter your email below to login to your account.
            </CardDescription>
            </CardHeader>
            <form @submit.prevent="submit">
                <CardContent class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input 
                        id="email" 
                        type="email" 
                        placeholder="m@example.com" 
                            v-model="form.email"  
                            :class="{'is-invalid': errors.email}"
                        required />
                        <span v-if="errors.email">{{ errors.email }}</span>
                    </div>
                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <Input 
                            id="password" 
                            type="password" 
                            required 
                            v-model="form.password"  
                            :class="{'is-invalid': errors.password}"
                        />
                        <span v-if="errors.password">{{ errors.password }}</span>

                    </div>
                </CardContent>
                <CardFooter>
                    <Button type="submit" class="w-full">
                        Sign in
                    </Button>
                </CardFooter>
            </form>
        </Card>
    </div>
</template>