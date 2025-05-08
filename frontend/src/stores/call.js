import { defineStore } from "pinia";
import { useAuthStore } from "./auth";

export const useCallStore = defineStore("callStore", {
	state: () => ({
		calls: [],
		currentCall: null,
		loading: false,
		error: null,
	}),

	getters: {
		hasError: (state) => state.error !== null,
	},

	actions: {
		async handleApiCall(apiCall) {
			const authStore = useAuthStore();
			this.loading = true;
			this.error = null;

			try {
				if (!authStore.isAuthenticated) {
					throw new Error("Not authenticated");
				}

				return await apiCall();
			} catch (error) {
				this.error = error.message || "An unknown error occurred";
				console.error("API Error:", error);
				throw error;
			} finally {
				this.loading = false;
			}
		},

		async fetchCalls() {
			return this.handleApiCall(async () => {
				const authStore = useAuthStore();
				const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/calls`, {
					headers: {
						Accept: "application/json",
						Authorization: `Bearer ${authStore.accessToken}`,
					},
				});

				if (!response.ok) {
					const errorData = await response.json().catch(() => ({}));
					throw new Error(errorData.message || `HTTP ${response.status}`);
				}

				const data = await response.json();
				this.calls = data.data;
				return data;
			});
		},

		async fetchCall(id) {
			return this.handleApiCall(async () => {
				const authStore = useAuthStore();
				const response = await fetch(
					`${import.meta.env.VITE_API_BASE_URL}/api/calls/${id}`,
					{
						headers: {
							Authorization: `Bearer ${authStore.accessToken}`,
						},
					}
				);

				if (!response.ok) {
					const errorData = await response.json().catch(() => ({}));
					throw new Error(errorData.message || `HTTP ${response.status}`);
				}

				const data = await response.json();
				this.currentCall = data.data;
				return data;
			});
		},

		async createCall(callData) {
			return this.handleApiCall(async () => {
				const authStore = useAuthStore();
				const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/calls`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						Authorization: `Bearer ${authStore.accessToken}`,
					},
					body: JSON.stringify(callData),
				});

				if (!response.ok) {
					const errorData = await response.json().catch(() => ({}));
					throw new Error(errorData.message || `HTTP ${response.status}`);
				}

				const data = await response.json();
				this.calls.unshift(data.data);
				return data;
			});
		},

		async updateCall({ id, ...callData }) {
			return this.handleApiCall(async () => {
				const authStore = useAuthStore();
				const response = await fetch(
					`${import.meta.env.VITE_API_BASE_URL}/api/calls/${id}`,
					{
						method: "PUT",
						headers: {
							"Content-Type": "application/json",
							Authorization: `Bearer ${authStore.accessToken}`,
						},
						body: JSON.stringify(callData),
					}
				);

				if (!response.ok) {
					const errorData = await response.json().catch(() => ({}));
					throw new Error(errorData.message || `HTTP ${response.status}`);
				}

				const data = await response.json();
				const index = this.calls.findIndex((c) => c.id === id);
				if (index !== -1) {
					this.calls[index] = data.data;
				}
				this.currentCall = data.data;
				return data;
			});
		},

		async deleteCall(id) {
			return this.handleApiCall(async () => {
				const authStore = useAuthStore();
				const response = await fetch(
					`${import.meta.env.VITE_API_BASE_URL}/api/calls/${id}`,
					{
						method: "DELETE",
						headers: {
							Authorization: `Bearer ${authStore.accessToken}`,
						},
					}
				);

				if (!response.ok) {
					const errorData = await response.json().catch(() => ({}));
					throw new Error(errorData.message || `HTTP ${response.status}`);
				}

				this.calls = this.calls.filter((call) => call.id !== id);
				return { success: true };
			});
		},

		async checkForTicket(id) {
			return this.handleApiCall(async () => {
				const authStore = useAuthStore();
				const response = await fetch(
					`${import.meta.env.VITE_API_BASE_URL}/api/calls/${id}/check-ticket`,
					{
						headers: {
							Authorization: `Bearer ${authStore.accessToken}`,
						},
					}
				);

				if (!response.ok) {
					const errorData = await response.json().catch(() => ({}));
					throw new Error(errorData.message || `HTTP ${response.status}`);
				}

				const data = await response.json();
				this.currentCall = data.call;
				return data;
			});
		},
	},
});
