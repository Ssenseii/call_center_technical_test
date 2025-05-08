import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";
import HomeView from "../views/HomeView.vue";
import Login from "../views/auth/Login.vue";
import Register from "../views/auth/Register.vue";
import CallVue from "../views/CallVue.vue";
import TicketVue from "../views/TicketVue.vue";

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: "/",
			name: "home",
			component: HomeView,
			meta: { requiresAuth: true },
		},
		{
			path: "/calls",
			name: "callvue",
			component: CallVue,
			meta: { requiresAuth: true },
		},
		{
			path: "/tickets",
			name: "tickets",
			component: TicketVue,
			meta: { requiresAuth: true },
		},
		{
			path: "/register",
			name: "register",
			component: Register,
			meta: { requiresGuest: true },
		},
		{
			path: "/login",
			name: "login",
			component: Login,
			meta: { requiresGuest: true },
		},
	],
});

router.beforeEach((to, from, next) => {
	const authStore = useAuthStore();

	if (to.meta.requiresAuth && !authStore.isAuthenticated()) {
		next({ name: "login" });
	} else if (to.meta.requiresGuest && authStore.isAuthenticated()) {
		next({ name: "home" });
	} else {
		next();
	}
});

export default router;
