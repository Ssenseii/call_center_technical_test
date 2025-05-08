import { defineStore } from "pinia";
import { useAuthStore } from "./auth";

export const useTicketStore = defineStore("ticketStore", {
	state: () => ({
		tickets: [],
		currentTicket: null,
		availableCalls: [], // calls you can attach to a ticket
		loading: false,
		error: null,
	}),
	getters: {
		hasError: (state) => state.error !== null,
	},
	actions: {
		async handleApiCall(apiCall) {
			const auth = useAuthStore();
			this.loading = true;
			this.error = null;
			try {
				if (!auth.isAuthenticated) throw new Error("Not authenticated");
				return await apiCall();
			} catch (err) {
				this.error = err.message || "Unknown error";
				console.error(err);
				throw err;
			} finally {
				this.loading = false;
			}
		},

		// fetch all tickets
		async fetchTickets() {
			return this.handleApiCall(async () => {
				const auth = useAuthStore();
				const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/tickets`, {
					headers: { Authorization: `Bearer ${auth.accessToken}` },
				});
				if (!res.ok) throw new Error((await res.json()).message || res.status);
				const data = await res.json();
				this.tickets = data.data.data || data.data; // if paginated vs non‑paginated
				return data;
			});
		},
		// fetch single ticket + its comments
		async fetchTicket(id) {
			return this.handleApiCall(async () => {
				const auth = useAuthStore();
				const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/tickets/${id}`, {
					headers: { Authorization: `Bearer ${auth.accessToken}` },
				});
				if (!res.ok) throw new Error((await res.json()).message || res.status);
				const data = await res.json();
				this.currentTicket = data.data;
				return data;
			});
		},
		// fetch calls for create/edit form
		async fetchAvailableCalls() {
			return this.handleApiCall(async () => {
				const auth = useAuthStore();
				const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/tickets/create`, {
					headers: { Authorization: `Bearer ${auth.accessToken}` },
				});
				if (!res.ok) throw new Error((await res.json()).message || res.status);
				const data = await res.json();
				this.availableCalls = data.data.calls;
				return data;
			});
		},
		// create a ticket
		async createTicket(payload) {
			return this.handleApiCall(async () => {
				const auth = useAuthStore();
				const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/tickets`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						Authorization: `Bearer ${auth.accessToken}`,
					},
					body: JSON.stringify(payload),
				});
				if (!res.ok) throw new Error((await res.json()).message || res.status);
				const data = await res.json();
				this.tickets.unshift(data.data);
				return data;
			});
		},
		// update ticket
		async updateTicket({ id, ...payload }) {
			return this.handleApiCall(async () => {
				const auth = useAuthStore();
				const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/tickets/${id}`, {
					method: "PUT",
					headers: {
						"Content-Type": "application/json",
						Authorization: `Bearer ${auth.accessToken}`,
					},
					body: JSON.stringify(payload),
				});
				if (!res.ok) throw new Error((await res.json()).message || res.status);
				const data = await res.json();
				const idx = this.tickets.findIndex((t) => t.id === id);
				if (idx !== -1) this.tickets[idx] = data.data;
				this.currentTicket = data.data;
				return data;
			});
		},
		// delete ticket
		async deleteTicket(id) {
			return this.handleApiCall(async () => {
				const auth = useAuthStore();
				const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/api/tickets/${id}`, {
					method: "DELETE",
					headers: { Authorization: `Bearer ${auth.accessToken}` },
				});
				if (!res.ok) throw new Error((await res.json()).message || res.status);
				this.tickets = this.tickets.filter((t) => t.id !== id);
				return { success: true };
			});
		},
		// update only status
		async updateStatus({ id, status }) {
			return this.handleApiCall(async () => {
				const auth = useAuthStore();
				const res = await fetch(
					`${import.meta.env.VITE_API_BASE_URL}/api/tickets/${id}/status`,
					{
						method: "POST",
						headers: {
							"Content-Type": "application/json",
							Authorization: `Bearer ${auth.accessToken}`,
						},
						body: JSON.stringify({ status }),
					}
				);
				if (!res.ok) throw new Error((await res.json()).message || res.status);
				const data = await res.json();
				const idx = this.tickets.findIndex((t) => t.id === id);
				if (idx !== -1) this.tickets[idx] = data.data;
				if (this.currentTicket?.id === id) this.currentTicket.status = status;
				return data;
			});
		},
	},
});
