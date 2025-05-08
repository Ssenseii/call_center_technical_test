<template>
	<header class="bg-gray-900 text-white py-4 shadow-md">
		<div class="container px-6">
			<nav class="mx-auto flex justify-between items-center">
				<!-- Navigation Links -->
				<div class="space-x-6">
					<RouterLink
						:to="{ name: 'home' }"
						class="text-xl font-semibold hover:text-gray-400 transition-colors"
						>Dashboard</RouterLink
					>

					<router-link
						to="/calls"
						class="text-md font-medium hover:text-gray-400 transition-colors"
						>Calls</router-link
					>

					<router-link
						to="/tickets"
						class="text-md font-medium hover:text-gray-400 transition-colors"
						>Tickets</router-link
					>
				</div>

				<!-- Authentication and User Info -->
				<div class="space-x-6 flex items-center">
					<!-- Register/Login for Guests -->
					<RouterLink
						v-if="!isAuthenticated"
						:to="{ name: 'register' }"
						class="text-md font-normal hover:text-gray-300 transition-colors"
						>Register</RouterLink
					>

					<RouterLink
						v-if="!isAuthenticated"
						:to="{ name: 'login' }"
						class="text-md font-normal hover:text-gray-300 transition-colors"
						>Login</RouterLink
					>

					<!-- Display User Info and Logout Button -->
					<div
						v-if="isAuthenticated"
						class="flex items-center space-x-3 text-md font-normal text-gray-300"
					>
						<span class="capitalize">{{ userName }} - {{ userRole }}</span>
						<button
							@click="logout"
							class="btn btn-danger text-sm bg-red-600 hover:bg-red-700 text-white rounded-md px-3 py-2 transition-all"
						>
							Logout
						</button>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<RouterView />
</template>

<script>
import { useAuthStore } from "@/stores/auth";

export default {
	computed: {
		isAuthenticated() {
			const authStore = useAuthStore();
			return authStore.isAuthenticated();
		},
		userName() {
			const authStore = useAuthStore();
			return authStore.user ? authStore.user.name : ""; // Adjust to how user name is stored
		},
		userRole() {
			const authStore = useAuthStore();
			return authStore.user ? authStore.user.role : ""; // Adjust to how role is stored
		},
	},
	methods: {
		logout() {
			const authStore = useAuthStore();
			authStore.logout();
		},
	},
};
</script>
