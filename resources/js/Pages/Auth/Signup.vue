<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
//Form 
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const errors = ref({});

const submit = () =>{
    form.post('/signup', {
        onError:(error) =>{
            errors.value = error;
        }
    })
}

</script>

<template>
    <div class="mx-auto h-screen w-screen flex justify-center items-center ">
        <Card class="mx-auto max-w-sm">
            <CardHeader>
            <CardTitle class="text-xl">
                Sign Up
            </CardTitle>
            <CardDescription>
                Enter your information to create an account
            </CardDescription>
            </CardHeader>
            <CardContent>
            <form @submit.prevent="submit">
                <div class="grid gap-4">
                    <div class="grid  gap-4">
                    <div class="grid gap-2">
                        <Label for="username">User name</Label>
                        <Input id="username" 
                            v-model="form.name"  
                            :class="{'is-invalid': errors.name}"   
                            placeholder="" 
                            required 
                            autocomplete="false" 
                            autofocus/>
                        <span v-if="errors.name">{{ errors.name }}</span>
                    </div>
                    </div>
                    <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"  
                        :class="{'is-invalid': errors.email}"
                        placeholder="m@example.com"
                        required
                    />
                    <span v-if="errors.email">{{ errors.email }}</span>

                    </div>
                    <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input 
                        id="password" 
                        type="password"
                         v-model="form.password"  
                        :class="{'is-invalid': errors.password}"
                        />
                    <span v-if="errors.password">{{ errors.password }}</span>
                    <Label for="password">Password Confirm</Label>
                    <Input 
                        id="password_confirm" 
                        type="password"
                         v-model="form.password_confirmation"  
                        :class="{'is-invalid': errors.password_confirmation}"
                        />
                    <span v-if="errors.password_confirmation">{{ errors.password_confirmation }}</span>
                    </div>
                    <Button type="submit" class="w-full">
                    Create an account
                    </Button>
                    <Button variant="outline" class="w-full" disabled>
                    Sign up with GitHub
                    </Button>
                </div>
                <div class="mt-4 text-center text-sm">
                    Already have an account?
                    <a href="#" class="underline">
                    Sign in
                    </a>
                </div>
            </form>
            </CardContent>
        </Card>
    </div>
</template>