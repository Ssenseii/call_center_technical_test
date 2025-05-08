import { defineStore } from "pinia";
import { ref } from "vue";
import { useAuthStore } from "./auth"; // Import auth store

export const useCommentStore = defineStore("comment", () => {
	const loading = ref(false);
	const error = ref(null);

	const createComment = async (ticketId, content) => {
		const auth = useAuthStore(); // Access the auth store to get the token
		const apiCall = async () => {
			try {
				loading.value = true;
				error.value = null;

				const response = await fetch(
					`${import.meta.env.VITE_API_BASE_URL}/api/comments/tickets/${ticketId}`,
					{
						method: "POST",
						headers: {
							"Content-Type": "application/json",
							Authorization: `Bearer ${auth.accessToken}`, // Add token from auth store
						},
						body: JSON.stringify({ content }), // Stringify the payload
					}
				);

				// Handle response and check for errors
				if (!response.ok) {
					const errorData = await response.json();
					throw new Error(errorData.message || "Unknown error");
				}

				const data = await response.json();
				console.log("Server response:", data);

				return data.data;
			} catch (err) {
				console.error("Fetch error:", err);

				error.value = err.message || "Unknown error";
				throw err;
			} finally {
				loading.value = false;
			}
		};

		// Use the handleApiCall from ticketStore
		return await apiCall();
	};

	const updateComment = async (commentId, content) => {
		const auth = useAuthStore();
		const apiCall = async () => {
			try {
				loading.value = true;
				error.value = null;

				const response = await fetch(
					`${import.meta.env.VITE_API_BASE_URL}/api/comments/${commentId}`,
					{
						method: "PUT",
						headers: {
							"Content-Type": "application/json",
							Authorization: `Bearer ${auth.accessToken}`,
						},
						body: JSON.stringify({ content }),
					}
				);

				if (!response.ok) {
					const errorData = await response.json();
					throw new Error(errorData.message || "Failed to update comment");
				}

				const data = await response.json();
				return data.data; // Return the updated comment
			} catch (err) {
				error.value = err.message || "Unknown error";
				throw err;
			} finally {
				loading.value = false;
			}
		};

		return await apiCall();
	};

	const deleteComment = async (commentId) => {
		const auth = useAuthStore();
		const apiCall = async () => {
			try {
				loading.value = true;
				error.value = null;

				const response = await fetch(
					`${import.meta.env.VITE_API_BASE_URL}/api/comments/${commentId}`,
					{
						method: "DELETE",
						headers: {
							Authorization: `Bearer ${auth.accessToken}`,
						},
					}
				);

				if (!response.ok) {
					const errorData = await response.json();
					throw new Error(errorData.message || "Unknown error");
				}
			} catch (err) {
				error.value = err.message || "Unknown error";
				throw err;
			} finally {
				loading.value = false;
			}
		};

		return await apiCall();
	};

	return {
		loading,
		error,
		createComment,
		updateComment,
		deleteComment,
	};
});
