import { defineStore } from "pinia";

export const useAuthStore = defineStore("authStore", {
	state: () => ({
		user: JSON.parse(localStorage.getItem("user")) || null,
		errors: {},
		accessToken: localStorage.getItem("accessToken") || null, // Retrieve token from localStorage
	}),

	actions: {
		// Register a new user
		async register(formData) {
			const baseUrl = `${import.meta.env.VITE_API_BASE_URL}/api`;
			try {
				const res = await fetch(`${baseUrl}/register`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json",
					},
					body: JSON.stringify(formData),
				});

				const data = await res.json();

				if (data.errors) {
					this.errors = data.errors;
				} else {
					this.user = data.user;
					this.accessToken = data.access_token;
					// Save to localStorage
					localStorage.setItem("user", JSON.stringify(data.user));
					localStorage.setItem("accessToken", data.access_token);
				}
			} catch (error) {
				console.error(error);
			}
		},

		// Login an existing user
		async login(formData) {
			const baseUrl = `${import.meta.env.VITE_API_BASE_URL}/api`;
			try {
				const res = await fetch(`${baseUrl}/login`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json",
					},
					body: JSON.stringify(formData),
				});

				const data = await res.json();

				if (data.errors) {
					this.errors = data.errors;
				} else {
					this.user = data.user;
					this.accessToken = data.access_token;
					// Save to localStorage
					localStorage.setItem("user", JSON.stringify(data.user));
					localStorage.setItem("accessToken", data.access_token);
				}
			} catch (error) {
				console.error(error);
			}
		},

		// Logout the user
		logout() {
			this.user = null;
			this.accessToken = null;
			localStorage.removeItem("user");
			localStorage.removeItem("accessToken");
		},

		isAuthenticated() {
			return this.accessToken !== null;
		},
	},
});
