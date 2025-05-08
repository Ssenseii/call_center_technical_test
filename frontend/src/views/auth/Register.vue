<script setup>
import { reactive } from "vue";
import { useAuthStore } from "../../stores/auth";
import router from '../../router'


const authStore = useAuthStore();

const formData = reactive({
	name: "",
	email: "",
	password: "",
	password_confirmation: "",
});

const handleSubmit = () => {
	authStore.register(formData).then(() => {
		if (authStore.accessToken) {
			// Redirect to home or another protected route on success
			router.push({ name: "home" });
		}
	});
};
</script>

<template>
	<form @submit.prevent="handleSubmit" class="max-w-md mx-auto bg-white p-8">
		<div class="mb-4">
			<label for="name" class="block text-sm font-medium text-gray-700">Name</label>
			<input
				id="name"
				type="text"
				v-model="formData.name"
				placeholder="Enter your name"
				class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
			/>
			<p v-if="authStore.errors.name" class="text-red-500 text-xs mt-1">
				{{ authStore.errors.name[0] }}
			</p>
		</div>

		<div class="mb-4">
			<label for="email" class="block text-sm font-medium text-gray-700">Email</label>
			<input
				id="email"
				type="email"
				v-model="formData.email"
				placeholder="Enter your email"
				class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
			/>
			<p v-if="authStore.errors.email" class="text-red-500 text-xs mt-1">
				{{ authStore.errors.email[0] }}
			</p>
		</div>

		<div class="mb-4">
			<label for="password" class="block text-sm font-medium text-gray-700">Password</label>
			<input
				id="password"
				type="password"
				v-model="formData.password"
				placeholder="Enter your password"
				class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
			/>
			<p v-if="authStore.errors.password" class="text-red-500 text-xs mt-1">
				{{ authStore.errors.password[0] }}
			</p>
		</div>

		<div class="mb-4">
			<label for="password_confirmation" class="block text-sm font-medium text-gray-700"
				>Confirm Password</label
			>
			<input
				id="password_confirmation"
				type="password"
				v-model="formData.password_confirmation"
				placeholder="Confirm your password"
				class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
			/>
		</div>

		<div>
			<button
				type="submit"
				class="w-full p-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600"
			>
				Register
			</button>
		</div>
	</form>
</template>
