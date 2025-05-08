<script setup>
import { reactive } from "vue";
import { useAuthStore } from "../../stores/auth";
import router from '../../router'

const authStore = useAuthStore();

const formData = reactive({
	email: "",
	password: "",
});

const handleSubmit = () => {
	authStore.login(formData).then(() => {
		if (authStore.accessToken) {
			router.push({ name: "home" });
		}
	});
};
</script>

<template>
	<form @submit.prevent="handleSubmit" class="max-w-md mx-auto bg-white p-8">
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

		<div>
			<button
				type="submit"
				class="w-full p-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600"
			>
				Login
			</button>
		</div>
	</form>
</template>
